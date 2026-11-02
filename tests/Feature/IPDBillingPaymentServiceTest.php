<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Receipt;
use App\Services\InvoiceService;
use App\Services\IPDBillingService;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class IPDBillingPaymentServiceTest extends TestCase
{
    private IPDBillingService $service;

    private string $ipdId;

    private string $invoiceId;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('ipd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ipd_number');
            $table->timestamps();
        });

        Schema::create('invoice', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id')->nullable();
            $table->string('currency', 10)->nullable();
            $table->float('collected_amount')->default(0);
            $table->float('balanced_amount')->default(0);
            $table->string('ipd_billing_status')->nullable();
            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('receipt_number')->unique();
            $table->uuid('invoice_id')->nullable();
            $table->float('amount', 10, 2);
            $table->string('currency', 10)->nullable();
            $table->dateTime('date');
            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ipd_invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('invoice_id');
            $table->float('amount')->default(0);
            $table->float('tax_amount')->default(0);
            $table->float('total_amount')->default(0);
            $table->string('service_category')->nullable();
            $table->date('service_date')->nullable();
            $table->timestamps();
        });

        $this->ipdId = (string) Str::uuid();
        $this->invoiceId = (string) Str::uuid();
        DB::table('ipd')->insert([
            'id' => $this->ipdId,
            'ipd_number' => 'IPD0001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('invoice')->insert([
            'id' => $this->invoiceId,
            'ipd_id' => $this->ipdId,
            'currency' => 'INR',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('ipd_invoice_items')->insert([
            'id' => (string) Str::uuid(),
            'ipd_id' => $this->ipdId,
            'invoice_id' => $this->invoiceId,
            'amount' => 1000,
            'tax_amount' => 0,
            'total_amount' => 1000,
            'service_category' => 'Ward',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->service = new IPDBillingService(
            Mockery::mock(InvoiceService::class),
            Mockery::mock(PaymentService::class)
        );
    }

    public function test_it_creates_a_payment_and_returns_the_receipt(): void
    {
        $receipt = $this->service->addPayment(new Request([
            'amount' => 500,
            'currency' => 'INR',
            'date' => '2026-11-02',
            'payment_type' => 'Cash',
            'notes' => 'Advance payment',
        ]), $this->ipdId);

        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame($this->invoiceId, $receipt->invoice_id);
        $this->assertEquals(500, $receipt->amount);
        $this->assertDatabaseHas('receipts', ['id' => $receipt->id, 'amount' => 500]);

        $invoice = Invoice::findOrFail($this->invoiceId);
        $this->assertEquals(500, $invoice->collected_amount);
        $this->assertEquals(500, $invoice->balanced_amount);
    }

    public function test_it_partially_edits_an_existing_payment_and_recalculates_totals(): void
    {
        $receipt = $this->service->addPayment(new Request([
            'amount' => 400,
            'payment_type' => 'Cash',
        ]), $this->ipdId);

        $updated = $this->service->addPayment(new Request([
            'receipt_id' => $receipt->id,
            'amount' => 750,
            'notes' => 'Corrected payment',
        ]), $this->ipdId);

        $this->assertSame($receipt->id, $updated->id);
        $this->assertEquals(750, $updated->amount);
        $this->assertSame('Cash', $updated->payment_type);
        $this->assertSame('Corrected payment', $updated->notes);

        $invoice = Invoice::findOrFail($this->invoiceId);
        $this->assertEquals(750, $invoice->collected_amount);
        $this->assertEquals(250, $invoice->balanced_amount);
    }

    public function test_it_requires_a_payment_field_when_editing(): void
    {
        $receipt = $this->service->addPayment(new Request(['amount' => 400]), $this->ipdId);
        $this->expectException(ValidationException::class);

        $this->service->addPayment(new Request(['receipt_id' => $receipt->id]), $this->ipdId);
    }

    public function test_it_cannot_edit_a_receipt_from_another_ipd(): void
    {
        $otherIpdId = (string) Str::uuid();
        $otherInvoiceId = (string) Str::uuid();
        DB::table('ipd')->insert([
            'id' => $otherIpdId,
            'ipd_number' => 'IPD0002',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('invoice')->insert([
            'id' => $otherInvoiceId,
            'ipd_id' => $otherIpdId,
            'currency' => 'INR',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherReceipt = Receipt::create([
            'invoice_id' => $otherInvoiceId,
            'amount' => 100,
            'date' => now(),
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->service->addPayment(new Request([
            'receipt_id' => $otherReceipt->id,
            'amount' => 200,
        ]), $this->ipdId);
    }
}

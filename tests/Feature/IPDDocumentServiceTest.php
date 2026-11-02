<?php

namespace Tests\Feature;

use App\Models\IpdDocument;
use App\Services\IPDDocumentService;
use DomainException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class IPDDocumentServiceTest extends TestCase
{
    private IPDDocumentService $service;

    private string $ipdId;

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

        Schema::create('ipd_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id')->nullable();
            $table->string('document_type');
            $table->string('document_path');
            $table->string('document_source', 20)->default('generated');
            $table->timestamps();
            $table->foreign('ipd_id')->references('id')->on('ipd')->cascadeOnDelete();
        });

        $this->ipdId = (string) Str::uuid();
        DB::table('ipd')->insert([
            'id' => $this->ipdId,
            'ipd_number' => 'IPD0001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::fake('public');
        $this->service = app(IPDDocumentService::class);
    }

    public function test_it_uploads_and_lists_multiple_documents_by_type(): void
    {
        $request = $this->uploadRequest('supportive_document', [
            UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->image('photo.jpg'),
        ]);

        $documents = $this->service->create($request);

        $this->assertCount(2, $documents);
        foreach ($documents as $document) {
            $this->assertSame('supportive_document', $document['document_type']);
            $this->assertSame(IpdDocument::SOURCE_UPLOADED, $document['document_source']);
            $this->assertStringStartsWith('pdfs/ipd/IPD0001/others/', $document['document_path']);
            Storage::disk('public')->assertExists($document['document_path']);
        }

        IpdDocument::create([
            'ipd_id' => $this->ipdId,
            'document_type' => 'doctor_notes',
            'document_path' => 'pdfs/ipd/IPD0001/doctor_notes.pdf',
            'document_source' => IpdDocument::SOURCE_GENERATED,
        ]);

        $filterRequest = Request::create('/api/ipd_document_list/'.$this->ipdId, 'GET', [
            'document_type' => 'supportive_document',
        ]);
        $filtered = $this->service->all($this->ipdId, $filterRequest);

        $this->assertCount(2, $filtered);
        $this->assertSame(['supportive_document'], array_values(array_unique(array_column($filtered, 'document_type'))));
    }

    public function test_it_replaces_and_deletes_an_uploaded_document(): void
    {
        $document = $this->service->create($this->uploadRequest('lab_report', [
            UploadedFile::fake()->create('old.pdf', 100, 'application/pdf'),
        ]))[0];

        $updateRequest = Request::create('/api/ipd_document_update/'.$document['id'], 'POST', [
            'document_type' => 'supportive_document',
        ]);
        $updateRequest->files->set(
            'document',
            UploadedFile::fake()->create('replacement.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        );

        $updated = $this->service->update($document['id'], $updateRequest);

        $this->assertSame('supportive_document', $updated['document_type']);
        $this->assertNotSame($document['document_path'], $updated['document_path']);
        Storage::disk('public')->assertMissing($document['document_path']);
        Storage::disk('public')->assertExists($updated['document_path']);

        $this->service->delete($document['id']);

        $this->assertDatabaseMissing('ipd_documents', ['id' => $document['id']]);
        Storage::disk('public')->assertMissing($updated['document_path']);
    }

    public function test_it_rejects_updates_and_deletes_for_generated_documents(): void
    {
        $generated = IpdDocument::create([
            'ipd_id' => $this->ipdId,
            'document_type' => 'discharge_summary',
            'document_path' => 'pdfs/ipd/IPD0001/discharge_summary.pdf',
            'document_source' => IpdDocument::SOURCE_GENERATED,
        ]);

        try {
            $this->service->update($generated->id, new Request(['document_type' => 'other_document']));
            $this->fail('Generated documents must not be updateable.');
        } catch (DomainException) {
            $this->assertDatabaseHas('ipd_documents', ['id' => $generated->id]);
        }

        $this->expectException(DomainException::class);
        $this->service->delete($generated->id);
    }

    public function test_it_rejects_invalid_document_types(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->uploadRequest('Invalid Document Type', [
            UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf'),
        ]));
    }

    public function test_it_rejects_unsupported_and_oversized_files(): void
    {
        foreach ([
            UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'),
            UploadedFile::fake()->create('large.pdf', 15361, 'application/pdf'),
        ] as $file) {
            try {
                $this->service->create($this->uploadRequest('supportive_document', [$file]));
                $this->fail('Invalid document files must be rejected.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('documents.0', $exception->errors());
            }
        }
    }

    public function test_it_rejects_an_unknown_ipd_id(): void
    {
        $this->ipdId = (string) Str::uuid();
        $this->expectException(ValidationException::class);

        $this->service->create($this->uploadRequest('supportive_document', [
            UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf'),
        ]));
    }

    private function uploadRequest(string $documentType, array $files): Request
    {
        $request = Request::create('/api/ipd_document_add', 'POST', [
            'ipd_id' => $this->ipdId,
            'document_type' => $documentType,
        ]);
        $request->files->set('documents', $files);

        return $request;
    }
}

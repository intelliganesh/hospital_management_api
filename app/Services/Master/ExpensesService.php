<?php

namespace App\Services\Master;

use Illuminate\Http\Request;
use App\Models\Master\Expenses;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use AutoIdGenerate;
use App\Enums\ServiceType;
use App\Models\SystemSettings;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

use App\Traits\ExpensesValidation;


class ExpensesService implements CRUDContract, FilterContract
{
    use ExpensesValidation;

    private $filter;
    private $columns;
    private $expensesService;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Master\Expenses $expensesService
     */
    public function __construct(CheckValidation $checkValidationService, Expenses $expensesService)
    {
        $this->filter = Expenses::$filter;
        $this->columns = Expenses::$columns;
        $this->expensesService = $expensesService;
        $this->checkValidationService = $checkValidationService;

    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filter as $column) {
            if (!empty($request[$column])) {
                $data->where($column, $request[$column]);
            }
        }
        return $data;
    }

    /**
     * @deprecated this function is not in use
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated this function is not in use
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * @deprecated message
     */
    public function create(Request $request): void
    {

    }

    /**
     * Summary of createExpenses
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create  expenses  record within a secure transaction')]
    public function createExpenses(Request $request)
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        if(isset($request->generate_voucher_number) && $request->generate_voucher_number){
            $request->merge(['voucher_number' => AutoIdGenerate::generateId(ServiceType::Voucher)]);
        }
        return Expenses::create($request->all())->id;
    }

    /**
     * @deprecated message
     */
    public function update(Request $request, string|null $id): void
    {

    }


    /**
     * Summary of updateExpenses
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return string|null
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  expenses  record within a secure transaction')]
    public function updateExpenses(Request $request, string|null $id): mixed
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Expenses::findOrFail($id)->update($request->all());
        return $id;
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        Expenses::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Expenses
     */
    public function get(string $id): Expenses
    {
        return Expenses::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $expenses = Expenses::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $expenses = $this->search($searchValue, $expenses);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $expenses = $expenses->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $expenses = $this->filterMultipleFields($request->multiple_filter, $expenses);
        }

        return $expenses->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Generate and download a voucher for an expense
     * 
     * @param string $id
     * @return string URL to download the generated PDF
     */
    public function getVoucherDownload(string $id)
    {
        $html = view('templates.downloads.expense-voucher', $this->voucherData($id))->render();
        // Define PDF filename
        $fileName = 'expense_voucher_' . $id . '_' . time() . '.pdf';
        $filePath = storage_path("app/public/pdfs/{$fileName}");

        // Ensure the directory exists
        if (!Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        // Generate and save the PDF using Browsershot
        Browsershot::html($html)
            ->format('A4')
            ->margins(5, 5, 5, 5)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->emulateMedia('screen')
            ->setOption('printBackground', true)
            ->savePdf($filePath);

        // Return the public URL
        return asset("storage/pdfs/{$fileName}");
    }

    /**
     * Prepare data for the voucher template
     * 
     * @param string $id
     * @return array
     */
    private function voucherData(string $id)
    {
        $expense = Expenses::findOrFail($id);
        $auth = Auth::user();
        $user = User::where('id', $auth->id)->first();
        $systemSettings = SystemSettings::where('id', $user->system_settings_id)->first();
        $system = SystemSettings::where('id', $user->system_settings_id)->first();
        $data= [
            'expense' => $expense,
            'user' => $user,
            'system_settings' => $systemSettings,
            'current_date' => now()->format('Y-m-d'),
            'hospital_name' => $systemSettings->hospital_name ?? 'Hospital Management System',
            'hospital_address' => $systemSettings->address ?? '',
            'hospital_phone' => $systemSettings->phone ?? '',
            'hospital_email' => $systemSettings->email ?? '',
            'hospital_logo' => $systemSettings->logo ? asset('storage/' . $systemSettings->logo) : asset('logo.png'),
        ];
        if (! empty($systemSettings)) {
            $data['letter_header_address'] = $systemSettings->letter_header;
            $data['billing_letter_header'] = $systemSettings->billing_letter_header;
        }

        return $data;
    }
}
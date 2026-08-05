<?php

namespace App\Services\Reports;

use App\Exports\Expense;
use Illuminate\Http\Request;
use App\Models\Master\Expenses;
use App\Services\CheckValidation;
use Rap2hpoutre\FastExcel\FastExcel;
// use Maatwebsite\Excel\Facades\Excel;
// use App\Models\Reports\ExpensesReports;



class ExpensesReportsService
{

    private $filter = [
        'id',
        "date",
        "voucher_number",
        "entered_name",
        "for_name",
        // "proof",
        "amount",
        "expense_name",
        "mode_of_payment",
    ];

    private $columns = [
        'id',
        "date",
        "voucher_number",
        "entered_name",
        "for_name",
        // "proof",
        "amount",
        "expense_name",
        "mode_of_payment",
    ];
    private $checkValidationService;
    private $expensesReportsService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        // $this->filter = Expenses::$filter;
        // $this->columns = Expenses::$columns;
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

    public function all(?Request $request): mixed
    {
        $expensesReports = Expenses::query();
        if ($request->has("from_date") && $request->has("to_date")) {
            $expensesReports = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $expensesReports);
        }
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $expensesReports = $this->search($searchValue, $expensesReports);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $expensesReports = $expensesReports->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $expensesReports = $this->filterMultipleFields($request->multiple_filter, $expensesReports);
        }

        $totalExpenses = $expensesReports->sum('amount');

        return ['totalExpenses' => $totalExpenses, 'table' => $expensesReports->paginate(env('PAGINATION', 25))];
    }


    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     */
    public function filterByDateRange(string $searchText, $data)
    {
        $dates = explode("|", $searchText);
        $start = \Carbon\Carbon::parse($dates[0])->startOfDay();
        $end = \Carbon\Carbon::parse($dates[1])->endOfDay();
        $data->whereBetween('created_at', [$start, $end]);
        return $data;
    }


    /**
     * Summary of downloadExcel
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExcel(Request $request)
    {
        $expensesReports = Expenses::query();
        if ($request->has("from_date") && $request->has("to_date")) {
            $expensesReports = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $expensesReports);
        }
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $expensesReports = $this->search($searchValue, $expensesReports);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $expensesReports = $expensesReports->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $expensesReports = $this->filterMultipleFields($request->multiple_filter, $expensesReports);
        }

        $fileName = 'expenses_report_' . time() . '.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        // Create Excel file
        // return Excel::download(new Expense($data), 'orders-products-data.xlsx');
        // (new FastExcel(collect($expensesReports)))->export($tempPath);
        // Create a custom export with proper number formatting
        $data = $expensesReports->get()->map(function ($row) {
            // Make sure amount is properly formatted as a number
            // Remove any leading zeros, spaces, or apostrophes that might cause Excel to treat it as text
            $amount = 0;
            if (is_numeric($row->amount)) {
                // Convert to float and format without any leading apostrophe
                $amount = (float)$row->amount;
            }
            
            return [
                'Date' => $row->date ?? '',
                'Voucher Number' => $row->voucher_number ?? '',
                'Expense Name' => $row->expense_name ?? '',
                'Amount' => $amount, // This will be a pure numeric value
                'Description' => $row->description ?? '',
                'Mode of Payment' => $row->mode_of_payment ?? '',
            ];
        });
        
        // Export the mapped data
        (new FastExcel($data))->export($tempPath);

        // Return downloadable file
        return response()->download($tempPath)->deleteFileAfterSend(true);

    }

}
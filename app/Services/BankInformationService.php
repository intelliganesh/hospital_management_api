<?php

namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\BankInformation;
use App\Services\CheckValidation;
use App\Traits\BankInformationValidation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BankInformationService implements CRUDContract, FilterContract
{
    use BankInformationValidation;

    private $columns;
    private $filtersColumn;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = BankInformation::$columns;
        $this->filtersColumn = BankInformation::$filtersColumn;
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
        foreach ($this->filtersColumn as $column) {
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
     * Get all bank information with pagination, filtering, and sorting
     */
    public function all(?Request $request = null): LengthAwarePaginator
    {
        $query = BankInformation::query();

        if ($request) {
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'id');
            $sortOrder = $request->input('sort_order', 'asc');

            if (!empty($search)) {
                $query = $this->search($search, $query);
            }

            $filters = $request->all();
            if (!empty($filters)) {
                $query = $this->filterMultipleFields($filters, $query);
            }

            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate(env('PAGINATION', 15));
    }

    /**
     * Get a single bank information by ID
     */
    public function get(string $id): BankInformation
    {
        return BankInformation::findOrFail($id);
    }

    /**
     * Create a new bank information record
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create bank information record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        BankInformation::create($request->all());
    }

    /**
     * Update a bank information record
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update bank information record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        BankInformation::findOrFail($id)->update($request->all());
    }

    /**
     * Partially update a bank information record (PATCH)
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Partially update bank information record within a secure transaction')]
    public function partialUpdate(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        BankInformation::findOrFail($id)->update($request->all());
    }

    /**
     * Delete a bank information record
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Delete bank information record within a secure transaction')]
    public function delete(string $id): void
    {
        BankInformation::findOrFail($id)->delete();
    }

    /**
     * Summary of bankInformationDropdownList
     * @return \Illuminate\Database\Eloquent\Collection<int, BankInformation>
     */
    public function bankInformationDropdownList()
    {
        return BankInformation::select('id', 'title','details')->get();
    }



}

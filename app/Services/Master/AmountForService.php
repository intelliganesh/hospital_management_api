<?php

namespace App\Services\Master;

use Illuminate\Http\Request;
use App\Enums\AmountForEnums;
use App\Contracts\CRUDContract;
use App\Models\Master\AmountFor;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;

use App\Traits\AmountForValidation;


class AmountForService implements CRUDContract, FilterContract
{
    use AmountForValidation;


    private $columns;
    private $amountForService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Models\Master\AmountFor $amountForService
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, AmountFor $amountForService)
    {
        $this->columns = AmountFor::$columns;
        $this->amountForService = $amountForService;
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
        foreach ($this->columns as $column) {
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
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create  amountFor  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        AmountFor::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  amountFor  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        AmountFor::findOrFail($id)->update($request->all());
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
        AmountFor::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return AmountFor
     */
    public function get(string $id): AmountFor
    {
        return AmountFor::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $amountFor = AmountFor::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $amountFor = $this->search($searchValue, $amountFor);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'amount_for';
            $sortOrder = $request->sort_order ?? 'desc';
            $amountFor = $amountFor->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $amountFor = $this->filterMultipleFields($request->multiple_filter, $amountFor);
        }

        return $amountFor->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of amountForList
     * @return array
     */
    public function listForDropdown(): array
    {
        return AmountFor::where('status', AmountForEnums::Active->value)->pluck('amount_for', 'id')->toArray();
    }

}
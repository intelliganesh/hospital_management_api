<?php

namespace App\Services\Master;

use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Services\CheckValidation;
use App\Models\Master\BillingServiceCategory;
use App\Traits\BillingServiceCategoryValidation;

class BillingServiceCategoryService implements CRUDContract, FilterContract
{
    use BillingServiceCategoryValidation;

    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = BillingServiceCategory::$columns;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('category_name', 'like', '%' . $searchText . '%')
            ->orWhere('status', 'like', '%' . $searchText . '%');
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['category_name']) && $request['category_name'] != null && $request['category_name'] != '') {
            $data->where('category_name', $request['category_name']);
        }

        if (isset($request['status']) && $request['status'] != null && $request['status'] != '') {
            $data->where('status', $request['status']);
        }

        return $data;
    }

    /**
     * @deprecated this method is not used
     */
    public function filterByDateRange(string $searchText, $data)
    {
        // code here
    }

    /**
     * @deprecated this method is not used
     */
    public function sortData(string $searchText, $data)
    {
        // code here
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        BillingServiceCategory::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $billingServiceCategory = BillingServiceCategory::findOrFail($id);
        $billingServiceCategory->where('id', $id)->update($request->all());
    }

    /**
     * @deprecated this method is not used
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $billingServiceCategory = BillingServiceCategory::findOrFail($id);
        $billingServiceCategory->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return BillingServiceCategory
     */
    public function get(string $id): BillingServiceCategory
    {
        $billingServiceCategory = BillingServiceCategory::findOrFail($id);
        return $billingServiceCategory;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $billingServiceCategory = BillingServiceCategory::query();

        if ($request->has('search')) {
            $searchValue = $request->search;
            $billingServiceCategory = $this->search($searchValue, $billingServiceCategory);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'id';
            $sortOrder = $request->sort_order ?? 'desc';
            $billingServiceCategory = $billingServiceCategory->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $billingServiceCategory = $this->filterMultipleFields($request->multiple_filter, $billingServiceCategory);
        }

        return $billingServiceCategory->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    public function billingServiceCategoryList()
    {
        return BillingServiceCategory::select('id', 'category_name')->where('status', 'Active')->get();
    }
}

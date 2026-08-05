<?php

namespace App\Services\Master;

use AutoIdGenerate;
use App\Enums\ServiceType;
use Illuminate\Http\Request;
use App\Models\Master\Findings;
use App\Contracts\CRUDContract;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\FindingsValidation;

class FindingsService implements CRUDContract, FilterContract
{

    use FindingsValidation;
    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = Findings::$columns;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('findings_number', 'like', '%' . $searchText . '%')->orWhere('finding_name', 'like', '%' . $searchText . '%')->orWhere('category', 'like', '%' . $searchText . '%')->orWhere('status', 'like', '%' . $searchText . '%');
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['findings_number']) && $request['findings_number'] != null && $request['findings_number'] != '') {
            $data->where('findings_number', $request['findings_number']);
        }
        if (isset($request['finding_name']) && $request['finding_name'] != null && $request['finding_name'] != '') {
            $data->where('finding_name', $request['finding_name']);
        }
        if (isset($request['category']) && $request['category'] != null && $request['category'] != '') {
            $data->where('category', $request['category']);
        }
        if (isset($request['status']) && $request['status'] != null && $request['status'] != '') {
            $data->where('status', $request['status']);
        }
        return $data;
    }

    /**
     *  @deprecated this method is not used 
     */
    public function filterByDateRange(string $searchText, $data)
    {
        //code here
    }

    /**
     *  @deprecated this method is not used 
     */
    public function sortData(string $searchText, $data)
    {
        //code here
    }


    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Findings::create(array_merge($request->all(), ['findings_number' => AutoIdGenerate::generateId(ServiceType::Findings)]));
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
        $findings = Findings::findOrFail($id);
        $findings->where('id', $id)->update($request->all());
    }

    /**
     * @deprecated this method is not used 
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
        $findings = Findings::findOrFail($id);
        $findings->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Findings
     */
    public function get(string $id): Findings
    {
        $findings = Findings::findOrFail($id);
        return $findings;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {

        $findings = Findings::query();

        if ($request->has('search')) {
            $searchValue = $request->search;
            $findings = $this->search($searchValue, $findings);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'findings_number';
            $sortOrder = $request->sort_order ?? 'desc';
            $findings = $findings->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $findings = $this->filterMultipleFields($request->multiple_filter, $findings);
        }

        return $findings->select($this->columns)->paginate(env('PAGINATION', 25));

    }

    public function findingsList()
    {
        return Findings::select('id', 'finding_name')->get();
    }

}
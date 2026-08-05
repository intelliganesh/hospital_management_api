<?php

namespace App\Services\Master;

use App\Enums\AvasthaEnum;
use Illuminate\Http\Request;
use App\Models\Master\Avastha;
use App\Contracts\CRUDContract;
use App\Traits\AvasthaValidation;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;

class AvasthaService implements CRUDContract, FilterContract
{
    use AvasthaValidation;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('name', 'like', '%' . $searchText . '%');
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['name']) && $request['name'] != null && $request['name'] != '') {
            $data = $data->where('name', 'like', '%' . $request['name'] . '%');
        }
        return $data;
    }

    /**
     * @deprecated this method is not used
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated this method is not used
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Avastha::create($request->all());
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
        $avastha = Avastha::findOrFail($id);
        $avastha->update($request->all());
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
        $avastha = Avastha::findOrFail($id);
        $avastha->delete();
    }


    /**
     * Summary of get
     * @param string $id
     * @return Avastha|null
     */
    public function get(string $id): Avastha
    {
        return Avastha::find($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $avasthas = Avastha::query();

        if ($request->has('search')) {
            $searchValue = $request->search;
            $avasthas = $this->search($searchValue, $avasthas);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $avasthas = $avasthas->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $avasthas = $this->filterMultipleFields($request->multiple_filter, $avasthas);
        }

        return $avasthas->paginate(env('PAGINATION', 25));

    }

    /**
     * Summary of list
     * @return array
     */
    public function list()
    {
        return AvasthaEnum::options();
    }

}
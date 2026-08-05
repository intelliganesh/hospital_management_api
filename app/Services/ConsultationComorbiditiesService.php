<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\ConsultationComorbidities;

use App\Traits\ConsultationComorbiditiesValidation;


class ConsultationComorbiditiesService implements CRUDContract, FilterContract
{
    use ConsultationComorbiditiesValidation;

    private $columns;
    private $checkValidationService;
    private $consultationComorbiditiesService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, ConsultationComorbidities $consultationComorbiditiesService)
    {
        $this->columns = ConsultationComorbidities::$columns;
        $this->checkValidationService = $checkValidationService;
        $this->consultationComorbiditiesService = $consultationComorbiditiesService;

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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  consultationComorbidities  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        ConsultationComorbidities::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  consultationComorbidities  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        ConsultationComorbidities::findOrFail($id)->update($request->all());
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
        ConsultationComorbidities::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return ConsultationComorbidities
     */
    public function get(string $id): ConsultationComorbidities
    {
        return ConsultationComorbidities::findOrFail($id);
    }


    /**
     * Summary of getByDynamicColumn
     * @param string $id
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Collection<int, ConsultationComorbidities>
     */
    public function getByDynamicColumn(string $id, string $column): mixed
    {
        return ConsultationComorbidities::where($column, $id)->get();
    }

    /**
     * Summary of updateOrCreate
     * @param array $id
     * @param array $data
     * @return void
     */
    public function updateOrCreate(array $id, array $data): void
    {
        $this->checkValidationService->checkValidation($this->validate($data, true, $id['id'] ?? null));
        ConsultationComorbidities::updateOrCreate($id, $data);
    }

    public function all(?Request $request): mixed
    {
        $consultationComorbidities = ConsultationComorbidities::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $consultationComorbidities = $this->search($searchValue, $consultationComorbidities);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $consultationComorbidities = $consultationComorbidities->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $consultationComorbidities = $this->filterMultipleFields($request->multiple_filter, $consultationComorbidities);
        }

        return $consultationComorbidities->select($this->columns)->paginate(env('PAGINATION', 25));
    }

}
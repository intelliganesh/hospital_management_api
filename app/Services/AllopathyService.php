<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Allopathy;

use App\Traits\AllopathyValidation;


class AllopathyService implements CRUDContract, FilterContract
{
    use AllopathyValidation;


    private $columns;
    private $allopathyService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, Allopathy $allopathyService)
    {
        $this->columns = Allopathy::$columns;
        $this->allopathyService = $allopathyService;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  allopathy  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Allopathy::create($request->all());
    }


    /**
     * Summary of createOrUpdate
     * @param mixed $request
     * @param mixed $id
     * @return Allopathy
     */
    public function createOrUpdate($request, $id): Allopathy
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        return Allopathy::updateOrCreate(['consultation_id' => $id], [
            'dre' => $request->dre,
            'tests' => $request->tests,
            'amount' => $request->amount,
            'medicines' => $request->medicines,
            'diet_plan' => $request->diet_plan,
            'yoga_asana' => $request->yoga_asana,
            'proctoscopy' => $request->proctoscopy,
            'food_advice' => $request->food_advice,
            'additional_cost' => $request->Service,
            'co_morbidities' => $request->co_morbidities,
            'on_examination' => $request->on_examination,
            'treatment_plan' => $request->treatment_plan,
            'chief_complaints' => $request->chief_complaints,
            'surgical_history' => $request->surgical_history,
            'dre_induration_at' => $request->dre_induration_at,
            'dre_secondary_position' => $request->dre_secondary_position,
            'proctoscopy_anal_polyp_at' => $request->proctoscopy_anal_polyp_at,
            'proctoscopy_secondary_position' => $request->proctoscopy_secondary_position,
            'previous_scar' => $request->previous_scar,
            'previous_scar_position' => $request->previous_scar_position,
            'abscess' => $request->abscess,
            'abscess_position' => $request->abscess_position,
            'dre_induration_at' => $request->dre_induration_at,
            'managements' => $request->managements,
            'managements_date' => $request->managements_date,
            'combination_medicines' => $request->combination_medicines,
            'diagnosis_summary' => $request->diagnosis_summary,
            'fistula_remark' => $request->fistula_remark,
        ]);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  allopathy  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Allopathy::findOrFail($id)->update($request->all());
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
        Allopathy::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Allopathy
     */
    public function get(string $id): Allopathy
    {
        return Allopathy::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $allopathy = Allopathy::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $allopathy = $this->search($searchValue, $allopathy);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $allopathy = $allopathy->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $allopathy = $this->filterMultipleFields($request->multiple_filter, $allopathy);
        }

        return $allopathy->select($this->columns)->paginate(env('PAGINATION', 25));
    }

}
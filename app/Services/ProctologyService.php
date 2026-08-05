<?php

namespace App\Services;

use App\Models\Proctology;
use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\ProctologyValidation;

class ProctologyService implements CRUDContract, FilterContract
{
    use ProctologyValidation;

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
     * @deprecated this function is not in use
     */
    public function search(string $searchText, $data)
    {
        return $data;
    }


    /**
     * @deprecated this function is not in use
     */
    public function filterMultipleFields($request, $data)
    {
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
     * Summary of createOrUpdate
     * @param mixed $request
     * @param mixed $id
     * @return Proctology
     */
    public function createOrUpdate($request, $id): Proctology
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        return Proctology::updateOrCreate(['consultation_id' => $id], [
            'dre' => $request->dre,
            'tests' => $request->tests,
            'amount' => $request->amount,
            'abscess' => $request->abscess,
            'diet_plan' => $request->diet_plan,
            'medicines' => $request->medicines,
            'combination_medicines' => $request->combination_medicines,
            'yoga_asana' => $request->yoga_asana,
            'sonologist' => $request->sonologist,
            'anal_valve' => $request->anal_valve,
            'proctoscopy' => $request->proctoscopy,
            'managements' => $request->managements,
            'food_advice' => $request->food_advice,
            'additional_cost' => $request->Service,
            'no_of_fistula' => $request->no_of_fistula,
            'fistula_recurrence' => $request->fistula_recurrence,
            'fistula_recurrence_surgery_count'=>$request->fistula_recurrence_surgery_count,
            'fistula_remark' => $request->fistula_remark,
            'previous_scar' => $request->previous_scar,
            'diagnosis_summary'=>$request->diagnosis_summary,
            'co_morbidities' => $request->co_morbidities,
            'on_examination' => $request->on_examination,
            'treatment_plan' => $request->treatment_plan,
            'discount_amount' => $request->discount_amount,
            'chief_complaints' => $request->chief_complaints,
            'abscess_position' => $request->abscess_position,
            'managements_date' => $request->managements_date,
            'surgical_history' => $request->surgical_history,
            'dre_induration_at' => $request->dre_induration_at,
            'secondary_anal_valve' => $request->secondary_anal_valve,
            'consultation_discount' => $request->consultation_discount,
            'dre_secondary_position' => $request->dre_secondary_position,
            'previous_scar_position' => $request->previous_scar_position,
            'type_of_fistula_position' => $request->type_of_fistula_position,
            'type_of_fistula_sphincter' => $request->type_of_fistula_sphincter,
            'posterior_fistulous_angle' => $request->posterior_fistulous_angle,
            'proctoscopy_anal_polyp_at' => $request->proctoscopy_anal_polyp_at,
            'internal_opening_position' => $request->internal_opening_position,
            'internal_opening_distance' =>$request->internal_opening_distance,
            'secondary_opening_position' => $request->secondary_opening_position,
            'co_morbidities_description' => $request->co_morbidities_description,
            'no_of_tracks_in_one_fistula' => $request->no_of_tracks_in_one_fistula,
            'proctoscopy_secondary_position' => $request->proctoscopy_secondary_position,
            'external_opening_position'=>$request->external_opening_position,
            'no_of_external_opening_position'=>$request->no_of_external_opening_position,
            'any_other'=>$request->any_other,
            'no_of_secondary_opening_position'=>$request->no_of_secondary_opening_position,
            'type_of_crypt'=>$request->type_of_crypt,
            'crypt_cause'=>$request->crypt_cause,
            'basis_of_high_low_riding'=>$request->basis_of_high_low_riding,
            'distant_visceral_communication'=>$request->distant_visceral_communication,
            'sono_fistula_gram'=>$request->sono_fistula_gram,
            'mri_fistula_gram'=>$request->mri_fistula_gram,
            'currency'=>$request->currency,
            'sonologist_findings'=>$request->sonologist_findings,
            'other_investigation'=>$request->other_investigation,
        ]);
        // return Proctology::updateOrCreate(['consultation_id' => $id], [
        //     'dre' => $request->dre,
        //     'tests' => $request->tests,
        //     'amount' => $request->amount,
        //     'diet_plan' => $request->diet_plan,
        //     'medicines' => $request->medicines,
        //     'yoga_asana' => $request->yoga_asana,
        //     'proctoscopy' => $request->proctoscopy,
        //     'food_advice' => $request->food_advice,
        //     'additional_cost' => $request->Service,
        //     'co_morbidities' => $request->co_morbidities,
        //     'on_examination' => $request->on_examination,
        //     'treatment_plan' => $request->treatment_plan,
        //     'chief_complaints' => $request->chief_complaints,
        //     'surgical_history' => $request->surgical_history,
        //     'dre_induration_at' => $request->dre_induration_at,
        //     'dre_secondary_position' => $request->dre_secondary_position,
        //     'proctoscopy_anal_polyp_at' => $request->proctoscopy_anal_polyp_at,
        //     'proctoscopy_secondary_position' => $request->proctoscopy_secondary_position,
        // ]);
    }

    /**
     * @deprecated this function is not in use
     */
    public function create(Request $request): void
    {

    }

    /**
     * @deprecated this function is not in use
     */
    public function update(Request $request, string|null $id): void
    {
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
    }

    public function delete(string $id): void
    {
    }

    /**
     * @deprecated this function is not in use
     */
    public function get(string $id): mixed
    {
        return null;
    }

    /**
     * @deprecated this function is not in use
     */
    public function all(?Request $request): mixed
    {
        return null;
        // if ($request->has('search')) {
        //     $searchValue = $request->search;
        // }

        // if ($request->has('sort_by')) {
        //     $sortBy = $request->sort_by ?? '';
        //     $sortOrder = $request->sort_order ?? 'desc';
        // }

        // if ($request->has('multiple_filter')) {
        //     $this->filterMultipleFields($request->multiple_filter, []);
        // }

    }

}
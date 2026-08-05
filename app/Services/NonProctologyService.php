<?php
namespace App\Services;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\NonProctology;
use App\Services\CheckValidation;
use App\Traits\NonProctologyValidation;
use Illuminate\Http\Request;

class NonProctologyService implements CRUDContract, FilterContract
{
    use NonProctologyValidation;
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
     * Summary of createOrUpdate
     * @param mixed $request
     * @param mixed $id
     * @return NonProctology
     */
    public function createOrUpdate($request, $id): NonProctology
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        // $yoga = YogaAsana::where('id', $request->yoga_asana)->first();
        return NonProctology::updateOrCreate([
            'consultation_id' => $id,
            // 'benefits' => $yoga->benefits,
            // 'asana_name' => $yoga->asana_name,
            // 'description' => $yoga->description,
            // 'difficulty_level' => $yoga->difficulty_level,
            // 'contraindications' => $yoga->contraindications,
            // 'recommended_duration' => $yoga->recommended_duration,
        ], [
            'agni'                       => $request->agni,
            'amount'                     => $request->amount,
            'tests'                      => $request->tests,
            'koshta'                     => $request->koshta,
            'avastha'                    => $request->avastha,
            'vikruti'                    => $request->vikruti,
            'prakriti'                   => $request->prakriti,
            'diet_plan'                  => $request->diet_plan,
            'medicines'                  => $request->medicines,
            'diagnosis_summary'=>$request->diagnosis_summary,
            'combination_medicines'      => $request->combination_medicines,
            'yoga_asana'                 => $request->yoga_asana,
            'food_advice'                => $request->food_advice,
            'additional_cost'            => $request->Service,
            'co_morbidities'             => $request->co_morbidities,
            'co_morbidities_description' => $request->co_morbidities_description,
            'on_examination'             => $request->on_examination,
            'treatment_plan'             => $request->treatment_plan,
            // 'additional_cost' => $request->additional_cost,
            'chief_complaints'           => $request->chief_complaints,
            'surgical_history'           => $request->surgical_history,
            'discount_amount'            => $request->discount_amount,
            'consultation_discount'      => $request->consultation_discount,
        ]);
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
     * @deprecated this function is not in use
     */
    public function create(Request $request): void
    {
    }

    /**
     * @deprecated this function is not in use
     */
    public function update(Request $request, string | null $id): void
    {
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        //code here
    }

    /**
     * @deprecated this function is not in use
     */
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
        //  if ($request->has('search')) {
        //      $searchValue = $request->search;
        //  }

        //  if ($request->has('sort_by')) {
        //          $sortBy = $request->sort_by ?? '';
        //          $sortOrder = $request->sort_order ?? 'desc';
        //  }

        //  if ($request->has('multiple_filter')) {
        //      $this->filterMultipleFields($request->multiple_filter, []);
        //  }

    }

}

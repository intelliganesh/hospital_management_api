<?php

namespace App\Services\Master;


use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Contracts\CRUDContract;
use App\Models\Master\Allergies;
use App\Contracts\FilterContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Traits\AllergiesValidation;
class AllergyService implements CRUDContract, FilterContract
{

    use ResponseTrait;
    use AllergiesValidation;

    private $updateOrCreateColumns;

    private $checkValidationService;


    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
        $this->updateOrCreateColumns = Allergies::$updateOrCreateColumns;
    }


    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create allergies record within a secure transaction')]
    public function create(Request $request): void
    {
        $validator = $this->validate($request);
        $this->checkValidationService->checkValidation($validator);
        Allergies::create($request->only($this->updateOrCreateColumns));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update Allergies record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true,$id));
        $appointment = Allergies::findOrFail($id);
        $appointment->update($request->only($this->updateOrCreateColumns));
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // write code here.
    }

    /**
     * Summary of delete
     * @param string $id
     * @return bool
     */
    public function delete(string $id): void
    {
        $appointment = Allergies::findOrFail($id);
        $appointment->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Allergies|null
     */
    public function get(string $id): mixed
    {
        $appointment = Allergies::findOrFail($id);
        return $appointment;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return \Illuminate\Database\Eloquent\Collection<int, Allergies>
     */
    public function all(?Request $request): mixed
    {
        $appointment = Allergies::query();
        if ($request->has('search')) {
            $appointment = $this->search($request->input('search'), $appointment);
        }
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'allergen_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $appointment = $appointment->orderBy($sortBy, $sortOrder);
        }
        return $appointment->select('id', 'allergen_name', 'allergen_type', 'other_allergen_type')->paginate(env('PAGINATION', 25));
    }



    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('allergen_name', 'like', '%' . $searchText . '%')
            ->orWhere('allergen_type', 'like', '%' . $searchText . '%')
            ->orWhere('other_allergen_type', 'like', '%' . $searchText . '%');
    }


    /**
     * @deprecated This method is not used.
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated This method is not used.
     * 
     */
    public function sortData(string $searchText, $data)
    {
        // write code here
    }

}
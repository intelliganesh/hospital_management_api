<?php


namespace App\Services\Master;

use Throwable;
use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Services\CheckValidation;
use App\Models\Master\MedicineCategories;
use App\Traits\MedicineCategoriesValidation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MedicineCategoriesService implements CRUDContract, FilterContract
{
    use MedicineCategoriesValidation;

    /**
     * Summary of checkValidationService
     * @var 
     */
    protected $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return MedicineCategories
     */
    public function get(string $id): MedicineCategories|Throwable
    {
        $medicineCategories = MedicineCategories::findOrFail($id);
        if (!$medicineCategories) {
            throw new NotFoundHttpException('Data not found.');
        }
        return $medicineCategories;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request = null): mixed
    {
        $medicineCategories = MedicineCategories::query();
        if ($request && $request->has('search')) {
            $searchValue = $request->search;
            $medicineCategories = $this->search($searchValue, $medicineCategories);
        }
        if ($request && $request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'medicine_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $medicineCategories = $medicineCategories->orderBy($sortBy, $sortOrder);
        }
        return $medicineCategories->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $validate = $this->validate($request, true);
        $this->checkValidationService->checkValidation($validate);
        MedicineCategories::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, ?string $id): void
    {
        $validate = $this->validate($request, true,$id);
        $this->checkValidationService->checkValidation($validate);
        $medicineCategories = MedicineCategories::findOrFail($id);
        if (!$medicineCategories) {
            throw new NotFoundHttpException('Data not found.');
        }
        $medicineCategories->update($request->all());
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $medicineCategories = MedicineCategories::findOrFail($id);
        if (!$medicineCategories) {
            throw new NotFoundHttpException('Data not found.');
        }
        $medicineCategories->delete();
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {

    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        $data->where(function ($query) use ($searchText) {
            $query->where('category_name', 'like', '%' . $searchText . '%');
        });
        return $data;
    }
    /**
     * @deprecated message
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }
    /**
     * @deprecated message
     */
    public function sortData(string $searchText, $data)
    {
    }


    /**
     * Summary of getAllMedicineCategoriesList
     * @return \Illuminate\Database\Eloquent\Collection<int, MedicineCategories>
     */
    public function getAllMedicineCategoriesList()
    {
        return MedicineCategories::select('id', 'category_name')->get();
    }
}
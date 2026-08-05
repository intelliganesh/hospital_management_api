<?php

namespace App\Services;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use Illuminate\Http\Request;
use App\Models\MedicineCategoryMapping;
use App\Services\Master\MedicinesService;
use App\Traits\MedicineCategoryMappingValidation;
use App\Services\Master\MedicineCategoriesService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MedicineCategoryMappingService implements CRUDContract, FilterContract
{

    use MedicineCategoryMappingValidation;

    /**
     * Summary of medicineService
     * @var 
     */
    protected $medicineService;
    /**
     * Summary of checkValidationService
     * @var 
     */
    protected $checkValidationService;
    /**
     * Summary of medicineCategoriesService
     * @var 
     */
    protected $medicineCategoriesService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\MedicinesService $medicineService
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\Master\MedicineCategoriesService $medicineCategoriesService
     */
    public function __construct(MedicinesService $medicineService, CheckValidation $checkValidationService, MedicineCategoriesService $medicineCategoriesService)
    {
        $this->medicineService = $medicineService;
        $this->checkValidationService = $checkValidationService;
        $this->medicineCategoriesService = $medicineCategoriesService;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create  medicine_category_mapping  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        MedicineCategoryMapping::create($request->all());
    }


    /**
     * Summary of getAllMedicineCategoryAndMedicineList
     * @return array{medicine: \App\Models\Master\Medicines, medicineCategory: \App\Models\Master\MedicineCategories}
     */
    public function getAllMedicineCategoryAndMedicineList()
    {
        return [
            'medicine' => $this->medicineService->getAllMedicineList(),
            'medicine_category' => $this->medicineCategoriesService->getAllMedicineCategoriesList(),
        ];
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return MedicineCategoryMapping
     */
    public function get(string $id): MedicineCategoryMapping
    {
        $medicineCategoryMapping = MedicineCategoryMapping::findOrFail($id);
        if (!$medicineCategoryMapping) {
            throw new NotFoundHttpException('Data not found.');
        }
        return $medicineCategoryMapping;
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $medicineCategoryMapping = MedicineCategoryMapping::findOrFail($request->id);
        if (!$medicineCategoryMapping) {
            throw new NotFoundHttpException('Data not found.');
        }
        $medicineCategoryMapping->update($request->all());
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(?Request $request = null): mixed
    {
        $medicineCategoryMapping = MedicineCategoryMapping::query();
        if ($request && $request->has('search')) {
            $searchValue = $request->search;
            $medicineCategoryMapping = $this->search($searchValue, $medicineCategoryMapping);
        }
        if ($request && $request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'medicine_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $medicineCategoryMapping = $medicineCategoryMapping->orderBy($sortBy, $sortOrder);
        }
        $paginated = $medicineCategoryMapping->with('medicine', 'category')->paginate(env('PAGINATION', 25));

        $transformed = $paginated->setCollection(
            $paginated->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'medicine' => $item->medicine,
                    'category' => $item->category,
                ];
            })
        );

        return $transformed;
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // write code
    }

    public function delete(string $id): void
    {
        MedicineCategoryMapping::findOrFail($id)->delete();
    }

    /**
     * Summary of search
     * @param mixed $searchValue
     * @param mixed $query
     */
    public function search(string $searchValue, $query)
    {
        $query->where(function ($query) use ($searchValue) {
            $query->whereHas('medicine', function ($query) use ($searchValue) {
                $query->where('medicine_name', 'like', '%' . $searchValue . '%');
            });
            $query->orWhereHas('category', function ($query) use ($searchValue) {
                $query->where('category_name', 'like', '%' . $searchValue . '%');
            });
        });
        return $query;
    }


    /**
     * @deprecated filterByDateRange is not in use
     */
    public function filterByDateRange(string $searchText, $data)
    {
        return [];
    }

    /**
     * @deprecated sortData is not in use
     */
    public function sortData(string $searchText, $data)
    {
        return [];
    }


}
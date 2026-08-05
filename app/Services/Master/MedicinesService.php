<?php
namespace App\Services\Master;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Enums\Consultation\TypeEnum;
use App\Models\Master\Medicines;
use App\Services\CheckValidation;
use App\Traits\MedicinesValidation;
use DepartmentTypeData;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class MedicinesService implements CRUDContract, FilterContract
{

    use MedicinesValidation;

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
     * @return Medicines
     */
    public function get(string $id): Medicines | Throwable
    {
        $mediciens = Medicines::findOrFail($id);
        if (! $mediciens) {
            throw new NotFoundHttpException('Data not found.');
        }
        return $mediciens;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $medicines = Medicines::query();
        if ($request?->has('search')) {
            $medicines = $this->search($request->input('search'), $medicines);
        }
        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? 'medicine_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $medicines = $medicines->orderBy($sortBy, $sortOrder);
        }
        return $medicines->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of allMedicines
     * @param mixed $columnsName
     * @param mixed $columnsValue
     * @return \Illuminate\Database\Eloquent\Collection<int, Medicines>
     */
    public function allMedicines(array | string $selectColumns, string $columnsName, bool $columnsValue): mixed
    {
        return Medicines::select($selectColumns)->where($columnsName, $columnsValue)->get();
    }

    /**
     * Summary of relatedMedicinesByIds
     * @param array|string $selectColumns
     * @param string $ids
     * @param bool $columnsValue
     * @return array|\Illuminate\Database\Eloquent\Collection<int, Medicines>
     */
    public function relatedMedicinesByIds(array | string $selectColumns, string | null $ids, ?bool $mediciensStatus = true)
    {
        if ($ids == null) {
            return [];
        }

        $ids = explode(',', $ids);
        return Medicines::select($selectColumns)->whereIn('id', $ids)->where('is_active', $mediciensStatus)->get();
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
        Medicines::create($request->all());
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
        $validate = $this->validate($request, true, $id);
        $this->checkValidationService->checkValidation($validate);
        $mediciens = Medicines::findOrFail($id);
        if (! $mediciens) {
            throw new NotFoundHttpException('Data not found.');
        }
        $mediciens->update($request->all());
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $mediciens = Medicines::findOrFail($id);
        if (! $mediciens) {
            throw new NotFoundHttpException('Data not found.');
        }
        $mediciens->delete();
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
    {

    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        $data->where('medicine_name', 'like', '%' . $searchText . '%')
            ->orWhere('generic_name', 'like', '%' . $searchText . '%')
            ->orWhere('dosage_form', 'like', '%' . $searchText . '%')
            ->orWhere('manufacturer', 'like', '%' . $searchText . '%')
            ->orWhere('expiry_date', 'like', '%' . $searchText . '%')
            ->orWhere('unit_price', 'like', '%' . $searchText . '%')
            ->orWhere('strength', 'like', '%' . $searchText . '%');
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
     * Summary of getMedicineById
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Medicines|null
     */
    public function getMedicineById(string $id, array $select = ['*']): Medicines
    {
        $ids       = explode(',', $id);
        $medicines = Medicines::select($select);
        $query     = count($ids) > 1
            ? $medicines->whereIn('id', $ids)
            : $medicines->where('id', $ids[0]);

        $medicines = $query->first();
        if (! $medicines) {
            throw new NotFoundHttpException('Medicines data not found.');
        }

        return $medicines;
    }

    /**
     * Summary of getMedicineList
     * @param array $select
     * @return \Illuminate\Database\Eloquent\Collection<int, Medicines>
     */
    public function getMedicineList(array $select = ['*'])
    {
        return Medicines::select($select)->get();
    }

    /**
     * Summary of getAllMedicineList
     * @return \Illuminate\Database\Eloquent\Collection<int, Medicines>
     */
    public function getAllMedicineList()
    {
        return Medicines::select('id', 'medicine_name')->get();
    }

    /**
     * Summary of medicinesList
     * @param string|array $fieldname
     * @return \Illuminate\Database\Eloquent\Collection<int, Medicines>
     */
    public function medicinesList(Request $request)
    {
        $fieldname = $request->field_name;
        $fields    = is_array($fieldname) ? $fieldname : [$fieldname];
        $fields    = array_merge(['id', 'unit_price'], $fields);
        if (isset($request->departmentValue)) {
            $departmentType = DepartmentTypeData::normalizeDepartmentType($request->departmentValue ?? TypeEnum::Proctology->value);
            return Medicines::where('department_type', $departmentType)->select($fields)->get();
        } else {
            return Medicines::select($fields)->get();

        }
    }

}

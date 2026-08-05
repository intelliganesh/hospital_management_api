<?php

namespace App\Services\Master;

use AutoIdGenerate;
use App\Enums\ServiceType;
use Illuminate\Http\Request;
use App\Models\Master\Doctors;
use App\Contracts\CRUDContract;
use App\Services\CheckValidation;
use App\Traits\DoctorsValidation;
use App\Contracts\FilterContract;

class DoctorsService implements CRUDContract, FilterContract
{

    use DoctorsValidation;

    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = Doctors::$columns;
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        $data->where(function ($query) use ($searchText) {
            foreach ($this->columns as $column) {
                $query->orWhere($column, 'like', '%' . $searchText . '%');
            }
        });
        return $data;
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['full_name']) && $request['full_name'] != null && $request['full_name'] != '') {
            $data = $data->where('full_name', 'like', '%' . $request['full_name'] . '%');
        }
        if (isset($request['email']) && $request['email'] != null && $request['email'] != '') {
            $data = $data->where('email', 'like', '%' . $request['email'] . '%');
        }

        if (isset($request['phone_number']) && $request['phone_number'] != null && $request['phone_number'] != '') {
            $data = $data->where('phone_number', 'like', '%' . $request['phone_number'] . '%');
        }

        if (isset($request['email']) && $request['email'] != null && $request['email'] != '') {
            $data = $data->where('email', 'like', '%' . $request['email'] . '%');
        }

        if (isset($request['qualification']) && $request['qualification'] != null && $request['qualification'] != '') {
            $data = $data->where('qualification', 'like', '%' . $request['qualification'] . '%');
        }

        if (isset($request['experience_years']) && $request['experience_years'] != null && $request['experience_years'] != '') {
            $data = $data->where('experience_years', 'like', '%' . $request['experience_years'] . '%');
        }

        return $data;
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated partialUpdate is not in use
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
        $doctor = [
            'code' => AutoIdGenerate::generateId(ServiceType::DoctorCode)
        ];
        Doctors::create(array_merge($request->all(), $doctor));
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
        $doctor = Doctors::findOrFail($id);
        $doctor->update($request->all());
    }

    /**
     * @deprecated partialUpdate is not in use
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
        Doctors::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Doctors
     */
    public function get(string $id): Doctors
    {
        $doctor = Doctors::findOrFail($id);
        return $doctor;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $doctor = Doctors::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $doctor = $this->search($searchValue, $doctor);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $doctor = $doctor->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $doctor = $this->filterMultipleFields($request->multiple_filter, $doctor);
        }

        return $doctor->select($this->columns)->paginate(env('PAGINATION', 25));

    }

    /**
     * Summary of listForDropdown
     * @return \Illuminate\Database\Eloquent\Collection<int, Doctors>
     */
    public function listForDropdown()
    {
        return Doctors::select('id', 'full_name', 'doctor_code')->get();
    }

}
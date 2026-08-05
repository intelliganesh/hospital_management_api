<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Prescriptions;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Attributes\Transactional;
use App\Services\Users\UserService;
use App\Traits\PrescriptionValidation;
use App\Services\Master\MedicinesService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PrescriptionService implements CRUDContract, FilterContract
{

    use PrescriptionValidation;
    private $doctorService;
    private $patientService;
    private $medicineService;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\PatientService $patientService
     * @param \App\Services\Users\UserService $doctorService
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\Master\MedicinesService $medicineService
     */
    public function __construct(UserService $doctorService, PatientService $patientService, CheckValidation $checkValidationService, MedicinesService $medicineService)
    {
        $this->doctorService = $doctorService;
        $this->patientService = $patientService;
        $this->medicineService = $medicineService;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $prescription = Prescriptions::query();
        if ($request->has('search')) {
            $prescription = $this->search($request->input('search'), $prescription);
        }
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'patient_number';
            $sortOrder = $request->sort_order ?? 'desc';
            $prescription = $prescription->orderBy($sortBy, $sortOrder);
        }
        return $prescription->select('id', 'patient_number', 'patient_name', 'patient_email', 'patient_phone', 'doctor_email', 'doctor_name', 'doctor_phone', 'medicine_name')->paginate(env('PAGINATION', 25));

    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create prescription record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $remainingData = $this->getRemainingData($request);
        Prescriptions::create(array_merge($request->all(), $remainingData));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update prescription record within a secure transaction')]
    public function update(Request $request, ?string $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        $prescription = Prescriptions::findOrFail($id);
        if (!$prescription) {
            throw new NotFoundHttpException('Prescription data not found.');
        }
        $remainingData = $this->getRemainingData($request);
        $prescription->update(array_merge($request->all(), $remainingData));

    }


    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $prescription = Prescriptions::findOrFail($id);
        if (!$prescription) {
            throw new NotFoundHttpException('Prescription data not found.');
        }
        $prescription->delete();
    }


    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Prescriptions
     */
    public function get(string $id): Prescriptions
    {
        $prescription = Prescriptions::findOrFail($id);
        if (!$prescription) {
            throw new NotFoundHttpException('Prescription data not found.');
        }
        return $prescription;
    }

    /**
     * @deprecated message
     */
    public function partialUpdate(Request $request, ?string $id): void
    {
        // write code here
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('medicine_name', 'like', '%' . $searchText . '%');
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

    /**
     * Summary of getRemainingData
     * @param \Illuminate\Http\Request $request
     * @return array{doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, medicine_name: mixed, patient_email: mixed, patient_name: string, patient_number: mixed, patient_phone: mixed}
     */
    private function getRemainingData(Request $request)
    {
        $doctor = $this->doctorService->get($request->doctor_id);
        $patient = $this->patientService->get($request->patient_id);
        $medicien = $this->medicineService->getMedicineById($request->medicine_ids, ['medicine_name']);
        $remainingData = [
            'doctor_name' => $doctor->name,
            'doctor_email' => $doctor->email,
            'patient_email' => $patient->email,
            'doctor_phone' => $doctor->phone,
            'patient_phone' => $patient->phone_no,
            'medicine_name' => $medicien->medicine_name,
            'patient_number' => $patient->patient_number,
            'patient_name' => $patient->first_name . ' ' . $patient->last_name,
        ];
        return $remainingData;
    }



}
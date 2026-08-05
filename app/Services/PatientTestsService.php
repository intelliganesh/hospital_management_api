<?php

namespace App\Services;

use Throwable;
use App\Models\PatientTests;
use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Contracts\FilterContract;
use App\Traits\PatientTestsValidation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PatientTestsService implements CRUDContract, FilterContract
{

    use PatientTestsValidation;

    private static $consultationColumns = ['patient_name', 'patient_email', 'patient_phone', 'patient_number', 'doctor_name', 'doctor_email', 'doctor_phone'];

    protected $paymentService;
    protected $consultationService;
    private $checkValidationService;

    public function __construct(PaymentService $paymentService, CheckValidation $checkValidationService, ConsultationService $consultationService)
    {
        $this->paymentService = $paymentService;
        $this->consultationService = $consultationService;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return PatientTests
     */
    public function get(string $id): PatientTests|Throwable
    {
        $patientTest = PatientTests::findOrFail($id);
        if (!$patientTest) {
            throw new NotFoundHttpException('Data not found.');
        }
        return $patientTest;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request = null): mixed
    {
        $patientTest = PatientTests::query();
        if ($request && $request->has('search')) {
            $searchValue = $request->search;
            $patientTest = $this->search($searchValue, $patientTest);
        }
        if ($request && $request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'test_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $patientTest = $patientTest->orderBy($sortBy, $sortOrder);
        }
        return $patientTest->paginate(env('PAGINATION', 25));
    }

    /**
     * @deprecated message
     */
    public function create(Request $request): void
    {
        //code here
    }

    /**
     * Summary of patientTestCreate
     * @param \Illuminate\Http\Request $request
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create patient tests record within a secure transaction')]
    public function patientTestCreate(Request $request)
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $consultation = $this->consultationService->getByColumnNameDynamic('id', $request->consultation_id);
        if (!$consultation) {
            throw new NotFoundHttpException('Data not found.');
        }
        $consultation->only(self::$consultationColumns);
        $patientTests = PatientTests::create(array_merge($request->all(), $consultation->toArray()));
        $paymentData = ['amount' => $request->billing_amount, 'amount_for' => 'Test'];
        $this->paymentService->create(new Request(array_merge($paymentData, $consultation->toArray())));
        return ['id' => $patientTests->id];
    }

    /**
     * @deprecated message
     */
    public function update(Request $request, ?string $id): void
    {
        //code here
    }


    /**
     * Summary of patientTestUpdate
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return array{id: string|null}
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update patient tests record within a secure transaction')]
    public function patientTestUpdate(Request $request, ?string $id)
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        $patientTest = PatientTests::findOrFail($id);
        if (!$patientTest) {
            throw new NotFoundHttpException('Data not found.');
        }
        $consultation = $this->consultationService->getByColumnNameDynamic('id', $request->consultation_id);
        if (!$consultation) {
            throw new NotFoundHttpException('Data not found.');
        }
        $consultation->only(self::$consultationColumns);
        $paymentData = ['amount' => $request->billing_amount, 'amount_for' => 'Test'];
        $this->paymentService->updateByColumnName(new Request(array_merge($paymentData, $consultation->toArray())), $consultation->appointment_id, 'appointment_id');
        $patientTest->update(array_merge($request->all(), $consultation->toArray()));

        return ['id' => $id];
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $patientTest = PatientTests::findOrFail($id);
        if (!$patientTest) {
            throw new NotFoundHttpException('Data not found.');
        }
        $patientTest->delete();
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
        return $data->where('test_name', 'like', '%' . $searchText . '%')->orWhere('test_place', 'like', '%' . $searchText . '%')->orWhere('result_status', 'like', '%' . $searchText . '%')->orWhereHas('users', function ($query) use ($searchText) {
            $query->where('name', 'like', '%' . $searchText . '%');
        });
    }
    /**
     * @deprecated message
     */
    public function filterByDateRange(string $searchText, $data)
    {
        //code here
    }
    /**
     * @deprecated message
     */
    public function sortData(string $searchText, $data)
    {
    }
}
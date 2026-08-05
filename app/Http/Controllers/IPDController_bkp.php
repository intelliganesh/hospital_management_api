<?php

namespace App\Http\Controllers;

use Exception;
use App\Services\IPDService_bkp;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\PatientHelperService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IPDController_bkp extends Controller
{

    use ResponseTrait;

    private $ipdService;

    private $patientHelperService;

    public function __construct(IPDService_bkp $ipdService, PatientHelperService $patientHelperService)
    {
        $this->ipdService = $ipdService;
        $this->patientHelperService = $patientHelperService;
    }

    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->ipdService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function get(string $id)
    {
        try {
            return $this->successResponse($this->ipdService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function create(Request $request)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->ipdService);
            $proxiedService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->ipdService);
            $proxiedService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function delete(string $id)
    {
        try {
            $this->ipdService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function patientListForDropDown()
    {
        try {
            return $this->successResponse($this->patientHelperService->patientListForDropDown());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
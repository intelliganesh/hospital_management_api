<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Services\Master\BillingServiceCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BillingServiceCategoryController extends Controller
{
    use ResponseTrait;

    private $billingServiceCategoryService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\BillingServiceCategoryService $billingServiceCategoryService
     */
    public function __construct(BillingServiceCategoryService $billingServiceCategoryService)
    {
        $this->billingServiceCategoryService = $billingServiceCategoryService;
    }

    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->billingServiceCategoryService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function get(string $id)
    {
        try {
            return $this->successResponse($this->billingServiceCategoryService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function create(Request $request)
    {
        try {
            $this->billingServiceCategoryService->create($request);
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
            $this->billingServiceCategoryService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
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
            $this->billingServiceCategoryService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function billingServiceCategoryList()
    {
        try {
            return $this->successResponse($this->billingServiceCategoryService->billingServiceCategoryList());
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

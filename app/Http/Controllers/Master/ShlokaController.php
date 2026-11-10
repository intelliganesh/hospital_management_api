<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\ShlokaService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class ShlokaController extends Controller
{
    use ResponseTrait;

    private $shlokaService;
    public function __construct(ShlokaService $shlokaService)
    {
        $this->shlokaService = $shlokaService;
    }
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->shlokaService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->shlokaService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/Shloka data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function create(Request $request)
    {
        try {
            $this->shlokaService->create($request);
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
            $this->shlokaService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/Shloka data not found.');
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
            $this->shlokaService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/Shloka data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function optionsList()
    {
        try {
            return $this->successResponse($this->shlokaService->optionsList());
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/Shloka data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
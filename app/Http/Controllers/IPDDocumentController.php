<?php

namespace App\Http\Controllers;

use App\Services\IPDDocumentService;
use App\Traits\ResponseTrait;
use DomainException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Documents",
 *     description="API endpoints for managing generated and uploaded IPD documents"
 * )
 */
class IPDDocumentController extends Controller
{
    use ResponseTrait;

    public function __construct(private IPDDocumentService $ipdDocumentService) {}

    /**
     * @OA\Post(
     *     path="/api/ipd_document_add",
     *     summary="Upload documents for an IPD patient",
     *     tags={"IPD Documents"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"ipd_id", "document_type", "documents"},
     *
     *                 @OA\Property(property="ipd_id", type="string", format="uuid"),
     *                 @OA\Property(property="document_type", type="string", pattern="^[a-z0-9]+(?:_[a-z0-9]+)*$", example="supportive_document"),
     *                 @OA\Property(
     *                     property="documents",
     *                     type="array",
     *
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Documents uploaded successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="IPD record not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function create(Request $request)
    {
        try {
            return $this->successResponse(
                $this->ipdDocumentService->create($request),
                'IPD documents uploaded successfully',
                201
            );
        } catch (ValidationException $exception) {
            return $this->validationResponse($exception);
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse(new NotFoundHttpException('IPD record not found.'));
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_document_list/{ipd_id}",
     *     summary="List documents for an IPD patient",
     *     tags={"IPD Documents"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(name="ipd_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(
     *         name="document_type",
     *         in="query",
     *         required=false,
     *         description="Optional snake_case document type filter",
     *
     *         @OA\Schema(type="string", example="supportive_document")
     *     ),
     *
     *     @OA\Response(response=200, description="IPD documents retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="IPD record not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function all(Request $request, string $ipdId)
    {
        try {
            return $this->successResponse(
                $this->ipdDocumentService->all($ipdId, $request),
                'IPD documents retrieved successfully'
            );
        } catch (ValidationException $exception) {
            return $this->validationResponse($exception);
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse(new NotFoundHttpException('IPD record not found.'));
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_document_details/{id}",
     *     summary="Get an IPD document",
     *     tags={"IPD Documents"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *
     *     @OA\Response(response=200, description="IPD document retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="IPD document not found"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse(
                $this->ipdDocumentService->get($id),
                'IPD document retrieved successfully'
            );
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse(new NotFoundHttpException('IPD document not found.'));
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_document_update/{id}",
     *     summary="Update an uploaded IPD document",
     *     description="Generated IPD documents are read-only",
     *     tags={"IPD Documents"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="document_type", type="string", example="supportive_document"),
     *                 @OA\Property(property="document", type="string", format="binary")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="IPD document updated successfully"),
     *     @OA\Response(response=403, description="Generated documents cannot be updated"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="IPD document not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            return $this->successResponse(
                $this->ipdDocumentService->update($id, $request),
                'IPD document updated successfully'
            );
        } catch (ValidationException $exception) {
            return $this->validationResponse($exception);
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse(new NotFoundHttpException('IPD document not found.'));
        } catch (DomainException $exception) {
            return $this->errorResponse([], $exception->getMessage(), 403);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/ipd_document_delete/{id}",
     *     summary="Delete an uploaded IPD document",
     *     description="Generated IPD documents are read-only",
     *     tags={"IPD Documents"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *
     *     @OA\Response(response=200, description="IPD document deleted successfully"),
     *     @OA\Response(response=403, description="Generated documents cannot be deleted"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="IPD document not found"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->ipdDocumentService->delete($id);

            return $this->successResponse(null, 'IPD document deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse(new NotFoundHttpException('IPD document not found.'));
        } catch (DomainException $exception) {
            return $this->errorResponse([], $exception->getMessage(), 403);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }
}

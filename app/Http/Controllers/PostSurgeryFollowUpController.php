<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\PostSurgeryFollowUpService;

/**
 * @OA\Tag(
 *     name="Post Surgery Follow Up",
 *     description="Post Surgery Follow Up operations"
 * )
 */
class PostSurgeryFollowUpController extends Controller
{

    use ResponseTrait;

    private $postSurgeryFollowUpService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(PostSurgeryFollowUpService $postSurgeryFollowUpService)
    {
        $this->postSurgeryFollowUpService = $postSurgeryFollowUpService;

    }

    /**
     * @OA\Get(
     *     path="/api/post_surgery_follow_up_list",
     *     summary="Get all post surgery follow-ups",
     *     description="Retrieve a list of all post surgery follow-ups in the system",
     *     tags={"Post Surgery Follow Up"},
     *     security={{"bearerAuth": {}}},
     *       @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of post surgery follow-ups",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery follow-ups retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890"),
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="b2c3d4e5-6789-01fg-hi34-56789jklm901"),
     *                 @OA\Property(property="surgery_id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012"),
     *                 @OA\Property(property="follow_up_date", type="string", format="date-time", example="2024-09-15T10:00:00Z"),
     *                 @OA\Property(property="notes", type="string", example="Patient recovering well after surgery"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-01T08:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-01T08:30:00Z")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->postSurgeryFollowUpService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/post_surgery_follow_up_details/{id}",
     *     summary="Get complete post surgery follow-up details",
     *     tags={"Post Surgery Follow Up"},
     *     description="Get complete post surgery follow-up details by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the post surgery follow-up to get details",
     *          @OA\Schema(type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful post surgery follow-up details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery follow-up details retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890"),
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="b2c3d4e5-6789-01fg-hi34-56789jklm901"),
     *                 @OA\Property(property="surgery_id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012"),
     *                 @OA\Property(property="follow_up_date", type="string", format="date-time", example="2024-09-15T10:00:00Z"),
     *                 @OA\Property(property="notes", type="string", example="Patient recovering well after surgery"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-01T08:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-01T08:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function get(string $id)
    {
        try {
            return $this->successResponse($this->postSurgeryFollowUpService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryFollowUp data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/post_surgery_follow_up_add",
     *     summary="Add a new post surgery follow-up",
     *     tags={"Post Surgery Follow Up"},
     *     description="Add a new post surgery follow-up record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Post surgery follow-up details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={"patient_id", "surgery_id", "follow_up_date"},
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="b2c3d4e5-6789-01fg-hi34-56789jklm901"),
     *                 @OA\Property(property="surgery_id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012"),
     *                 @OA\Property(property="follow_up_date", type="string", format="date-time", example="2024-09-15T10:00:00Z"),
     *                 @OA\Property(property="notes", type="string", example="Patient recovering well after surgery")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post surgery follow-up created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery follow-up created successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"field_name": {"The field is required."}},
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function create(Request $request)
    {
        try {
            $this->postSurgeryFollowUpService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/post_surgery_follow_up_update/{id}",
     *     summary="Update post surgery follow-up",
     *     tags={"Post Surgery Follow Up"},
     *     description="Update post surgery follow-up details by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post surgery follow-up to update",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update post surgery follow-up details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="b2c3d4e5-6789-01fg-hi34-56789jklm901"),
     *                 @OA\Property(property="surgery_id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012"),
     *                 @OA\Property(property="follow_up_date", type="string", format="date-time", example="2024-09-15T10:00:00Z"),
     *                 @OA\Property(property="notes", type="string", example="Patient recovering well after surgery")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post surgery follow-up updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery follow-up updated successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"field_name": {"The field is required."}},
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        try {
            $this->postSurgeryFollowUpService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryFollowUp data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/download-post-surgery-follow-up",
     *     summary="Download post surgery follow-up document",
     *     tags={"Post Surgery Follow Up"},
     *     description="Download a post surgery follow-up document",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document download successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Document downloaded successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    public function followUpDownload(Request $request)
    {
        try {
            return $this->postSurgeryFollowUpService->followUpDownload($request);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryFollowUp data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/download-post-surgery-follow-up-pdf/{id}",
     *     summary="Get post surgery follow-up document download URL",
     *     tags={"Post Surgery Follow Up"},
     *     description="Get download URL for a post surgery follow-up document",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post surgery follow-up document",
     *         @OA\Schema(type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved download URL",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Download URL retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="url", type="string", example="https://example.com/documents/follow-up-123.pdf")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    public function getPostSurgeryFollowUpDownload(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->postSurgeryFollowUpService->getPostSurgeryFollowUpDownload($id)]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryFollowUp data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/post_surgery_follow_up_delete/{id}",
     *     summary="Delete a post surgery follow-up",
     *     tags={"Post Surgery Follow Up"},
     *     description="Deletes a post surgery follow-up record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post surgery follow-up to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post surgery follow-up successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery follow-up deleted successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function delete(string $id)
    {
        try {
            $this->postSurgeryFollowUpService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryFollowUp data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/post_surgery_details",
     *     summary="Get post surgery details",
     *     tags={"Post Surgery Follow Up"},
     *     description="Get details for a post surgery record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="surgery_id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved post surgery details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012"),
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-345678ijklmn"),
     *                 @OA\Property(property="surgery_date", type="string", format="date", example="2023-09-15"),
     *                 @OA\Property(property="surgery_type", type="string", example="Appendectomy"),
     *                 @OA\Property(property="notes", type="string", example="Patient recovering well"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-15T10:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-15T10:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    public function postSurgeryDetails(Request $request)
    {
        try {
            return $this->successResponse($this->postSurgeryFollowUpService->postSurgeryDetails($request));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryDetails data not found.');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/post_surgery_details_list_for_dropdown",
     *     summary="Get list of post surgery details",
     *     tags={"Post Surgery Follow Up"},
     *     description="Get a list of post surgery details",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="b2c3d4e5-6789-01fg-hi34-56789jklm901")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved post surgery details list",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post surgery details list retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="c3d4e5f6-7890-12gh-ij56-78901klmn012"),
     *                 @OA\Property(property="surgery_date", type="string", format="date", example="2023-09-15"),
     *                 @OA\Property(property="surgery_type", type="string", example="Appendectomy")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    public function postSurgeryDetailsList(Request $request)
    {
        try {
            return $this->successResponse($this->postSurgeryFollowUpService->postSurgeryDetailsList($request));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('PostSurgeryDetails data not found.');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
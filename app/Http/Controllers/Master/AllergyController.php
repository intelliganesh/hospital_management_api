<?php

namespace App\Http\Controllers\Master;

use OpenApi\Annotations as OA;


use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\AllergyService;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Allergy",
 *     description="API endpoints for managing patient allergies, allergen types, and reactions"
 * )
 */
class AllergyController extends Controller
{
    use ResponseTrait;

    private $allergyService;

    public function __construct(AllergyService $allergyService)
    {
        $this->allergyService = $allergyService;
    }

    /**
     * @OA\Get(
     *     path="/api/allergies_list",
     *     summary="Get all allergens",
     *     description="Retrieve a list of all allergens",
     *     tags={"Allergy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of allergens",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="allergen_name", type="string", example="Peanut"),
     *                 @OA\Property(
     *                    property="allergen_type",
     *                    type="string",
     *                    enum={
     *                        "Food", "Drug", "Latex", "Plant", "Other", 
     *                        "Animal", "Insect", "Vaccine", "Chemical", "Environmental"
     *                    },
     *                    example="Food"
     *                 ),
     *                 @OA\Property(property="other_allergen_type", type="string", example="Metal"), 
     *                 @OA\Property(property="reaction_type", type="string", example="Anaphylaxis"),
     *                 @OA\Property(property="documented_by", type="string", example="Dr. John Doe"),
     *                 @OA\Property(property="notes", type="string", example="Patient needs to avoid peanuts in all forms"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-19T14:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-19T14:35:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bad request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->allergyService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/allergies_details/{id}",
     *     summary="Get allergy by ID",
     *     tags={"Allergy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Allergy details successfully fetched.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Allergy details successfully fetched."
     *              ),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="result",
     *                     type="object",
     *                     required={"allergen_name", "allergen_type","documented_by"},
     *                     @OA\Property(property="allergen_type", type="string", example="Food"),
     *                     @OA\Property(property="allergen_name", type="string", example="Penicillin"),
     *                     @OA\Property(property="other_allergen_type", type="string", example="Anaphylaxis"),
     *                     @OA\Property(property="documented_by", type="string", example="Dr. Smith, Allergy Clinic"),
     *                     @OA\Property(property="notes", type="string", example="Confirmed by skin test in 2019")
     *                 ),
     *             ),
     *          ),
     *      ),
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
            return $this->successResponse($this->allergyService->get($id));
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/allergies_add",
     *     summary="Create new allergy",
     *     tags={"Allergy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"allergen_name", "allergen_type","documented_by"},
     *             @OA\Property(property="allergen_type", type="string", example="Food"),
     *             @OA\Property(property="allergen_name", type="string", example="Penicillin"),
     *             @OA\Property(property="other_allergen_type", type="string", example="Anaphylaxis"),
     *             @OA\Property(property="documented_by", type="string", example="Dr. Smith, Allergy Clinic"),
     *             @OA\Property(property="notes", type="string", example="Confirmed by skin test in 2019")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Allergy created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Allergy created successfully.")
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
            $this->allergyService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/allergies_update/{id}",
     *     summary="Update allergy by ID",
     *     tags={"Allergy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"allergen_name", "allergen_type", "reaction_type", "severity"},
     *             @OA\Property(property="allergen_name", type="string", example="Penicillin"),
     *             @OA\Property(property="allergen_type", type="string", example="Medication"),
     *             @OA\Property(property="reaction_type", type="string", example="Rash"),
     *             @OA\Property(property="severity", type="string", example="Severe"),
     *             @OA\Property(property="documented_by", type="string", example="Dr. Smith, Allergy Clinic"),
     *             @OA\Property(property="notes", type="string", example="Confirmed by skin test in 2019")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Allergy updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Allergy updated successfully.")
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
            $this->allergyService->update($request, $id);
            return $this->successResponse();
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
     *     path="/api/allergies_delete/{id}",
     *     summary="Delete allergy by ID",
     *     tags={"Allergy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Allergy deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Allergy deleted successfully.")
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
            $this->allergyService->delete($id);
            return $this->successResponse();
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

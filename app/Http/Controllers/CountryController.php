<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

use Exception;
use App\Models\Country;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @OA\Tag(
 *     name="Geography",
 *     description="API endpoints for managing countries"
 * )
 */
class CountryController extends Controller
{
    use ResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/countries/{id}",
     *     tags={"Geography"},
     *     summary="Get list of countries",
     *     description="Returns a list of all available countries",
     *     security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=false,
     *         description="ID of the country",
     *         @OA\Schema(
     *             type="string",
     *             example="1,name"
     *         )
     *     ),
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="Page number for pagination",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Number of items per page",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Countries retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1, description="Country ID"),
     *                 @OA\Property(property="name", type="string", example="India", description="Country name"),
     *                 @OA\Property(property="code", type="string", example="IN", description="Country code (e.g., IN, US)"),
     *                 @OA\Property(property="phonecode", type="string", example="+91", nullable=true, description="Phone code (e.g., +91)")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function all(?string $id = null)
    {
        try {
            $country = [];
            if ($id) {
                $country = Country::where('id', $id)->orWhere('name', 'like', '%' . $id . '%')->get();
            } else {
                $country = Country::get();
            }
            if (!$country) {
                throw new NotFoundHttpException('State data not found');
            }
            return $this->successResponse($country);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

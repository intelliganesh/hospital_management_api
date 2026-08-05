<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

use Exception;
use App\Models\City;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @OA\Tag(
 *     name="Geography",
 *     description="API endpoints for managing cities"
 * )
 */

class CityController extends Controller
{
    use ResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/cities/{state_id}",
     *     tags={"Geography"},
     *     summary="Get cities by state ID",
     *     description="Returns a list of cities that belong to the given state ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="state_id",
     *         in="path",
     *         required=true,
     *         description="ID of the state",
     *         @OA\Schema(
     *             type="string",
     *             example="10,name"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Cities retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1, description="City ID"),
     *                 @OA\Property(property="name", type="string", example="Bangalore", description="City name"),
     *                 @OA\Property(property="state_id", type="integer", example=10, description="ID of the associated state")
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

    public function all(string $id)
    {
        try {
            $city = City::where('state_id', $id)->orWhere('name', $id)->get();
            if (!$city) {
                throw new NotFoundHttpException('City data not found');
            }
            return $this->successResponse($city);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

use Exception;
use App\Models\State;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Geography",
 *     description="API endpoints for managing geographical data like states and countries"
 * )
 */
class StateController extends Controller
{
    use ResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/states/{country_id}",
     *     tags={"Geography"},
     *     summary="Get states by country ID",
     *     description="Returns a list of states that belong to the given country ID or name",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="path",
     *         required=true,
     *         description="ID or name of the country",
     *         @OA\Schema(
     *             type="string",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="States list successfully fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="States list successfully fetched"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="State ID"),
     *                     @OA\Property(property="name", type="string", example="Karnataka", description="State name"),
     *                     @OA\Property(property="country_id", type="integer", example=1, description="ID of the associated country"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
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
            $state = State::where('country_id', $id)->orWhere('name', $id)->get();
            if (!$state) {
                throw new NotFoundHttpException('State data not found');
            }
            return $this->successResponse($state);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

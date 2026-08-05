<?php

namespace App\Http\Controllers\Api\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="UnauthorizedResponse",
 *     description="Unauthorized - Invalid credentials",
 *     @OA\JsonContent(
 *         @OA\Property(property="error", type="string", example="Invalid credentials")
 *     )
 * )
 *
 * @OA\Response(
 *     response="ServerErrorResponse",
 *     description="Server error",
 *     @OA\JsonContent(
 *         @OA\Property(property="status", type="string", example="error"),
 *         @OA\Property(property="message", type="string", example="Something went wrong.")
 *     )
 * ),
 * 
 * @OA\Response(
 *    response="NotFound",
 *    description="Data not found",
 *    @OA\JsonContent(
 *        @OA\Property(property="status", type="string", example="error"),
 *        @OA\Property(property="message", type="string", example="Data not found.")
 *    )
 * )
 */
class Responses
{
    // This class can be empty; it’s just a container for Swagger annotations
}

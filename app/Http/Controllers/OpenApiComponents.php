<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 *     description="Enter your bearer token, e.g. 'Bearer {token}'"
 * )
 */
class OpenApiComponents
{
    // This class can be empty; it's just a container for Swagger annotations
}
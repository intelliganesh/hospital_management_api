<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Hospital Management API",
 *     version="1.0.0",
 *     description="API for managing hospital operations, including user authentication and management",
 *     @OA\Contact(
 *         email="vishnuprakash@intellispiders.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Local development server"
 * )
 */
class Swagger
{
    // This can be an empty class; it’s just a placeholder for annotations
}
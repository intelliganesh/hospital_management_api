<?php

namespace App\Interceptors;

use Exception;
use ReflectionMethod;
use App\Attributes\Transactional;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;

class ServiceInterceptor
{
    /**
     * Wrap the given service in a dynamic proxy that intercepts method calls.
     *
     * @param object $service
     * @return object
     */
    public static function intercept(object $service): object
    {
        return new class ($service) {
            private object $service;

            public function __construct(object $service)
            {
                $this->service = $service;
            }

            /**
             * Dynamically intercept method calls.
             *
             * @param string $method
             * @param array $arguments
             * @return mixed
             */
            public function __call(string $method, array $arguments)
            {
                // Reflect on the method being called
                $reflection = new ReflectionMethod($this->service, $method);
                $attributes = $reflection->getAttributes(Transactional::class);

                // If the method is annotated with Transactional, enforce security and transaction
                if (!empty($attributes)) {
                    /** @var Transactional $txAttribute */
                    $txAttribute = $attributes[0]->newInstance();

                    // Security check: if secure flag is true and a required role is specified
                    if ($txAttribute->secure && $txAttribute->requiredRole) {
                        // $user = Auth::user();
                        // if (!$user || $user->role !== $txAttribute->requiredRole) {
                        throw new Exception('Unauthorized: Insufficient permissions for this operation.');
                        // abort(403, 'Unauthorized: Insufficient permissions for this operation.');
                        // }
                    }

                    // Execute within a database transaction
                    return DB::transaction(function () use ($method, $arguments) {
                        return call_user_func_array([$this->service, $method], $arguments);
                    });
                }

                // If no attribute, call the method normally
                return call_user_func_array([$this->service, $method], $arguments);
            }
        };
    }
}

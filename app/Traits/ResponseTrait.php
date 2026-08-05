<?php
namespace App\Traits;

use Illuminate\Validation\ValidationException;
use LogActivity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ResponseTrait
{

    /**
     * Return success response
     *
     * @param string $message
     * @param array|null $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(array | object | null $data = null, string $message = 'Request processed successfully', int $statusCode = 200)
    {
        if (is_null($data)) {
            return response()->json([
                'status'  => 'success',
                'message' => $message,
            ], $statusCode);
        } else {
            return response()->json([
                'status'  => 'success',
                'message' => $message,
                'data'    => $data,
            ], $statusCode);
        }
    }

    /**
     * Return validation error response
     *
     * @param string $message
     * @param int $statusCode
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationResponse(ValidationException $exception, string $message = 'Validation error', int $statusCode = 422)
    {
        // LogActivity::addToLog($exception->getMessage(), $exception->getTraceAsString(), $statusCode());
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $exception->validator->errors()->toArray(),
        ], $statusCode);
    }

    /**
     * Return error response
     *
     * @param NotFoundHttpException $notFound
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFoundResponse(NotFoundHttpException $notFound)
    {
        LogActivity::addToLog($notFound->getMessage(), $notFound->getTraceAsString(), $notFound->getStatusCode());
        return response()->json([
            'errors'  => [],
            'status'  => 'error',
            'message' => $notFound->getMessage(),
        ], $notFound->getStatusCode());
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(array | object | null $errors = [], string $message = 'An error occured while processing.', int $statusCode = 400)
    {
        if (is_array($errors) || is_object($errors)) {
            $errors = json_encode($errors);
        }

        LogActivity::addToLog($message, $errors, $statusCode);
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    /**
     * Return exception response
     *
     * @param \Exception $exception
     * @param string $fallbackMessage
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function exceptionResponse(\Exception $exception, string $fallbackMessage = 'Something went wrong!', int $statusCode = 500)
    {
        LogActivity::addToLog($exception->getMessage(), $exception->getTraceAsString(), $exception->getCode());
        return response()->json([
            'status'  => 'error',
            'message' => $fallbackMessage,
            'error'   => [
                'exception' => $exception->getMessage(),
                'file'      => $exception->getFile(),
                'line'      => $exception->getLine(),
            ],
        ], $statusCode);
    }
}

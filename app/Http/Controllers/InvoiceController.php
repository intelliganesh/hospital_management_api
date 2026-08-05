<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\InvoiceService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Invoices",
 *     description="API endpoints for managing invoices and payments"
 * )
 */
class InvoiceController extends Controller
{

    use ResponseTrait;
    private $invoiceService;

    /**
     * Summary of __construct
     * @param \App\Services\InvoiceService $invoiceService
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * @OA\Get(
     *     path="/api/invoices",
     *     summary="Get all invoices",
     *     description="Retrieves a list of all invoices with optional filtering",
     *     tags={"Invoices"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering invoices",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoices retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="invoice_number", type="string", example="INV-2023-001"),
     *                     @OA\Property(property="patient_id", type="integer", example=5),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=1500.00),
     *                     @OA\Property(property="status", type="string", example="paid"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-15T14:30:00Z")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="last_page_url", type="string"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
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
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->invoiceService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/invoices/{id}",
     *     summary="Get invoice by ID",
     *     description="Retrieves a specific invoice by its ID",
     *     tags={"Invoices"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the invoice to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="invoice_number", type="string", example="INV-2023-001"),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=1500.00),
     *                 @OA\Property(property="paid_amount", type="number", format="float", example=1500.00),
     *                 @OA\Property(property="due_amount", type="number", format="float", example=0.00),
     *                 @OA\Property(property="status", type="string", example="paid"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-15T14:30:00Z"),
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="service_name", type="string", example="Consultation"),
     *                     @OA\Property(property="quantity", type="integer", example=1),
     *                     @OA\Property(property="unit_price", type="number", format="float", example=500.00),
     *                     @OA\Property(property="total_price", type="number", format="float", example=500.00)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
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
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->invoiceService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/invoices",
     *     summary="Create a new invoice",
     *     description="Creates a new invoice with the provided details",
     *     tags={"Invoices"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Invoice data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"patient_id", "items"},
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="invoice_date", type="string", format="date", example="2023-09-15"),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2023-09-30"),
     *                 @OA\Property(property="discount", type="number", format="float", example=50.00),
     *                 @OA\Property(property="tax", type="number", format="float", example=75.00),
     *                 @OA\Property(property="notes", type="string", example="Patient consultation and medication"),
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="service_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=1),
     *                     @OA\Property(property="unit_price", type="number", format="float", example=500.00)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="invoice_number", type="string", example="INV-2023-001")
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
            return $this->successResponse($this->invoiceService->create($request));
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function addOrUpdate(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->invoiceService);
            return $this->successResponse($proxiedService->addOrUpdate($request, $id));
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/invoices/{id}/download",
     *     summary="Download invoice",
     *     description="Generates and returns a download URL for the invoice",
     *     tags={"Invoices"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the invoice to download",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Download URL generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice download URL generated"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="url", type="string", example="https://example.com/storage/invoices/invoice_1.pdf")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
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
    public function download(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->invoiceService->download($id)]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/invoices/payment",
     *     summary="Add payment to invoice",
     *     description="Records a payment for an invoice",
     *     tags={"Invoices"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"invoice_id", "amount", "payment_method"},
     *                 @OA\Property(property="invoice_id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", format="float", example=500.00),
     *                 @OA\Property(property="payment_method", type="string", example="cash"),
     *                 @OA\Property(property="payment_date", type="string", format="date", example="2023-09-15"),
     *                 @OA\Property(property="notes", type="string", example="Partial payment")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment recorded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment recorded successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="invoice_id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", format="float", example=500.00),
     *                 @OA\Property(property="payment_method", type="string", example="cash"),
     *                 @OA\Property(property="payment_date", type="string", format="date", example="2023-09-15")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
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
    public function addPayment(Request $request)
    {
        try {
            return $this->successResponse($this->invoiceService->addPayment($request));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
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
     *     path="/api/invoices/{id}/payment-details",
     *     summary="Get payment details for an invoice",
     *     description="Retrieves all payment records for a specific invoice",
     *     tags={"Invoices"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the invoice to get payment details for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment details retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="invoice_id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", format="float", example=500.00),
     *                 @OA\Property(property="payment_method", type="string", example="cash"),
     *                 @OA\Property(property="payment_date", type="string", format="date", example="2023-09-15"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-15T10:30:00Z")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
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
    public function paymentDetails(string $id)
    {
        try {
            return $this->successResponse($this->invoiceService->paymentDetails($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function test(string $id)
    {
        try {
            return $this->invoiceService->testDetails($id);
            // return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function amountIncludeInInvoice(Request $request)
    {
        try {
            return $this->successResponse($this->invoiceService->amountIncludeInInvoice($request));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function downloadPrescriptionweb(string $id)
    {
        try {
            return $this->invoiceService->downloadPrescriptionweb($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Invoice data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    // public function update(Request $request, string $id)
    // {
    //     try {
    //         return $this->successResponse();
    //     } catch (ModelNotFoundException $e) {
    //         throw new NotFoundHttpException('Invoice data not found.');
    //     } catch (NotFoundHttpException $notFound) {
    //         return $this->errorResponse(
    //             [],
    //             $notFound->getMessage(),
    //             $notFound->getStatusCode()
    //         );
    //     } catch (ValidationException $ve) {
    //         return $this->errorResponse(
    //             $ve->validator->errors()->toArray(),
    //             'Validation error',
    //             422
    //         );
    //     } catch (Exception $e) {
    //         return $this->exceptionResponse($e);
    //     }
    // }

    // public function delete(string $id)
    // {
    //     try {
    //         return $this->successResponse();
    //     } catch (ModelNotFoundException $e) {
    //         throw new NotFoundHttpException('Invoice data not found.');
    //     } catch (NotFoundHttpException $notFound) {
    //         return $this->errorResponse(
    //             [],
    //             $notFound->getMessage(),
    //             $notFound->getStatusCode()
    //         );
    //     } catch (Exception $e) {
    //         return $this->exceptionResponse($e);
    //     }
    // }

}
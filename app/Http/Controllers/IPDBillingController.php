<?php

namespace App\Http\Controllers;

use App\Services\IPDBillingService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Billing",
 *     description="API endpoints for managing IPD billing records, invoice items and payments"
 * )
 */
class IPDBillingController extends Controller
{
    use ResponseTrait;

    private $ipdBillingService;

    public function __construct(IPDBillingService $ipdBillingService)
    {
        $this->ipdBillingService = $ipdBillingService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_billing_list",
     *     summary="Get all IPD billing records",
     *     description="Retrieve a paginated list of IPD billing records with invoice totals calculated from IPD invoice items and receipts",
     *     tags={"IPD Billing"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search by invoice number, patient number, patient name, patient phone or IPD number",
     *          @OA\Schema(type="string", example="Rahul")
     *     ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *          @OA\Schema(type="string", example="created_at")
     *     ),
     *     @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort order",
     *          @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Number of items per page",
     *          @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD billing list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD billing list fetched successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->ipdBillingService->all($request), 'IPD billing list fetched successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_billing_details/{ipd_id}",
     *     summary="Get IPD billing details",
     *     description="Retrieve IPD billing details with grouped invoice items, receipts and calculated summary",
     *     tags={"IPD Billing"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="ipd_id",
     *          in="path",
     *          required=true,
     *          description="IPD ID",
     *          @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD billing details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD billing details fetched successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function get(string $ipdId)
    {
        try {
            return $this->successResponse($this->ipdBillingService->get($ipdId), 'IPD billing details fetched successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD billing data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_billing_update/{ipd_id}",
     *     summary="Create or update IPD billing invoice",
     *     description="Create or update an IPD billing invoice record for an IPD enrollment",
     *     tags={"IPD Billing"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ipd_id",
     *         in="path",
     *         required=true,
     *         description="IPD ID",
     *         @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Billing invoice data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ipd_billing_status", type="string", example="Running/Completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD billing updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD billing updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The amount field is required."})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function updateInvoice(Request $request, string $ipdId)
    {
        try {
            return $this->successResponse($this->ipdBillingService->updateInvoice($request, $ipdId), 'IPD billing updated successfully');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_billing_add_payment/{ipd_id}",
     *     summary="Add IPD billing payment",
     *     description="Record a payment receipt against an IPD billing invoice",
     *     tags={"IPD Billing"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ipd_id",
     *         in="path",
     *         required=true,
     *         description="IPD ID",
     *         @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=5000),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *             @OA\Property(property="date", type="string", format="date", example="2026-06-22"),
     *             @OA\Property(property="payment_type", type="string", example="Cash"),
     *             @OA\Property(property="transaction_id", type="string", example="TXN12345"),
     *             @OA\Property(property="status", type="string", example="Completed"),
     *             @OA\Property(property="notes", type="string", example="Advance payment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD billing payment saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD billing payment saved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The amount field is required."})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function addPayment(Request $request, string $ipdId)
    {
        try {
            return $this->successResponse($this->ipdBillingService->addPayment($request, $ipdId), 'IPD billing payment saved successfully');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD billing data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_billing_add_charges/{ipd_id}",
     *     summary="Add IPD billing charge",
     *     description="Add an invoice item charge under a service category for IPD billing",
     *     tags={"IPD Billing"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ipd_id",
     *         in="path",
     *         required=true,
     *         description="IPD ID",
     *         @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Charge data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"amount","service_category","tax_percent"},
     *             @OA\Property(property="amount", type="number", format="float", example=3000),
     *             @OA\Property(property="front_desk_user_id", type="integer", example=1),
     *             @OA\Property(property="service_category", type="string", example="WARD"),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *             @OA\Property(property="description", type="string", example="Private Room - Room 301"),
     *             @OA\Property(property="tax_percent", type="number", format="float", example=18),
     *             @OA\Property(property="service_date", type="string", format="date", example="2026-06-22")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD billing charges added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD billing charges added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The service category field is required."})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function addCharges(Request $request, string $ipdId)
    {
        try {
            return $this->successResponse($this->ipdBillingService->addCharges($request, $ipdId), 'IPD billing charges added successfully');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD billing data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_billing_update_charges/{id}",
     *     tags={"IPD Billing"},
     *     summary="Update IPD billing charges",
     *     description="Update IPD billing charges including amount, service category, and tax details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD Billing Charge record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=5000),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *             @OA\Property(property="date", type="string", format="date", example="2026-06-22"),
     *             @OA\Property(property="payment_type", type="string", example="Cash"),
     *             @OA\Property(property="transaction_id", type="string", example="TXN12345"),
     *             @OA\Property(property="status", type="string", example="Completed"),
     *             @OA\Property(property="notes", type="string", example="Advance payment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD Billing Charge record updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD Billing Charge record updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD Billing Charge record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function updateCharges(Request $request, string $id)
    {
        try {
            return $this->successResponse($this->ipdBillingService->updateCharges($request, $id), 'IPD Billing Charge record updated successfully');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD Billing Charge record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/ipd_billing_delete_charges/{id}",
     *     tags={"IPD Billing"},
     *     summary="Delete IPD Billing Charge record",
     *     description="Delete an IPD Billing Charge record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD Billing Charge record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD Billing Charge record deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD Billing Charge record deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD Billing Charge record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function deleteCharges(string $chargeId)
    {
        try {
            return $this->successResponse($this->ipdBillingService->deleteCharges($chargeId), 'IPD billing charges deleted successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD billing data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_billing_get_payment_details/{ipd_id}",
     *     summary="Get IPD billing payment details",
     *     description="Retrieve all receipt payments for an IPD billing invoice",
     *     tags={"IPD Billing"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="ipd_id",
     *          in="path",
     *          required=true,
     *          description="IPD ID",
     *          @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD billing payment details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD billing payment details fetched successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function paymentDetails(string $ipdId)
    {
        try {
            return $this->successResponse($this->ipdBillingService->paymentDetails($ipdId), 'IPD billing payment details fetched successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD billing data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

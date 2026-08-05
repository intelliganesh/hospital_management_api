<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Interceptors\ServiceInterceptor;
use App\Services\Master\ExpensesService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Expenses",
 *     description="API endpoints for managing hospital expenses, vouchers, and financial transactions"
 * )
 */
class ExpensesController extends Controller
{
    use ResponseTrait;
    private $expensesService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\ExpensesService $expensesService
     */
    public function __construct(ExpensesService $expensesService)
    {
        $this->expensesService = $expensesService;

    }

    /**
     * @OA\Get(
     *     path="/api/expenses_list",
     *     summary="Get all expenses",
     *     description="Retrieve a list of all expenses in the system with pagination",
     *     tags={"Expenses"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example="office supplies"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example="date"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of expenses",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Expenses list fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="date", type="string", format="date", example="2025-08-26"),
     *                         @OA\Property(property="amount", type="number", format="float", example=1500.50),
     *                         @OA\Property(property="description", type="string", example="Office supplies purchase"),
     *                         @OA\Property(property="expense_name", type="string", example="Office Supplies"),
     *                         @OA\Property(property="mode_of_payment", type="string", example="cash"),
     *                         @OA\Property(property="voucher_number", type="string", example="VCH0001"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-26T10:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-26T10:00:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/expenses_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/expenses_list?page=5"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost/api/expenses_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/expenses_list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
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
            return $this->successResponse($this->expensesService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/expenses_details/{id}",
     *     summary="Get complete expense details",
     *     tags={"Expenses"},
     *     description="Get detailed information about a specific expense by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the expense to retrieve details for",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful expense details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Expense details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date", example="2025-08-26"),
     *                 @OA\Property(property="amount", type="number", format="float", example=1500.50),
     *                 @OA\Property(property="description", type="string", example="Office supplies purchase"),
     *                 @OA\Property(property="expense_name", type="string", example="Office Supplies"),
     *                 @OA\Property(property="mode_of_payment", type="string", example="cash"),
     *                 @OA\Property(property="transaction_id", type="string", example="TXN123456"),
     *                 @OA\Property(property="for_name", type="string", example="Admin Department"),
     *                 @OA\Property(property="entered_name", type="string", example="John Doe"),
     *                 @OA\Property(property="other", type="string", example="Additional information"),
     *                 @OA\Property(property="voucher_number", type="string", example="VCH0001"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-26T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-26T10:00:00.000000Z")
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

    public function get(string $id)
    {
        try {
            return $this->successResponse($this->expensesService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Expenses data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/expenses_add",
     *     summary="Create a new expense record",
     *     tags={"Expenses"},
     *     description="Add a new expense record to the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Expense information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={"date", "amount", "description", "expense_name", "mode_of_payment"},
     *                 @OA\Property(property="date", type="string", format="date", example="2025-08-26", description="Date of the expense"),
     *                 @OA\Property(property="amount", type="number", format="float", example="1500.50", description="Amount of the expense"),
     *                 @OA\Property(property="description", type="string", example="Office supplies purchase", description="Description of the expense"),
     *                 @OA\Property(property="expense_name", type="string", example="Office Supplies", description="Name of the expense category"),
     *                 @OA\Property(property="mode_of_payment", type="string", example="cash", description="Payment method used", enum={"cash", "card", "bank_transfer", "check", "online"}),
     *                 @OA\Property(property="transaction_id", type="string", example="TXN123456", description="Transaction ID if applicable"),
     *                 @OA\Property(property="for_name", type="string", example="Admin Department", description="For whom the expense was made"),
     *                 @OA\Property(property="entered_name", type="string", example="John Doe", description="Name of the person who entered the expense"),
     *                 @OA\Property(property="other", type="string", example="Additional information", description="Any other relevant information"),
     *                 @OA\Property(property="generate_voucher_number", type="boolean", example=true, description="Whether to auto-generate a voucher number"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expense successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Expense created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="date", type="array", @OA\Items(type="string"), example={"The date field is required."}),
     *                 @OA\Property(property="amount", type="array", @OA\Items(type="string"), example={"The amount field is required."}),
     *                 @OA\Property(property="expense_name", type="array", @OA\Items(type="string"), example={"The expense name field is required."})
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
            $proxiedService = ServiceInterceptor::intercept($this->expensesService);
            return $this->successResponse(['id' => $proxiedService->createExpenses($request)]);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/expenses_update/{id}",
     *     summary="Update an expense record",
     *     tags={"Expenses"},
     *     description="Update an existing expense record in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the expense to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Expense information to update",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 @OA\Property(property="date", type="string", format="date", example="2025-08-26", description="Date of the expense"),
     *                 @OA\Property(property="amount", type="number", format="float", example="1500.50", description="Amount of the expense"),
     *                 @OA\Property(property="description", type="string", example="Office supplies purchase", description="Description of the expense"),
     *                 @OA\Property(property="expense_name", type="string", example="Office Supplies", description="Name of the expense category"),
     *                 @OA\Property(property="mode_of_payment", type="string", example="cash", description="Payment method used", enum={"cash", "card", "bank_transfer", "check", "online"}),
     *                 @OA\Property(property="transaction_id", type="string", example="TXN123456", description="Transaction ID if applicable"),
     *                 @OA\Property(property="for_name", type="string", example="Admin Department", description="For whom the expense was made"),
     *                 @OA\Property(property="entered_name", type="string", example="John Doe", description="Name of the person who entered the expense"),
     *                 @OA\Property(property="other", type="string", example="Additional information", description="Any other relevant information"),
     *                 @OA\Property(property="voucher_number", type="string", example="VCH0001", description="Voucher number")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expense successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Expense updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="amount", type="array", @OA\Items(type="string"), example={"The amount must be a number."})
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

    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->expensesService);
            return $this->successResponse(['id' => $proxiedService->updateExpenses($request, $id)]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Expenses data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/expenses_delete/{id}",
     *     summary="Delete an expense record",
     *     tags={"Expenses"},
     *     description="Permanently removes an expense record from the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the expense to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expense successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Expense deleted successfully"),
     *             @OA\Property(property="data", type="object")
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

    public function delete(string $id)
    {
        try {
            $this->expensesService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Expenses data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/expenses_voucher/{id}",
     *     summary="Download expense voucher PDF",
     *     tags={"Expenses"},
     *     description="Generate and download a PDF voucher for an expense record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the expense to generate voucher for",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully generated voucher PDF",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Voucher generated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="url", type="string", example="https://example.com/storage/pdfs/expense_voucher_1_1630000000.pdf"),
     *            )
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
    public function downloadVoucher(string $id)
    {
        try {
            $url = $this->expensesService->getVoucherDownload($id);
            return $this->successResponse(['url' => $url]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Expense data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
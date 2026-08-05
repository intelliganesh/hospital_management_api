<?php

namespace App\Http\Controllers\Reports;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Reports\ExpensesReportsService;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="API endpoints for generating and retrieving various hospital reports"
 * )
 */
class ExpensesReportsController extends Controller
{
    use ResponseTrait;
    private $expensesReportsService;

    /**
     * Summary of __construct
     * @param \App\Services\Reports\ExpensesReportsService $expensesReportsService
     */
    public function __construct(ExpensesReportsService $expensesReportsService)
    {
        $this->expensesReportsService = $expensesReportsService;
    }

    /**
     * @OA\Get(
     *     path="/api/expenses_reports_list",
     *     summary="Get all expense reports",
     *     description="Retrieve a list of all expense reports in the system",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *       @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of expense reports",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", example="1"),
     *                     @OA\Property(property="expense_date", type="string", format="date", example="2023-09-01"),
     *                     @OA\Property(property="expense_category", type="string", example="Utilities"),
     *                     @OA\Property(property="amount", type="number", format="float", example="1250.50"),
     *                     @OA\Property(property="description", type="string", example="Monthly electricity bill"),
     *                     @OA\Property(property="payment_method", type="string", example="Bank Transfer")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=5)
     *                 )
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
            return $this->successResponse($this->expensesReportsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/expenses_reports_download_excel",
     *     summary="Download expenses report as Excel file",
     *     description="Generate and download an Excel file containing expense reports based on filters",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="expense_category", type="string", example="Utilities", nullable=true),
     *             @OA\Property(property="payment_method", type="string", example="Bank Transfer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file download",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function downloadExcel(Request $request)
    {
        try {
            return $this->expensesReportsService->downloadExcel($request);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
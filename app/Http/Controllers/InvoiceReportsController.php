<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\InvoiceReportsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="API endpoints for managing invoice reports"
 * )
 */
class InvoiceReportsController extends Controller
{

    use ResponseTrait;

    private $invoiceReportsService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(InvoiceReportsService $invoiceReportsService)
    {
        $this->invoiceReportsService = $invoiceReportsService;

    }

    /**
     * @OA\Get(
     *     path="/api/reports/invoice_list",
     *     summary="Get all invoiceReportss",
     *     description="Retrieve a list of all invoiceReportss in the system",
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
     *         description="A list of invoice reports",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice reports retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="invoice_number", type="string", example="INV-2023-001"),
     *                     @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                     @OA\Property(property="date", type="string", format="date", example="2023-09-15"),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=1500.00),
     *                     @OA\Property(property="status", type="string", example="paid")
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
            return $this->successResponse($this->invoiceReportsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/invoice_reports_details/{id}",
     *     summary="Get complete invoiceReports details",
     *     tags={"Reports"},
     *     description="Get complete invoiceReports details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the invoiceReports to get invoiceReports details",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful invoiceReports details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice report details successfully fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="invoice_number", type="string", example="INV-2023-001"),
     *                 @OA\Property(property="patient_id", type="integer", example=5),
     *                 @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                 @OA\Property(property="date", type="string", format="date", example="2023-09-15"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=1500.00),
     *                 @OA\Property(property="paid_amount", type="number", format="float", example=1500.00),
     *                 @OA\Property(property="due_amount", type="number", format="float", example=0.00),
     *                 @OA\Property(property="status", type="string", example="paid"),
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
            return $this->successResponse($this->invoiceReportsService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('InvoiceReports data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/reports/invoice_download",
     *     summary="Download invoice reports as Excel",
     *     description="Generates and downloads an Excel file with invoice reports based on filters",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Filter parameters for the report",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2023-12-31"),
     *                 @OA\Property(property="status", type="string", example="paid"),
     *                 @OA\Property(property="patient_id", type="integer", example=5)
     *             )
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
    public function downloadExcel(Request $request)
    {
        try {
            return $this->invoiceReportsService->downloadExcel($request);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


}
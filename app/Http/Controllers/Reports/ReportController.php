<?php
namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\ConsultationReportService;
use App\Services\Reports\FistulaReportService;
use App\Services\Reports\IPDReportService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="API endpoints for generating various medical reports"
 * )
 */
class ReportController extends Controller
{
    use ResponseTrait;

    private $fistulaReportService;
    private $consultationReportService;
    private $ipdReportService;

    public function __construct(
        FistulaReportService $fistulaReportService,
        ConsultationReportService $consultationReportService,
        IPDReportService $ipdReportService
    ) {
        $this->fistulaReportService      = $fistulaReportService;
        $this->consultationReportService = $consultationReportService;
        $this->ipdReportService          = $ipdReportService;
    }

    /**
     * @OA\Get(
     *     path="/api/reports/fistula_list",
     *     summary="Get fistula report",
     *     description="Retrieve fistula report with filters for proctology consultations. All filter parameters should be passed inside 'multiple_filter' array.",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="multiple_filter[doctor_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by doctor ID",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[patient_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by patient ID",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[from_date]",
     *          in="query",
     *          required=false,
     *          description="Start date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[to_date]",
     *          in="query",
     *          required=false,
     *          description="End date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[type_of_fistula_position]",
     *          in="query",
     *          required=false,
     *          description="Filter by fistula position",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[type_of_fistula_sphincter]",
     *          in="query",
     *          required=false,
     *          description="Filter by fistula sphincter type",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[no_of_tracks_in_one_fistula]",
     *          in="query",
     *          required=false,
     *          description="Filter by number of tracks",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[no_of_fistula]",
     *          in="query",
     *          required=false,
     *          description="Filter by number of fistulas",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[internal_opening_position]",
     *          in="query",
     *          required=false,
     *          description="Filter by internal opening position",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[external_opening_position]",
     *          in="query",
     *          required=false,
     *          description="Filter by external opening position",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[secondary_opening_position]",
     *          in="query",
     *          required=false,
     *          description="Filter by secondary opening position",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="multiple_filter[type_of_crypt]",
     *          in="query",
     *          required=false,
     *          description="Filter by type of crypt",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="multiple_filter[sono_fistula_gram]",
     *          in="query",
     *          required=false,
     *          description="Filter by sono fistula gram",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="multiple_filter[mri_fistula_gram]",
     *          in="query",
     *          required=false,
     *          description="Filter by MRI fistula gram",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[basis_of_high_low_riding]",
     *          in="query",
     *          required=false,
     *          description="Filter by basis of high low riding",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[crypt_cause]",
     *          in="query",
     *          required=false,
     *          description="Filter by crypt cause",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[secondary_anal_valve]",
     *          in="query",
     *          required=false,
     *          description="Filter by secondary anal valve",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[posterior_fistulous_angle]",
     *          in="query",
     *          required=false,
     *          description="Filter by posterior fistulous angle",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[sonologist]",
     *          in="query",
     *          required=false,
     *          description="Filter by sonologist name",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[managements]",
     *          in="query",
     *          required=false,
     *          description="Filter by management type",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search by patient/doctor name",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (created_at, appointment_id, patient_name, doctor_name)",
     *         @OA\Schema(type="string", enum={"created_at", "appointment_id", "patient_name", "doctor_name"}, example="created_at")
     *      ),
     *     @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort order (asc/desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Fistula report retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Fistula report retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function fistulaReport(Request $request)
    {
        try {
            return $this->successResponse($this->fistulaReportService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reports/fistula_download",
     *     summary="Download fistula report as Excel",
     *     description="Generate and download fistula report in Excel format. All filter parameters should be passed inside 'multiple_filter' object.",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         description="Filter parameters wrapped in multiple_filter object",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="multiple_filter",
     *                 type="object",
     *                 @OA\Property(property="doctor_id", type="string", example="1"),
     *                 @OA\Property(property="from_date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="to_date", type="string", format="date", example="2025-12-31"),
     *                 @OA\Property(property="type_of_fistula_position", type="string", example="Anterior"),
     *                 @OA\Property(property="type_of_fistula_sphincter", type="string", example="Intersphincteric"),
     *                 @OA\Property(property="no_of_tracks_in_one_fistula", type="string", example="1"),
     *                 @OA\Property(property="no_of_fistula", type="string", example="1"),
     *                 @OA\Property(property="internal_opening_position", type="string", example="6 o'clock"),
     *                 @OA\Property(property="secondary_anal_valve", type="string", example="Present"),
     *                 @OA\Property(property="posterior_fistulous_angle", type="string", example="Acute"),
     *                 @OA\Property(property="sonologist", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="managements", type="string", example="Fistulotomy")
     *             ),
     *             @OA\Property(property="sort_by", type="string", enum={"created_at", "appointment_id", "patient_name", "doctor_name"}, example="created_at"),
     *             @OA\Property(property="sort_order", type="string", enum={"asc", "desc"}, example="desc")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file download",
     *         @OA\MediaType(mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function fistulaReportDownload(Request $request)
    {
        try {
            return $this->fistulaReportService->downloadExcel($request);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reports/consultation_list",
     *     summary="Get consultation report",
     *     description="Retrieve consultation report with filters including proctology and non-proctology data. All filter parameters should be passed inside 'multiple_filter' array.",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="multiple_filter[doctor_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by doctor ID",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[patient_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by patient ID",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[department]",
     *          in="query",
     *          required=false,
     *          description="Filter by department",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[from_date]",
     *          in="query",
     *          required=false,
     *          description="Start date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[to_date]",
     *          in="query",
     *          required=false,
     *          description="End date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search by patient/doctor name or patient number",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (created_at, appointment_id, patient_name, doctor_name)",
     *         @OA\Schema(type="string", enum={"created_at", "appointment_id", "patient_name", "doctor_name"}, example="created_at")
     *      ),
     *     @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort order (asc/desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[proc_chief_complaints]",
     *          in="query",
     *          required=false,
     *          description="Filter by proctology chief complaints",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[proc_surgical_history]",
     *          in="query",
     *          required=false,
     *          description="Filter by proctology surgical history",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[proc_co_morbidities]",
     *          in="query",
     *          required=false,
     *          description="Filter by proctology co-morbidities",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[proc_on_examination]",
     *          in="query",
     *          required=false,
     *          description="Filter by proctology on examination",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[proc_dre]",
     *          in="query",
     *          required=false,
     *          description="Filter by proctology DRE",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[proc_proctoscopy]",
     *          in="query",
     *          required=false,
     *          description="Filter by proctology proctoscopy",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_chief_complaints]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology chief complaints",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_surgical_history]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology surgical history",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_co_morbidities]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology co-morbidities",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_on_examination]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology on examination",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_prakriti]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology Prakriti",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_vikruti]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology Vikruti",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_agni]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology Agni",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_koshta]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology Koshta",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[non_proc_avastha]",
     *          in="query",
     *          required=false,
     *          description="Filter by non-proctology Avastha",
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Property(property="sort_by", type="string", enum={"created_at", "appointment_id", "patient_name", "doctor_name"}, example="created_at"),
     *     @OA\Property(property="sort_order", type="string", enum={"asc", "desc"}, example="desc"),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation report retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation report retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function consultationReport(Request $request)
    {
        try {
            return $this->successResponse($this->consultationReportService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reports/consultation_download",
     *     summary="Download consultation report as Excel",
     *     description="Generate and download consultation report with all proctology and non-proctology fields in Excel format. All filter parameters should be passed inside 'multiple_filter' object.",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         description="Filter parameters wrapped in multiple_filter object",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="multiple_filter",
     *                 type="object",
     *                 @OA\Property(property="doctor_id", type="string", example="1"),
     *                 @OA\Property(property="patient_id", type="string", example="019abfdf-2571-70ab-8833-0df9d34731f5"),
     *                 @OA\Property(property="department", type="string", example="Proctology"),
     *                 @OA\Property(property="from_date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="to_date", type="string", format="date", example="2025-12-31"),
     *                 @OA\Property(property="proc_chief_complaints", type="string", example="Pain"),
     *                 @OA\Property(property="proc_surgical_history", type="string", example="None"),
     *                 @OA\Property(property="proc_co_morbidities", type="string", example="Diabetes"),
     *                 @OA\Property(property="proc_on_examination", type="string", example="Tenderness"),
     *                 @OA\Property(property="proc_dre", type="string", example="Normal"),
     *                 @OA\Property(property="proc_proctoscopy", type="string", example="Internal hemorrhoids"),
     *                 @OA\Property(property="non_proc_chief_complaints", type="string", example="Headache"),
     *                 @OA\Property(property="non_proc_surgical_history", type="string", example="Appendectomy"),
     *                 @OA\Property(property="non_proc_co_morbidities", type="string", example="Hypertension"),
     *                 @OA\Property(property="non_proc_on_examination", type="string", example="Normal"),
     *                 @OA\Property(property="non_proc_prakriti", type="string", example="Vata"),
     *                 @OA\Property(property="non_proc_vikruti", type="string", example="Pitta"),
     *                 @OA\Property(property="non_proc_agni", type="string", example="Mandagni"),
     *                 @OA\Property(property="non_proc_koshta", type="string", example="Krura"),
     *                 @OA\Property(property="non_proc_avastha", type="string", example="Chronic")
     *             ),
     *             @OA\Property(property="sort_by", type="string", enum={"created_at", "appointment_id", "patient_name", "doctor_name"}, example="created_at"),
     *             @OA\Property(property="sort_order", type="string", enum={"asc", "desc"}, example="desc")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file download",
     *         @OA\MediaType(mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function consultationReportDownload(Request $request)
    {
        try {
            return $this->consultationReportService->downloadExcel($request);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reports/ipd_list",
     *     summary="Get basic IPD report",
     *     description="Retrieve a paginated basic IPD report. All report filters can be passed inside the 'multiple_filter' array.",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="multiple_filter[doctor_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by doctor ID",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[patient_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by patient ID",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[status]",
     *          in="query",
     *          required=false,
     *          description="Filter by IPD status",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[ward_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by ward ID",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[room_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by room ID",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[bed_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by bed ID",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[summary_type]",
     *          in="query",
     *          required=false,
     *          description="Filter by discharge summary type",
     *          @OA\Schema(type="string", enum={"surgical", "non_surgical"})
     *      ),
     *     @OA\Parameter(
     *          name="from_date",
     *          in="query",
     *          required=false,
     *          description="Admission start date (YYYY-MM-DD)",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Parameter(
     *          name="to_date",
     *          in="query",
     *          required=false,
     *          description="Admission end date (YYYY-MM-DD)",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search by patient, doctor, IPD number, ward, room, bed, or status",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *          @OA\Schema(type="string", enum={"admission_date_time", "discharge_date_time", "created_at", "ipd_number", "patient_name", "doctor_name", "status"}, example="admission_date_time")
     *      ),
     *     @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort order",
     *          @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Basic IPD report retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Basic IPD report retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function ipdReport(Request $request)
    {
        try {
            return $this->successResponse($this->ipdReportService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reports/ipd_download",
     *     summary="Download basic IPD report as Excel",
     *     description="Generate and download the basic IPD report in Excel format. All report filters can be passed inside the 'multiple_filter' object.",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         description="Filter parameters wrapped in multiple_filter object",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="multiple_filter",
     *                 type="object",
     *                 @OA\Property(property="doctor_id", type="string", example="1"),
     *                 @OA\Property(property="patient_id", type="string", example="019abfdf-2571-70ab-8833-0df9d34731f5"),
     *                 @OA\Property(property="status", type="string", example="admitted"),
     *                 @OA\Property(property="ward_id", type="string", example="019abfdf-2571-70ab-8833-0df9d34731f5"),
     *                 @OA\Property(property="room_id", type="string", example="019abfdf-2571-70ab-8833-0df9d34731f5"),
     *                 @OA\Property(property="bed_id", type="string", example="019abfdf-2571-70ab-8833-0df9d34731f5"),
     *                 @OA\Property(property="summary_type", type="string", enum={"surgical", "non_surgical"}, example="surgical"),
     *                 @OA\Property(property="from_date", type="string", format="date", example="2026-01-01"),
     *                 @OA\Property(property="to_date", type="string", format="date", example="2026-12-31")
     *             ),
     *             @OA\Property(property="search", type="string", example="IPD-001"),
     *             @OA\Property(property="sort_by", type="string", enum={"admission_date_time", "discharge_date_time", "created_at", "ipd_number", "patient_name", "doctor_name", "status"}, example="admission_date_time"),
     *             @OA\Property(property="sort_order", type="string", enum={"asc", "desc"}, example="desc")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file download",
     *         @OA\MediaType(mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function ipdReportDownload(Request $request)
    {
        try {
            return $this->ipdReportService->downloadExcel($request);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

<?php
namespace App\Http\Controllers;

use App\Services\IPDPreliminaryNotesService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Preliminary Notes",
 *     description="API endpoints for managing IPD preliminary notes"
 * )
 */
class IPDPreliminaryNotesController extends Controller
{
    use ResponseTrait;

    private $IPDpreliminaryNotesService;

    public function __construct(IPDPreliminaryNotesService $IPDpreliminaryNotesService)
    {
        $this->IPDpreliminaryNotesService = $IPDpreliminaryNotesService;
    }

    /**
     * @OA\Post(
     *     path="/api/preliminary_notes/{ipd_id}",
     *     summary="Create preliminary notes for an IPD",
     *     tags={"Preliminary Notes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ipd_id",
     *         in="path",
     *         required=true,
     *         description="IPD ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="chief_complaint", type="string", description="Chief complaint", example="Abdominal pain"),
     *             @OA\Property(property="associated_complaint", type="string", description="Associated complaint", example="Nausea and vomiting"),
     *             @OA\Property(property="previous_treatment_history", type="string", description="Previous treatment history", example="Treated with antibiotics 3 months ago"),
     *             @OA\Property(property="medical_history", type="string", description="Medical history", example="Hypertension, Diabetes"),
     *             @OA\Property(property="family_history", type="string", description="Family history", example="Father had heart disease"),
     *             @OA\Property(property="personal_history", type="string", description="Personal history", example="Non-smoker, occasional alcohol"),
     *             @OA\Property(property="allergy", type="string", description="Allergy", example="Penicillin allergy"),
     *             @OA\Property(property="bp", type="string", description="Blood pressure", example="120/80 mmHg"),
     *             @OA\Property(property="pulse", type="string", description="Pulse", example="72 bpm"),
     *             @OA\Property(property="temperature", type="string", description="Temperature", example="98.6°F"),
     *             @OA\Property(property="spo2", type="string", description="SpO2", example="98%"),
     *             @OA\Property(property="weight", type="string", description="Weight", example="70 kg"),
     *             @OA\Property(property="height", type="string", description="Height", example="175 cm"),
     *             @OA\Property(property="cvs", type="string", description="CVS examination", example="Normal heart sounds, no murmurs"),
     *             @OA\Property(property="rs", type="string", description="RS examination", example="Clear bilateral breath sounds"),
     *             @OA\Property(property="per_abdomen", type="string", description="Per abdomen examination", example="Soft, tender in epigastrium"),
     *             @OA\Property(property="local_examination", type="string", description="Local examination", example="No external abnormalities"),
     *             @OA\Property(property="pr", type="string", description="PR examination", example="Normal tone, no masses"),
     *             @OA\Property(property="dre", type="string", description="DRE examination", example="Normal, no hemorrhoids"),
     *             @OA\Property(property="proctoscopy", type="string", description="Proctoscopy", example="Normal mucosa"),
     *             @OA\Property(property="investigation", type="string", description="Investigation", example="CT abdomen ordered"),
     *             @OA\Property(property="hb", type="string", description="Hemoglobin", example="13.5 g/dL"),
     *             @OA\Property(property="tc", type="string", description="Total count", example="7500 cells/μL"),
     *             @OA\Property(property="esr", type="string", description="ESR", example="15 mm/hr"),
     *             @OA\Property(property="rbs", type="string", description="RBS", example="120 mg/dL"),
     *             @OA\Property(property="bt", type="string", description="Bleeding time", example="2.5 minutes"),
     *             @OA\Property(property="ct", type="string", description="Clotting time", example="5 minutes"),
     *             @OA\Property(property="blood_urea", type="string", description="Blood urea", example="35 mg/dL"),
     *             @OA\Property(property="hiv", type="string", description="HIV", example="Negative"),
     *             @OA\Property(property="hbsag", type="string", description="HBsAg", example="Negative"),
     *             @OA\Property(property="line_of_treatment", type="string", description="Line of treatment", example="Conservative management with IV fluids"),
     *             @OA\Property(property="provisional_diagnosis", type="string", description="Provisional diagnosis", example="Acute gastroenteritis"),
     *             @OA\Property(property="final_diagnosis", type="string", description="Final diagnosis", example="Acute gastroenteritis with dehydration"),
     *             @OA\Property(property="treatment_advised", type="string", description="Treatment advised", example="IV fluids, antiemetics, antibiotics"),
     *             @OA\Property(property="treatment_given", type="string", description="Treatment given", example="Normal saline 1L IV, Ondansetron 4mg"),
     *             @OA\Property(property="preoperative_instruction", type="string", description="Preoperative instruction", example="NPO after midnight, pre-operative labs done")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Preliminary notes created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Preliminary notes created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function create(Request $request, string $ipdId)
    {
        try {
            $notes = $this->IPDpreliminaryNotesService->create($request, $ipdId);
            return $this->successResponse($notes, 'Preliminary notes created successfully', 201);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/preliminary_notes",
     *     summary="Get all preliminary notes",
     *     tags={"Preliminary Notes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ipd_id",
     *         in="query",
     *         required=false,
     *         description="Filter by IPD ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preliminary notes retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Preliminary notes retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function all(Request $request)
    {
        try {
            $notes = $this->IPDpreliminaryNotesService->all($request);
            return $this->successResponse($notes, 'Preliminary notes retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/preliminary_notes/{id}",
     *     summary="Get preliminary notes by IPD ID",
     *     tags={"Preliminary Notes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preliminary notes retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Preliminary notes retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preliminary notes not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            $notes = $this->IPDpreliminaryNotesService->get($id);
            return $this->successResponse($notes, 'Preliminary notes retrieved successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Preliminary notes not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/preliminary_notes/{id}",
     *     summary="Update preliminary notes",
     *     tags={"Preliminary Notes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Preliminary notes ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="chief_complaint", type="string"),
     *             @OA\Property(property="associated_complaint", type="string"),
     *             @OA\Property(property="previous_treatment_history", type="string"),
     *             @OA\Property(property="medical_history", type="string"),
     *             @OA\Property(property="family_history", type="string"),
     *             @OA\Property(property="personal_history", type="string"),
     *             @OA\Property(property="allergy", type="string"),
     *             @OA\Property(property="bp", type="string"),
     *             @OA\Property(property="pulse", type="string"),
     *             @OA\Property(property="temperature", type="string"),
     *             @OA\Property(property="spo2", type="string"),
     *             @OA\Property(property="weight", type="string"),
     *             @OA\Property(property="height", type="string"),
     *             @OA\Property(property="cvs", type="string"),
     *             @OA\Property(property="rs", type="string"),
     *             @OA\Property(property="per_abdomen", type="string"),
     *             @OA\Property(property="local_examination", type="string"),
     *             @OA\Property(property="pr", type="string"),
     *             @OA\Property(property="dre", type="string"),
     *             @OA\Property(property="proctoscopy", type="string"),
     *             @OA\Property(property="investigation", type="string"),
     *             @OA\Property(property="hb", type="string"),
     *             @OA\Property(property="tc", type="string"),
     *             @OA\Property(property="esr", type="string"),
     *             @OA\Property(property="rbs", type="string"),
     *             @OA\Property(property="bt", type="string"),
     *             @OA\Property(property="ct", type="string"),
     *             @OA\Property(property="blood_urea", type="string"),
     *             @OA\Property(property="hiv", type="string"),
     *             @OA\Property(property="hbsag", type="string"),
     *             @OA\Property(property="line_of_treatment", type="string"),
     *             @OA\Property(property="provisional_diagnosis", type="string"),
     *             @OA\Property(property="final_diagnosis", type="string"),
     *             @OA\Property(property="treatment_advised", type="string"),
     *             @OA\Property(property="treatment_given", type="string"),
     *             @OA\Property(property="preoperative_instruction", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preliminary notes updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Preliminary notes updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preliminary notes not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $notes = $this->IPDpreliminaryNotesService->update($request, $id);
            return $this->successResponse($notes, 'Preliminary notes updated successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Preliminary notes not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/preliminary_notes/{id}",
     *     summary="Delete preliminary notes",
     *     tags={"Preliminary Notes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Preliminary notes ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preliminary notes deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Preliminary notes deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preliminary notes not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->IPDpreliminaryNotesService->delete($id);
            return $this->successResponse(null, 'Preliminary notes deleted successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Preliminary notes not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Documents",
 *     description="API endpoints for managing and downloading documents"
 * )
 */
class DocumentDownloadController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/document/download",
     *     summary="Download prescription document",
     *     description="Returns a prescription document view for the patient",
     *     tags={"Documents"},
     *     security={"bearerAuth": {}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Patient ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription document view",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Document generated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function index(Request $request)
    {
        // $patient = Patient::findOrFail($request->id);
        // if (!$patient) {
        //     throw new NotFoundHttpException('Patient data not found');
        // }
        // return view("templates.downloads.ipd_form_part1");
        $patient = [
            'name' => 'John Doe',
            'age' => 30,
            'gender' => 'Male',
            'id' => '1234567890',
            'contact' => '1234567890',
            'address' => '123 Main St, Anytown, USA',
        ];
        $doctor = [
            'name' => 'Dr. John Doe',
            'specialty' => 'Cardiology',
        ];
        $notes = 'notes';
        $diagnosis = 'diagnosis';
        $visit_date = '2023-01-01';
        $prescriptions = [
            ['medicine' => 'medicine1', 'dosage' => 'dosage1', 'frequency' => 'frequency1', 'duration' => 'duration1', 'notes' => 'notes1'],
            ['medicine' => 'medicine2', 'dosage' => 'dosage2', 'frequency' => 'frequency2', 'duration' => 'duration2', 'notes' => 'notes2'],
        ];
        return view("templates.downloads.prescription")->with(compact('patient', 'doctor', 'notes', 'diagnosis', 'visit_date', 'prescriptions'));
    }
}

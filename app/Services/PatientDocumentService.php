<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientDocumentService
{
    private $imageUploadService;

    /**
     * Summary of __construct
     * @param \App\Services\ImageUploadService $imageUploadService
     */
    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Upload document for a patient
     * Handles two scenarios:
     * 1. image[] contains file uploads - create new document rows
     * 2. image[] contains paths - keep existing documents
     * Deletes all documents not in image[] array
     * 
     * @param Request $request
     * @param string $patientId
     * @return PatientDocument|null
     */
    public function uploadDocument(Request $request, string $patientId): ?PatientDocument
    {
        // Verify patient exists
        $patient = Patient::findOrFail($patientId);

        // // Validate image field
        // $validationRules = [
        //     'document_date' => 'nullable|date',
        // ];

        // // Check if image is array or single file
        // if ($request->hasFile('image')) {
        //     if (is_array($request->file('image'))) {
        //         // Array of files
        //         $validationRules['image'] = 'required|array|min:1';
        //         $validationRules['image.*'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,txt,xlsx,xls|max:15360';
        //     } else {
        //         // Single file
        //         $validationRules['image'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,txt,xlsx,xls|max:15360';
        //     }
        // } else {
        //     // Could be array of paths (strings) or single path
        //     $imageInput = $request->input('image');
        //     if (is_array($imageInput)) {
        //         // Array of paths (strings)
        //         $validationRules['image'] = 'required|array|min:1';
        //         $validationRules['image.*'] = 'required|string';
        //     } else {
        //         // Single path (string)
        //         $validationRules['image'] = 'required|string';
        //     }
        // }

        // $request->validate($validationRules);

        // Get image[] array
        \Log::info("Request data".json_encode($request->all()));
        $imageArray = $request->input('image', []);
        if (!is_array($imageArray)) {
            $imageArray = [$imageArray];
        }

        // Separate files from paths
        $filesToUpload = [];
        $pathsToKeep = [];
        $lastDocument = null;

        // Prepare request for image upload service
        $request->merge([
            'modal_type' => 'patient_documents',
            'file_name' => 'document_path',
            'folder_name' => 'patient_documents'
        ]);

        foreach ($imageArray as $index => $item) {

            $isUploadedFile = $request->file('image') && isset($request->file('image')[$index]);

            if ($isUploadedFile) {

                $documentData = [
                    'patient_id'     => $patientId,
                    'document_name'  => $request->document_name ?? "New Document",
                    'document_date'  => $request->document_date ?? now()->toDateString(),
                    'uploaded_by'    => Auth::id(),
                    'document_path'  => ''
                ];

                $patientDocument = PatientDocument::create($documentData);
                $lastDocument = $patientDocument;

                try {
                    $this->imageUploadService->uploadImage($request, $patientDocument->id);
                    $patientDocument->refresh();
                    $pathsToKeep[] = $patientDocument->document_path;
                } catch (\Exception $e) {
                    $patientDocument->delete();
                    throw $e;
                }

            } else {

                if (is_string($item) && !empty($item)) {
                    $pathsToKeep[] = $item;
                }
            }
        }


        // Delete all documents for this patient that are NOT in pathsToKeep
        $existingDocuments = $this->getPatientDocuments($patientId);
        
        foreach ($existingDocuments as $doc) {
            if (!empty($doc->document_path)) {
                $docPaths = explode(',', $doc->document_path);
                $docPaths = array_filter($docPaths);
                
                // Check if ANY of this document's paths are in pathsToKeep
                $shouldKeep = false;
                foreach ($docPaths as $path) {
                    if (in_array($path, $pathsToKeep)) {
                        $shouldKeep = true;
                        break;
                    }
                }
                
                // Delete if not in pathsToKeep
                if (!$shouldKeep) {
                    $this->deleteDocument($doc->id);
                }
            }
        }

        return $lastDocument;
    }

    /**
     * Get all documents for a patient
     * 
     * @param string $patientId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPatientDocuments(string $patientId)
    {
        return PatientDocument::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a specific document
     * 
     * @param string $documentId
     * @return PatientDocument
     */
    public function getDocument(string $documentId): PatientDocument
    {
        return PatientDocument::findOrFail($documentId);
    }

    /**
     * Update document details
     * 
     * @param string $documentId
     * @param Request $request
     * @return PatientDocument
     */
    public function updateDocument(string $documentId, Request $request): PatientDocument
    {
        $document = PatientDocument::findOrFail($documentId);

        $updateData = [];
        
        if ($request->has('document_name')) {
            $updateData['document_name'] = $request->document_name;
        }
       
        if ($request->has('document_date')) {
            $updateData['document_date'] = $request->document_date;
        }

        $document->update($updateData);

        return $document;
    }

    /**
     * Delete a document
     * 
     * @param string $documentId
     * @return void
     */
    public function deleteDocument(string $documentId): void
    {
        $document = PatientDocument::findOrFail($documentId);
        $document->delete();
    }

    /**
     * Get documents by type
     * 
     * @param string $patientId
     * @param string $documentType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDocumentsByType(string $patientId, string $documentType)
    {
        return PatientDocument::where('patient_id', $patientId)
            ->where('document_type', $documentType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get documents by date range
     * 
     * @param string $patientId
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDocumentsByDateRange(string $patientId, string $startDate, string $endDate)
    {
        return PatientDocument::where('patient_id', $patientId)
            ->whereBetween('document_date', [$startDate, $endDate])
            ->orderBy('document_date', 'desc')
            ->get();
    }
}

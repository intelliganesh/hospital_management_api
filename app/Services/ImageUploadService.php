<?php
namespace App\Services;

use App\Enums\ImageService as ImageServiceEnum;
use App\Models\IPD;
use App\Models\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImageUploadService extends GetImageService
{
    private $imageFolders = [
        'test_docs',
        'users_image',
        "expense_image",
        'hospital_image',
        "allopathy_image",
        'proctology_image',
        'consultation_image',
        'user_address_image',
        'non_proctology_image',
        'patient_address_image',
        "hospital_letter_header_image",
        'patient_documents',
        'patient_attendant_address_image',
        'ipd_surgery',
        'ipd_anaesthesia',
        'ipd_pre_operative_checklist',
        'ipd_pre_operative_anaesthesia_evaluation',
        'ipd_department_anaesthesia',
        'ipd_anaesthesia_recover_observation',
        'ipd_discharge_summary',
        'ipd_preliminary_notes',
        'payment_screenshot',

    ];

    protected $imageService;

    /**
     * Summary of __construct
     * @param \App\Services\ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        parent::__construct($imageService);
    }
    public function uploadImage(Request $request, string $id): void
    {
        $modalType = $request->modal_type;

        if ($modalType == "patient_documents") {
            $patientId = $id;
            // Collect uploaded files
            $uploadedFiles = $request->file('image', []);

            // Collect existing paths
            $existingPaths = $request->input('oldImage', []);

            $pathsToKeep = [];
            $i           = 1;
            // ✅ Handle uploaded files
            foreach ($uploadedFiles as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {

                    $upload_path = "images/" . $request->folder_name;
                    // Upload file
                    $extension         = $file->getClientOriginalExtension();
                    $namewithextension = $file->getClientOriginalName();
                    $name              = explode('.', $namewithextension)[0];
                    $name              = preg_replace('/[ .]+/', '_', $name);
                    Log::info('Current i: ' . $i);

                    $fileName = time() . '_' . $i . '_' . $name . '.' . $extension;

                    Log::info($fileName);
                    $file->move(public_path($upload_path), $fileName);
                    $path = $request->folder_name . "/" . $fileName;

                    $document = PatientDocument::create([
                        'patient_id'    => $patientId,
                        'document_name' => $request->document_name ?? "New Document",
                        'document_date' => $request->document_date ?? now()->toDateString(),
                        'uploaded_by'   => Auth::id(),
                        'document_path' => $path,
                    ]);

                    $pathsToKeep[] = $path;
                    Log::info("Path to Keep adasdsd" . json_encode($pathsToKeep));
                    $i++;
                }
            }

            // ✅ Handle existing (old) image paths
            foreach ($existingPaths as $path) {
                if (is_string($path) && trim($path) !== '') {
                    $pathsToKeep[] = $path;
                }
            }
            Log::info("Final Path" . json_encode($pathsToKeep));

            // ✅ Delete documents not in pathsToKeep
            PatientDocument::where('patient_id', $patientId)
                ->whereNotIn('document_path', $pathsToKeep)
                ->delete();

        } else {
            $imageServiceEnum = ImageServiceEnum::from($modalType);
            $modelClass       = $imageServiceEnum->model();
            $model            = $modelClass::findOrFail($id);
            $imageField       = $request->file_name;

            $fileName   = 'image';
            $folderName = $request->folder_name;
            if (! in_array($request->folder_name, $this->imageFolders)) {
                throw new \Exception("Invalid image folder: {$request->folder_name}");
            }
            if (! array_key_exists($imageField, $model->getAttributes())) {
                throw new \Exception("Invalid image field: {$imageField}");
            }
            if ($modalType == "ipd_surgery" || $modalType == "ipd_pre_operative_checklist" || $modalType == "ipd_pre_operative_anaesthesia_evaluation" || $modalType == "ipd_department_anaesthesia" || $modalType == "ipd_anaesthesia" || $modalType == "ipd_anaesthesia_recover_observation" || $modalType == "ipd_discharge_summary" || $modalType == "ipd_preliminary_notes") {
                $ipd        = IPD::find($model->ipd_id);
                $folderName = "app/public/pdfs/ipd/{$ipd->ipd_number}/uploads";
            }

            $model->setAttribute($imageField, $this->convertImage('add', $request, $fileName, $folderName));
            $model->save();
        }
    }
}

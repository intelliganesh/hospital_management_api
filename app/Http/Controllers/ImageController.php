<?php
namespace App\Http\Controllers;

use App\Services\CheckValidation;
use App\Services\ImageUploadService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Images",
 *     description="API endpoints for managing image uploads"
 * )
 */
class ImageController extends Controller
{
    use ResponseTrait;

    protected $imageUploadService;
    protected $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\ImageUploadService $imageUploadService
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(ImageUploadService $imageUploadService, CheckValidation $checkValidationService)
    {
        $this->imageUploadService     = $imageUploadService;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * @OA\Post(
     *     path="/api/images",
     *     tags={"Images"},
     *     summary="Update image for a given ID",
     *     description="Uploads a new image for the specified ID",
     *     operationId="updateImage",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"id", "image", "modal_type", "folder_name", "file_name"},
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     description="ID of the resource to update",
     *                     example="1"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="modal_type",
     *                     type="string",
     *                     description="Model type associated with the image"
     *                 ),
     *                 @OA\Property(
     *                     property="file_name",
     *                     type="string",
     *                     description="File name to save the image as"
     *                 ),
     *                 @OA\Property(
     *                     property="folder_name",
     *                     type="string",
     *                     description="Target folder to save the image"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Image uploaded successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="file_path", type="string", example="storage/images/patients/patient_1.jpg"),
     *                 @OA\Property(property="file_url", type="string", example="http://example.com/storage/images/patients/patient_1.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="image", type="array", @OA\Items(type="string"), example={"The image field is required."}),
     *                 @OA\Property(property="id", type="array", @OA\Items(type="string"), example={"The id field is required."})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function image(Request $request)
    {
        try {
            $validationArray = [
                'id'          => 'required',
                'file_name'   => 'required',
                'modal_type'  => 'required',
                'folder_name' => 'required',
            ];
            // if (is_array($request->image)) {
            //     $validationArray = ['image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:2048'];
            // } else {
            //     $validationArray = ['image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:2048'];
            //     // 'image' => 'required|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            // }

            if (is_array($request->image)) {
                foreach ($request->image as $index => $item) {
                    if ($item instanceof UploadedFile && $item->isValid()) {
                        $validationArray["image.$index"] = 'sometimes|required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx|max:15360';
                    } elseif (is_string($item)) {
                        $validationArray["image.$index"] = 'sometimes|required|string';
                    } else {
                        $validationArray["image.$index"] = 'nullable';
                    }
                }
                // foreach ($request->image as $index => $item) {
                //     if ($item instanceof \Illuminate\Http\UploadedFile) {
                //         $validationArray["image.$index"] = 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:2048';
                //     } elseif (is_string($item)) {
                //         $validationArray["image.$index"] = 'sometimes|required|string';
                //     } else {
                //         $validationArray["image.$index"] = 'nullable';
                //     }
                // }
            } else {
                // Single image file (not array)
                if ($request->hasFile('image')) {
                    $validationArray['image'] = 'sometimes|required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx|max:15360';
                } elseif (is_string($request->image)) {
                    $validationArray['image'] = 'sometimes|required|string';
                } else {
                    $validationArray['image'] = 'sometimes|required';
                }
            }

            $validator = Validator::make($request->all(), $validationArray);
            $this->checkValidationService->checkValidation($validator);
            $this->imageUploadService->uploadImage($request, $request->id);
            return $this->successResponse();

        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

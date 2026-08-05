<?php
namespace App\Services;

use App\Services\ImageService;
use Illuminate\Http\Request;

class GetImageService
{

    private $imageService;

    /**
     * Summary of __construct
     * @param \App\Services\ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Summary of convertImage
     * @return null|string
     * @param mixed $fileName
     * @param mixed $pathName
     * @param mixed $methodType
     * @param \Illuminate\Http\Request $request
     */
    protected function convertImage($methodType = 'add', Request $request, $fileName, $pathName = 'images'): null | string
    {
        return $this->sendImage($request, $fileName, $pathName);
    }

    /**
     * Summary of sendImage
     * @param mixed $request
     * @param mixed $fileName
     * @param mixed $pathName
     * @return string
     */
    private function sendImage($request, $fileName, $pathName): string
    {

        $result = [];

        // Handle oldImage[] — image URLs sent from frontend
        $oldImages = $request->get('oldImage');
        // if ($oldImages && gettype($oldImages) === 'string') {
        if (! empty($oldImages)) {
            if (is_array($oldImages)) {
                $result = array_merge($result, $oldImages);
            } else {
                $result[] = $oldImages;
            }
        }

                                                    // Handle newly uploaded files
        $uploadedFiles = $request->file($fileName); // This gets image[] as array

        if ($uploadedFiles instanceof \Illuminate\Http\UploadedFile) {
            // Single file
            $result[] = $this->imageService->imageUpload($request, $fileName, $pathName);
        } elseif (is_array($uploadedFiles)) {
                                                             // Multiple files
            $request->files->set($fileName, $uploadedFiles); // Reset with clean file list
            $uploadedPaths = $this->imageService->multipleImageUpload($request, $fileName, $pathName);
            $result        = array_merge($result, $uploadedPaths);
        } else {
            $result = $request->input($fileName);
        }

        return implode(',', $result);
    }
}

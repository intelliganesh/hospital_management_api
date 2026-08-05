<?php

namespace App\Services;

use Exception;

class ImageService
{
    private $image = null;
    private $multipleArray = [];
    /**
     * Summary of image
     * @param mixed $file
     * @param string $path
     * @return string
     */
    private function image($file, string $path = "images")
    {
        $extension = $file->getClientOriginalExtension();
        $namewithextension = $file->getClientOriginalName();
        $name = explode('.', $namewithextension)[0];
        $fileName = time() . '_' . $name . '.' . $extension;
        $file->move(public_path("images/" . $path), $fileName);
        $this->image = $path . "/" . $fileName;
        return $this->image;
    }

    /**
     * Summary of imageUpload
     * @param mixed $data
     * @param string $fieldName
     * @param mixed $path
     * @return string
     */
    public function imageUpload($data, string $fieldName = 'image', $path): string|null
    {
        if (!$fieldName) {
            throw new Exception("Field name is empty");
        }
        $file = $data->file($fieldName);
        return $this->image($file, $path);
    }
    /**
     * Summary of multipleImageUpload
     * @param mixed $data
     * @param string $fieldName
     * @param mixed $path
     * @return string[]
     */
    public function multipleImageUpload($data, string $fieldName = 'image', $path)
    {

        $file = $data->file($fieldName);
        foreach ($file as $item) {
            $this->multipleArray[] = $this->image($item, $path);
        }
        return $this->multipleArray;
    }
}
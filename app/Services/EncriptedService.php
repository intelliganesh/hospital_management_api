<?php
namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class EncriptedService
{
    /**
     * Summary of encreption
     * @param string $value
     * @return array{id_number_encrypted: string, id_number_masked: string}
     */
    public function encreption(string $value): mixed
    {
        return $this->setIdNumberAttribute($value);
    }

    /**
     * Summary of setIdNumberAttribute
     * @param string $value
     * @return array{id_number_encrypted: string, id_number_masked: string}
     */
    private function setIdNumberAttribute(string $value): mixed
    {
        return [
            'id_number_masked'    => $this->maskId($value),
            'id_number_encrypted' => Crypt::encryptString($value),
        ];
    }

    /**
     * Summary of decrypt
     * @param string $encryptedValue
     * @return string
     */
    public function decrypt(string $encryptedValue): string
    {
        return Crypt::decryptString($encryptedValue);
    }
    /**
     * Summary of maskId
     * @param mixed $number
     * @return string
     */
    private function maskId($number): string
    {
        $len = strlen($number);
        return str_repeat('X', $len - 4) . substr($number, -4);
    }
}

<?php
namespace App\Traits;

use Exception;
trait EncryAndDecryTrait
{
    protected static $options           = 0;
    protected static $expirationMinutes = 60;
    protected static $ciphering         = "AES-128-CTR";
    protected static $encryption_iv     = '1234567891011121';

    protected function encrypt($data)
    {
        $encryption_key  = config('services.hospital_app.encrypt_key');
        $expiryTimestamp = now()->addMinutes(self::$expirationMinutes)->timestamp;
        $dataToEncrypt   = $data->email . '|' . $expiryTimestamp;
        $encrypted       = openssl_encrypt($dataToEncrypt, self::$ciphering, $encryption_key, self::$options, self::$encryption_iv);
        $encrypted       = base64_encode($encrypted);
        return $encrypted;
    }

    protected function decrypt($token)
    {
        $encryption_key = config('services.hospital_app.encrypt_key');
        $decoded        = base64_decode($token);
        $decrypted      = openssl_decrypt($decoded, self::$ciphering, $encryption_key, self::$options, self::$encryption_iv);
        if (! $decrypted || strpos($decrypted, '|') === false) {
            throw new Exception("Invalid or corrupted token.");
        }
        return explode('|', $decrypted);
    }
}

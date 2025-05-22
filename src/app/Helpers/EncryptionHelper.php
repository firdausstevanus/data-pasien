<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
    /**
     * Mengenkripsi data sensitif
     *
     * @param string $value
     * @return string|null
     */
    public static function encrypt($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return Crypt::encryptString($value);
    }

    /**
     * Mendekripsi data terenkripsi
     *
     * @param string $encryptedValue
     * @return string|null
     */
    public static function decrypt($encryptedValue)
    {
        if (empty($encryptedValue)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($encryptedValue);
        } catch (\Exception $e) {
            return null;
        }
    }
}

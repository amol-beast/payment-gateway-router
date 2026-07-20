<?php

namespace App\Classes;

class Encryption
{

    static public function encrypt($data,$secret_key)
    {
        $cipher = 'aes-256-gcm';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);

        // Encrypt the data and capture the authentication tag
        $ciphertext = openssl_encrypt(
            json_encode($data),
            $cipher,
            $secret_key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        // Package IV, Tag, and Ciphertext together, then encode to Base64 for storage
        $encryptedData = base64_encode($iv . $tag . $ciphertext);
        return $encryptedData;
    }

    static public function decrypt($data,$secretKey)
    {
        $payload = base64_decode($data);
        $cipher = "aes-256-gcm";
        $ivLength = openssl_cipher_iv_length($cipher);

        // AES-256-GCM uses a standard 16-byte authentication tag
        $tagLength = 16;


        // Extract the component segments out of the unified binary string
        $iv = substr($payload, 0, $ivLength);
        $tag = substr($payload, $ivLength, $tagLength);
        $ciphertext = substr($payload, $ivLength + $tagLength);


        // Perform decryption and verify the authenticity tag
        $decryptedData = openssl_decrypt(
            $ciphertext,
            $cipher,
            $secretKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return $decryptedData;
    }
}

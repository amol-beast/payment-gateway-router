<?php

namespace App\Classes;

class Encryption
{
    public static function encrypt(mixed $data, string $secret_key): string
    {
        $cipher = 'aes-256-gcm';
        $ivLength = openssl_cipher_iv_length($cipher);

        if ($ivLength < 1) {
            throw new \RuntimeException('Unable to determine IV length for cipher.');
        }

        $iv = random_bytes($ivLength);

        $payload = json_encode($data);

        if ($payload === false) {
            throw new \RuntimeException('Unable to encode data for encryption.');
        }

        $tag = '';

        // Encrypt the data and capture the authentication tag
        $ciphertext = openssl_encrypt(
            $payload,
            $cipher,
            $secret_key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Encryption failed.');
        }

        // Package IV, Tag, and Ciphertext together, then encode to Base64 for storage
        return base64_encode($iv.$tag.$ciphertext);
    }

    public static function decrypt(string $data, string $secretKey): string
    {
        $payload = base64_decode($data);
        $cipher = 'aes-256-gcm';
        $ivLength = openssl_cipher_iv_length($cipher);

        if ($ivLength < 1) {
            throw new \RuntimeException('Unable to determine IV length for cipher.');
        }

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

        if ($decryptedData === false) {
            throw new \RuntimeException('Decryption failed.');
        }

        return $decryptedData;
    }
}

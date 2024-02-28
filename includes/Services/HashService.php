<?php

namespace Paydock\Services;

use Paydock\PaydockPlugin;

class HashService
{
    private const CIPHER = 'AES-128-CBC';
    private const OPTION = OPENSSL_RAW_DATA;

    public static function encrypt(string $string): string
    {
        $ivlen = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, self::CIPHER, self::getKey(), self::OPTION, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, self::getKey(), true);

        return base64_encode($iv.$hmac.$ciphertext_raw);
    }

    public static function decrypt(string $string): string
    {
        $c = base64_decode($string);
        $ivlen = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, self::CIPHER, self::getKey(), self::OPTION, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, self::getKey(), true);

        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }

       return $string;
    }

    private static function getKey(): string
    {
        if (defined('AUTH_KEY')) {
            return AUTH_KEY;
        }

        return PaydockPlugin::PLUGIN_PREFIX;
    }
}

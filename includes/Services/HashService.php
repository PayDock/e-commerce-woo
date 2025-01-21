<?php

namespace PowerBoard\Services;

use Exception;

class HashService {

	private const CIPHER       = 'aes-128-cbc';
	private const OPTION       = OPENSSL_RAW_DATA;
	private const NONCE_LENGTH = 24;
	private const KEY_LENGTH   = 32;

	/**
	 * Encrypts a string using sodium - if available - or openssl
	 *
	 * @throws Exception If there is no data encryption module or sodium encryption available
	 */
	public static function encrypt( ?string $string_to_encrypt ): ?string {

		if (
			$string_to_encrypt === null ||
			strpos( $string_to_encrypt, 'sodium:' ) !== false ||
			strpos( $string_to_encrypt, 'openssl:' ) !== false
		) {
			return $string_to_encrypt;
		}

		if ( function_exists( 'sodium_crypto_secretbox' ) ) {
			try {
				$key        = self::get_key( self::KEY_LENGTH );
				$nonce      = random_bytes( self::NONCE_LENGTH );
				$ciphertext = sodium_crypto_secretbox( $string_to_encrypt, $nonce, $key );

				return 'sodium:' . base64_encode( $nonce . $ciphertext ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			} catch ( Exception $error ) {
				throw $error;
			}
		} elseif ( function_exists( 'openssl_encrypt' ) ) {

			$ivlen          = openssl_cipher_iv_length( self::CIPHER );
			$iv             = openssl_random_pseudo_bytes( $ivlen );
			$key            = self::get_key( 16 );
			$ciphertext_raw = openssl_encrypt( $string_to_encrypt, self::CIPHER, $key, self::OPTION, $iv );
			$hmac           = hash_hmac( 'sha256', $ciphertext_raw, $key, true );

			return 'openssl:' . base64_encode( $iv . $hmac . $ciphertext_raw ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		} else {
			throw new Exception( 'There is no available data encryption module.' );
		}
	}

	/**
	 * Decrypts a string based on the encryption module used to encrypt
	 *
	 * @throws Exception If sodium decryption not available
	 */
	public static function decrypt( ?string $string_to_decrypt ): string {

		if ( $string_to_decrypt === null ) {
			return '';
		}

		if ( strpos( $string_to_decrypt, 'sodium:' ) === 0 ) {
			$string_to_decrypt = substr( $string_to_decrypt, 7 );
			$decoded           = base64_decode( $string_to_decrypt ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$nonce             = substr( $decoded, 0, self::NONCE_LENGTH );
			$ciphertext        = substr( $decoded, self::NONCE_LENGTH );
			$key               = self::get_key( self::KEY_LENGTH );
			try {
				$plaintext = sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );

				if ( $plaintext === false ) {
					return $string_to_decrypt;
				}

				return $plaintext;
			} catch ( Exception $error ) {
				throw $error;
			}
		} elseif ( strpos( $string_to_decrypt, 'openssl:' ) === 0 ) {
			$string_to_decrypt  = substr( $string_to_decrypt, 8 );
			$c                  = base64_decode( $string_to_decrypt ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$ivlen              = openssl_cipher_iv_length( self::CIPHER );
			$iv                 = substr( $c, 0, $ivlen );
			$hmac               = substr( $c, $ivlen, $sha2len = 32 );
			$ciphertext_raw     = substr( $c, $ivlen + $sha2len );
			$key                = self::get_key( 16 );
			$original_plaintext = openssl_decrypt( $ciphertext_raw, self::CIPHER, $key, self::OPTION, $iv );
			$calcmac            = hash_hmac( 'sha256', $ciphertext_raw, $key, true );

			if ( $original_plaintext === false ) {
				return $string_to_decrypt;
			}

			if ( hash_equals( $hmac, $calcmac ) ) {
				return $original_plaintext;
			}

			return $string_to_decrypt;

		} else {
			return $string_to_decrypt;
		}
	}

	private static function get_key( int $length ): string {

		if ( defined( 'AUTH_KEY' ) ) {
			$key_material = AUTH_KEY;
		} else {
			$key_material = PLUGIN_PREFIX;
		}

		return substr( hash( 'sha256', $key_material, true ), 0, $length );
	}
}

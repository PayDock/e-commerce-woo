<?php

namespace PowerBoard\Services;

class HashService {

	private const CIPHER = 'aes-128-cbc';
	private const OPTION = OPENSSL_RAW_DATA;
	private const NONCE_LENGTH = 24;
	private const KEY_LENGTH = 32;

	public static function encrypt( ?string $string ): ?string {

		if (
			$string == null ||
			strpos( $string, 'sodium:' ) !== false ||
			strpos( $string, 'openssl:' ) !== false
		) {
			return $string;
		}

		if ( function_exists( 'sodium_crypto_secretbox' ) ) {

			$key = self::get_key( self::KEY_LENGTH );
			$nonce = random_bytes( self::NONCE_LENGTH );
			$ciphertext = sodium_crypto_secretbox( $string, $nonce, $key );

			return 'sodium:' . base64_encode( $nonce . $ciphertext );

		} elseif ( function_exists( 'openssl_encrypt' ) ) {

			$ivlen = openssl_cipher_iv_length( self::CIPHER );
			$iv = openssl_random_pseudo_bytes( $ivlen );
			$key = self::get_key( 16 );
			$ciphertext_raw = openssl_encrypt( $string, self::CIPHER, $key, self::OPTION, $iv );
			$hmac = hash_hmac( 'sha256', $ciphertext_raw, $key, true );

			return 'openssl:' . base64_encode( $iv . $hmac . $ciphertext_raw );

		} else {

			throw new \Exception( 'There is no available data encryption module.' );

		}

	}

	public static function decrypt( ?string $string ): string {

		if ( $string === null ) {
			return '';
		}

		if ( strpos( $string, 'sodium:' ) === 0 ) {

			$string = substr( $string, 7 );
			$decoded = base64_decode( $string );
			$nonce = substr( $decoded, 0, self::NONCE_LENGTH );
			$ciphertext = substr( $decoded, self::NONCE_LENGTH );
			$key = self::get_key( self::KEY_LENGTH );
			$plaintext = sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );

			if ( $plaintext === false ) {
				return $string;
			}

			return $plaintext;

		} elseif ( strpos( $string, 'openssl:' ) === 0 ) {

			$string = substr( $string, 8 );
			$c = base64_decode( $string );
			$ivlen = openssl_cipher_iv_length( self::CIPHER );
			$iv = substr( $c, 0, $ivlen );
			$hmac = substr( $c, $ivlen, $sha2len = 32 );
			$ciphertext_raw = substr( $c, $ivlen + $sha2len );
			$key = self::get_key( 16 );
			$original_plaintext = openssl_decrypt( $ciphertext_raw, self::CIPHER, $key, self::OPTION, $iv );
			$calcmac = hash_hmac( 'sha256', $ciphertext_raw, $key, true );

			if ( false === $original_plaintext ) {
				return $string;
			}

			if ( hash_equals( $hmac, $calcmac ) ) {
				return $original_plaintext;
			}

			return $string;

		} else {

			return $string;

		}

	}

	private static function get_key( int $length ): string {

		if ( defined( 'AUTH_KEY' ) ) {
			$keyMaterial = AUTH_KEY;
		} else {
			$keyMaterial = PLUGIN_PREFIX;
		}

		return substr( hash( 'sha256', $keyMaterial, true ), 0, $length );

	}

}

<?php

namespace PowerBoard\Services;

use PowerBoard\PowerBoardPlugin;
use phpseclib3\Crypt\AES;

class HashService {

	private const CIPHER = 'aes-128-cbc';
	private const OPTION = OPENSSL_RAW_DATA;

	public static function encrypt( string $string ): string {

		if ( function_exists( 'openssl_encrypt' ) ) {

			$ivlen = openssl_cipher_iv_length( self::CIPHER );
			$iv = openssl_random_pseudo_bytes( $ivlen );
			$ciphertext_raw = openssl_encrypt( $string, self::CIPHER, self::getKey(), self::OPTION, $iv );
			$hmac = hash_hmac( 'sha256', $ciphertext_raw, self::getKey(), true );

			return base64_encode( $iv . $hmac . $ciphertext_raw );

		} else {

			$aes = new AES( 'cbc' );
			$aes->setKey( self::getKey() );
			$iv = self::getIV();
			$aes->setIV( $iv );
			$ciphertext_raw = $aes->encrypt( $string );
			$hmac = hash_hmac( 'sha256', $ciphertext_raw, self::getKey(), true );

			return base64_encode( $iv . $hmac . $ciphertext_raw );

		}

	}

	public static function decrypt( string $string ): string {

		if ( function_exists( 'openssl_decrypt' ) ) {

			$c = base64_decode( $string );
			$ivlen = openssl_cipher_iv_length( self::CIPHER );
			$iv = substr( $c, 0, $ivlen );
			$hmac = substr( $c, $ivlen, $sha2len = 32 );
			$ciphertext_raw = substr( $c, $ivlen + $sha2len );
			$original_plaintext = openssl_decrypt( $ciphertext_raw, self::CIPHER, self::getKey(), self::OPTION, $iv );
			$calcmac = hash_hmac( 'sha256', $ciphertext_raw, self::getKey(), true );

			if ( false === $original_plaintext ) {
				return $string;
			}

			if ( hash_equals( $hmac, $calcmac ) ) {
				return $original_plaintext;
			}

			return $string;

		} else {

			$c = base64_decode( $string );
			$iv = substr( $c, 0, 16 );
			$hmac = substr( $c, 16, 32 );
			$ciphertext_raw = substr( $c, 48 );
			$aes = new AES( 'cbc' );
			$aes->setKey( self::getKey() );
			$aes->setIV( $iv );
			$original_plaintext = $aes->decrypt( $ciphertext_raw );
			$calcmac = hash_hmac( 'sha256', $ciphertext_raw, self::getKey(), true );

			if ( false === $original_plaintext ) {
				return $string;
			}

			if ( hash_equals( $hmac, $calcmac ) ) {
				return $original_plaintext;
			}

			return $string;

		}


	}

	private static function getKey(): string {

		if ( defined( 'AUTH_KEY' ) ) {
			return AUTH_KEY;
		}

		return PowerBoardPlugin::PLUGIN_PREFIX;

	}

	private static function getIV(): string {
		return substr( hash( 'sha256', self::getKey() ), 0, 16 );
	}

}

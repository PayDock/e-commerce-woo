<?php
declare( strict_types=1 );

namespace unit;

use PHPUnit\Framework\TestCase;
use WooPlugin\API\ChargeService;
use WooPlugin\Services\HashService;

class HashServiceTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PLUGIN_PREFIX' ) ) {
			define( 'PLUGIN_PREFIX', 'plugin_prefix_mock' );
		}
	}

	public function test_encrypt() {
		$string_to_encrypt = 'test';
		$actual            = HashService::encrypt( $string_to_encrypt );

		$this->assertNotEquals( $string_to_encrypt, $actual, 'Should return the encrypted string.' );
	}

	public function test_encrypt_decrypt() {
		$string_to_encrypt = 'test';
		$expected          = $string_to_encrypt;
		$encrypted_string  = HashService::encrypt( $string_to_encrypt );
		var_dump( $encrypted_string );
		$actual = HashService::decrypt( $encrypted_string );

		$this->assertEquals( $expected, $actual, 'Should return the initial string.' );
	}

	public function test_encrypt_encrypted_string() {
		$string_to_encrypt = 'sodium:VYf/G6m7o08f/v5553+R+0lwPriERAJqypnhJ3d0KMf3Rnh/FzlJ2oRvEec=';
		$expected          = $string_to_encrypt;
		$actual            = HashService::encrypt( $string_to_encrypt );

		$this->assertEquals( $expected, $actual, 'Should not encrypt the string again.' );
	}

	public function test_decrypt_null_string() {
		$string_to_decrypt = null;
		$expected          = '';
		$actual            = HashService::decrypt( $string_to_decrypt );

		$this->assertEquals( $expected, $actual, 'Should return an empty string.' );
	}

	public function test_decrypt() {
		$string_to_decrypt = 'sodium:Mb/Pl7+FmZt9bbQeulnrQYBV/MNhogLRLXuXCv3k1/ay4kLnJ6ilFh7/wmI=';
		$expected          = 'test';
		$actual            = HashService::decrypt( $string_to_decrypt );

		$this->assertEquals( $expected, $actual, 'Should the string decrypted.' );
	}
}

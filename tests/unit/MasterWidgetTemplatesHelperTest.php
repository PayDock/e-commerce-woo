<?php
declare( strict_types=1 );

namespace unit;

use PHPUnit\Framework\TestCase;
use PowerBoard\Helpers\MasterWidgetTemplatesHelper;

class MasterWidgetTemplatesHelperTest extends TestCase {

	public function test_map_templates_error() {
		$actual = MasterWidgetTemplatesHelper::map_templates( [ 'error' => 'error' ], true );
		$this->assertSame( [], $actual );
	}

	public function test_map_templates_empty() {
		$actual = MasterWidgetTemplatesHelper::map_templates( [], false );
		$this->assertSame( [], $actual );
	}

	public function test_map_templates_mandatory() {
		$actual   = MasterWidgetTemplatesHelper::map_templates(
			[
				[
					'_id'   => '1234',
					'label' => 'Test template 1',
				],
				[
					'_id'   => '5678',
					'label' => 'Test template 2',
				],
				[
					'_id'   => '9123',
					'label' => 'Test template 3',
				],
			],
			false
		);
		$expected = [
			''     => 'Select a template ID',
			'1234' => 'Test template 1 | 1234',
			'5678' => 'Test template 2 | 5678',
			'9123' => 'Test template 3 | 9123',
		];
		$this->assertSame( $expected, $actual );
	}

	public function test_map_templates_optional() {
		$actual   = MasterWidgetTemplatesHelper::map_templates(
			[
				[
					'_id'   => '1234',
					'label' => 'Test template 1',
				],
				[
					'_id'   => '5678',
					'label' => 'Test template 2',
				],
				[
					'_id'   => '9123',
					'label' => 'Test template 3',
				],
			],
			false,
			true
		);
		$expected = [
			''     => 'Unselect template ID',
			'1234' => 'Test template 1 | 1234',
			'5678' => 'Test template 2 | 5678',
			'9123' => 'Test template 3 | 9123',
		];
		$this->assertSame( $expected, $actual );
	}

	public static function validate_template_id( ?array $templates, bool $has_error, string $template_type_key, array $settings ): array {
		$invalid_key = false;
		if ( ! empty( $settings ) ) {
			$selected_template = ! empty( $settings[ $template_type_key ] ) ? $settings[ $template_type_key ] : [];
			if ( ! empty( $selected_template ) && ( $has_error || empty( $templates ) || ! array_key_exists( $selected_template, $templates ) ) ) {
				$settings[ $template_type_key ] = '';
				$invalid_key                    = true;
			}
		}

		return [
			'settings'    => $settings,
			'invalid_key' => $invalid_key,
		];
	}
	public function test_empty_settings_validate_template_id(): void {
		$templates         = [
			'test_template_id' => 'test_template_name | test_template_id',
		];
		$has_error         = false;
		$template_type_key = 'test_template_key';
		$settings          = [];
		$actual            = MasterWidgetTemplatesHelper::validate_template_id( $templates, $has_error, $template_type_key, $settings );
		$expected          = [
			'settings'    => $settings,
			'invalid_key' => false,
		];
		$this->assertSame( $expected, $actual );
	}

	public function test_empty_templates_validate_template_id(): void {
		$templates         = [];
		$has_error         = false;
		$template_type_key = 'test_template_key';
		$settings          = [
			'test_template_key' => 'test_template_id',
		];
		$actual            = MasterWidgetTemplatesHelper::validate_template_id( $templates, $has_error, $template_type_key, $settings );
		$expected          = [
			'settings'    => [
				'test_template_key' => '',
			],
			'invalid_key' => true,
		];
		$this->assertSame( $expected, $actual );
	}

	public function test_valid_validate_template_id(): void {
		$templates         = [
			'test_template_id' => 'test_template_name | test_template_id',
		];
		$has_error         = false;
		$template_type_key = 'test_template_key';
		$settings          = [
			'test_template_key' => 'test_template_id',
		];
		$actual            = MasterWidgetTemplatesHelper::validate_template_id( $templates, $has_error, $template_type_key, $settings );
		$expected          = [
			'settings'    => $settings,
			'invalid_key' => false,
		];
		$this->assertSame( $expected, $actual );
	}

	public function test_invalid_validate_template_id(): void {
		$templates         = [
			'test_template_id' => 'test_template_name | test_template_id',
		];
		$has_error         = true;
		$template_type_key = 'test_template_key';
		$settings          = [
			'test_template_key' => 'test_template_invalid_id',
		];
		$actual            = MasterWidgetTemplatesHelper::validate_template_id( $templates, $has_error, $template_type_key, $settings );
		$expected          = [
			'settings'    => [
				'test_template_key' => '',
			],
			'invalid_key' => false,
		];
		$this->assertSame( $expected, $actual );
	}

	public function test_template_error_validate_template_id(): void {
		$templates         = [
			'test_template_id' => 'test_template_name | test_template_id',
		];
		$has_error         = true;
		$template_type_key = 'test_template_key';
		$settings          = [
			'test_template_key' => 'test_template_id',
		];
		$actual            = MasterWidgetTemplatesHelper::validate_template_id( $templates, $has_error, $template_type_key, $settings );
		$expected          = [
			'settings'    => [
				'test_template_key' => '',
			],
			'invalid_key' => true,
		];
		$this->assertSame( $expected, $actual );
	}
}

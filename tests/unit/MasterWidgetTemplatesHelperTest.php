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
}

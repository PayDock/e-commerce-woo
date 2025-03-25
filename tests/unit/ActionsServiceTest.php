<?php

declare( strict_types=1 );

namespace unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PowerBoard\Services\ActionsService;

class ActionsServiceTest extends TestCase {
	protected MockObject $actions_service;
	public function setUp(): void {
		$this->actions_service = $this->createMock( ActionsService::class );
		// Mock a WC_Order object
		$order = $this->createMock( WC_Order::class );

		$order->method( 'get_id' )->willReturn( 1234 );
		$order->method( 'get_status' )->willReturn( 'completed' );
		$order->method( 'get_total' )->willReturn( 99.99 );
		$order->method( 'get_total_refunded' )->willReturn( 30.99 );
		$order->method( 'get_currency' )->willReturn( 30.99 );
		$_POST['order_id'] = 1234;
	}

	public function tearDown(): void {
		$_POST = [];
	}

	public function test_filter_refund_message() {
		// $product->method('get_price')->willReturn(0);
		$translation = 'Invalid refund amount';
		$text        = 'Invalid refund amount';
		$domain      = 'power-board';

		$expected = 'Invalid refund amount. Available amount: %s';
		$filtered = $this->actions_service->powerboard_filter_refund_message( $translation, $text, $domain );
		var_dump( $filtered );
		$this->assertEquals( $expected, $filtered );
	}
}

<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class OtherPaymentMethods extends AbstractEnum {
	protected const AFTERPAY = 'AFTERPAY';
	protected const ZIPPAY = 'ZIPPAY';

	public function getLabel(): string {
		switch ( $this->name ) {
			case self::ZIPPAY:
				return 'Zip';
			case self::AFTERPAY:
				return 'Afterpay v1';
			default:
				return '';
		}
	}

	public function getId(): string {
		switch ( $this->name ) {
			case self::ZIPPAY:
				return 'zip';
			case self::AFTERPAY:
				return 'afterpay';
			default:
				return '';
		}
	}
}

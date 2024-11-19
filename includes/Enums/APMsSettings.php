<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class APMsSettings extends AbstractEnum {

	protected const ENABLE = 'ENABLE';
	protected const GATEWAY_ID = 'GATEWAY_ID';
	protected const FRAUD = 'FRAUD';
	protected const FRAUD_SERVICE_ID = 'FRAUD_SERVICE_ID';
	protected const DIRECT_CHARGE = 'DIRECT_CHARGE';
	protected const SAVE_CARD = 'SAVE_CARD';
	protected const SAVE_CARD_OPTION = 'SAVE_CARD_OPTION';

	public function getInputType(): string {
		switch ( $this->name ) {
			case self::GATEWAY_ID:
			case self::FRAUD_SERVICE_ID:
				return 'password';
			case self::ENABLE:
			case self::FRAUD:
			case self::DIRECT_CHARGE:
			case self::SAVE_CARD:
				return 'checkbox';
			case self::SAVE_CARD_OPTION:
				return 'select';
			default:
				return '';
		}
	}

	public function getLabel(): string {
		switch ( $this->name ) {
			case self::FRAUD:
			case self::ENABLE:
				return ucfirst( strtolower( $this->name ) );
			case self::GATEWAY_ID:
				return 'Gateway ID';
			case self::DIRECT_CHARGE:
				return 'Direct Charge';
			case self::FRAUD_SERVICE_ID:
				return 'Fraud service ID';
			case self::SAVE_CARD:
				return 'Save card';
			case self::SAVE_CARD_OPTION:
				return 'Save card option';
			default:
				return '';
		}
	}

	public function getDescription(): string {
		switch ( $this->name ) {
			case self::DIRECT_CHARGE:
				return 'Direct charge stands for authorization and capture in a single request';

			case self::SAVE_CARD:
				return 'Offer your customer the option to save the card information permanently at PowerBoard for further usage';

			default:
				return '';
		}
	}

	public function getInputAttributes(): array {

		$attributes = [];

		if ( $this->getInputType() === 'password' ) {
			$attributes['autocomplete'] = 'off';
		}

		return $attributes;

	}

}

<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class SettingGroups extends AbstractEnum {
	protected const CREDENTIALS = 'CREDENTIALS';
	protected const ADVANCED = 'ADVANCED';
	protected const CARD = 'CARD';
	protected const BANK_ACCOUNT = 'BANK_ACCOUNT';
	protected const WALLETS = 'WALLETS';
	protected const A_P_M_S = 'A_P_M_S';

	public static function cases(): array {
		$items = parent::cases();

		return array_filter( $items, function ($item) {
			return self::BANK_ACCOUNT()->name !== $item->name;
		} );
	}

	public function getLabel(): string {
		switch ( $this->name ) {
			case self::CARD:
				return 'Cards';
			case self::WALLETS:
				return 'Wallets:';
			case self::A_P_M_S:
				return 'APMs:';
			case self::BANK_ACCOUNT:
				return 'Bank account:';
			case self::CREDENTIALS:
				return 'API Credential:';
			case self::ADVANCED:
				return 'Advanced';
			default:
				return '';
		}
	}
}

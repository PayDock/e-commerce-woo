<?php

namespace PowerBoard\Helpers;

use Exception;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\SDKAdapterService;

class VaultTokenHelper {
	private $args = [];

	public function __construct( $args ) {
		$this->args = $args;
	}

	public function get( $additionalFields = [] ) {
		$vaultToken = ! empty( $this->args['selectedtoken'] ) ? $this->args['selectedtoken'] : null;
		$OTTtoken   = ! empty( $this->args['paymentsourcetoken'] ) ? $this->args['paymentsourcetoken'] : null;

		if ( null !== $vaultToken ) {
			return $vaultToken;
		}

		if ( empty( $OTTtoken ) ) {
			throw new Exception( esc_html( __( 'The token wasn\'t generated correctly.', 'power-board' ) ) );
		}

		$vaultTokenData = [
			'token' => $OTTtoken,
		];

		if ( ! $this->args['cardsavecardchecked'] && ! $this->args['bankaccountsavechecked'] ) {
			$vaultTokenData['vault_type'] = 'session';
		}

		if ( ! empty( $additionalFields ) ) {
			$vaultTokenData = array_merge( $vaultTokenData, $additionalFields );
		}

		$responce = SDKAdapterService::getInstance()->createVaultToken( $vaultTokenData );

		if ( ! empty( $responce['error'] ) || empty( $responce['resource']['data']['vault_token'] ) ) {
			$message = ! empty( $responce['error']['message'] ) ? ' ' . $responce['error']['message'] : '';

			/* translators: %s: Detailed message from PowerBoard API. */
			throw new Exception( esc_html( sprintf( __( 'Can\'t create PowerBoard vault token. %s', 'power-board' ), $message ) ) );
		}

		if ( $this->shouldSaveVaultToken() ) {
			( new UserTokenRepository() )->saveUserToken( $responce['resource']['data'] );
		}

		$this->args['selectedtoken'] = $responce['resource']['data']['vault_token'];

		return $this->args['selectedtoken'];
	}

	public function shouldSaveVaultToken(): bool {
		$isCardSaveCard    = $this->args['cardsavecard'] && $this->args['cardsavecardchecked'];
		$isBankAccountSave = $this->args['bankaccountsaveaccount'] && $this->args['bankaccountsavechecked'];

		return $this->args['isuserloggedin'] && ( $isCardSaveCard || $isBankAccountSave );
	}
}

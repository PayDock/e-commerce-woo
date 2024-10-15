<?php

namespace WooPlugin\Helpers;

use Exception;
use WooPlugin\Repositories\UserTokenRepository;
use WooPlugin\Services\SDKAdapterService;

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
			throw new Exception( esc_html( __( 'The token wasn\'t generated correctly. widget_error', PLUGIN_TEXT_DOMAIN ) ) );
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

		$response = SDKAdapterService::getInstance()->createVaultToken( $vaultTokenData );

		if ( ! empty( $response['error'] ) ) {

			$parsed_api_error = '';

			if ( ! empty( $response['error']['details'][0]['description'] ) ) {

				$parsed_api_error = $response['error']['details'][0]['description'];

				if ( ! empty( $response['error']['details'][0]['status_code_description'] ) ) {
					$parsed_api_error .= ': ' . $response['error']['details'][0]['status_code_description'];
				}

			} elseif ( ! empty( $response['error']['message'] ) ) {
				$parsed_api_error = $response['error']['message'];
			}

			if ( empty( $parsed_api_error ) ) {
				$parsed_api_error = __( 'Unable to create ' . PLUGIN_TEXT_NAME . ' vault token', PLUGIN_TEXT_DOMAIN );
			}

			$parsed_api_error .= ' widget_error';

			throw new Exception( esc_html( $parsed_api_error ) );

		}

		if ( $this->shouldSaveVaultToken() ) {
			( new UserTokenRepository() )->saveUserToken( $response['resource']['data'] );
		}

		$this->args['selectedtoken'] = $response['resource']['data']['vault_token'];

		return $this->args['selectedtoken'];
	}

	public function shouldSaveVaultToken(): bool {
		$isCardSaveCard    = $this->args['cardsavecard'] && $this->args['cardsavecardchecked'];
		$isBankAccountSave = $this->args['bankaccountsaveaccount'] && $this->args['bankaccountsavechecked'];

		return $this->args['isuserloggedin'] && ( $isCardSaveCard || $isBankAccountSave );
	}
}

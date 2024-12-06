<?php

namespace PowerBoard\Services;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\APMsSettings;
use PowerBoard\Enums\BankAccountSettings;
use PowerBoard\Enums\CardSettings;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Enums\WalletSettings;
use PowerBoard\Enums\WidgetSettings;
use PowerBoard\Services\Settings\LiveConnectionSettingService;
use PowerBoard\Services\Settings\SandboxConnectionSettingService;
use PowerBoard\Services\Settings\WidgetSettingService;

final class SettingsService {
	private const ENABLED_CONDITION = 'yes';
	private static $instance = null;

	private $widgetService = null;
	private $settingService = null;

	private $isSandbox = false;
	private $isSafariOrIOS = false;

	protected function __construct() {
		$detector = new BrowserDetection();
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$userAgent = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] );
		} else {
			$userAgent = 'undefined';
		}
		$browser             = $detector->getBrowser( $userAgent );
		$os                  = $detector->getOS( $userAgent );
		$this->isSafariOrIOS = ( 'iOS' === $os['os_name'] )
		                       || (bool) $browser['browser_safari_original']
		                       || (bool) $browser['browser_ios_webview'];
	}

	public function isEnabledPayment(): bool {
		return self::ENABLED_CONDITION === ( new LiveConnectionSettingService() )
				->get_option( 'enabled' );
	}

	public function isSandbox(): bool {
		$this->getSettingsService();

		return $this->isSandbox;
	}

	private function getSettingsService(): AbstractSettingService {
		if ( ! is_null( $this->settingService ) ) {
			return $this->settingService;
		}

		$this->settingService = new SandboxConnectionSettingService();

		$this->isSandbox = self::ENABLED_CONDITION == $this->settingService
				->get_option(
					$this->getOptionName( $this->settingService->id, [
						SettingGroups::CREDENTIALS()->name,
						CredentialSettings::SANDBOX()->name,
					] )
				);

		if ( ! $this->isSandbox ) {
			$this->settingService = new LiveConnectionSettingService();
		}

		return $this->settingService;
	}

	public function getOptionName( string $id, array $fragments ): string {
		return implode( '_', array_merge( [ $id ], $fragments ) );
	}

	public function getWidgetAccessToken() {
		$settingService = $this->getSettingsService();

		return HashService::decrypt(
			$settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::CREDENTIALS()->name,
						CredentialSettings::WIDGET_KEY()->name,
					]
				)
			)
		);
	}

	public function getAccessToken() {
		$settingService = $this->getSettingsService();

		return HashService::decrypt(
			$settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::CREDENTIALS()->name,
						CredentialSettings::ACCESS_KEY()->name,
					]
				)
			)
		);
	}

	public function getCardGatewayId(): ?string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::CARD()->name,
				CardSettings::GATEWAY_ID()->name,
			]
		);

		$value = $settingService->get_option( $key );

		if ( $value !== null ) {
			return HashService::decrypt( $value );
		} else {
			return null;
		}
	}

	public function getBankAccountGatewayId(): ?string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::BANK_ACCOUNT()->name,
				BankAccountSettings::GATEWAY_ID()->name,
			]
		);

		$value = $settingService->get_option( $key );

		if ( $value !== null ) {
			return HashService::decrypt( $value );
		} else {
			return null;
		}
	}

	public function getWalletGatewayId( WalletPaymentMethods $methods ): ?string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::WALLETS()->name,
				$methods->name,
				WalletSettings::GATEWAY_ID()->name,
			]
		);

		$value = $settingService->get_option( $key );

		if ( $value !== null ) {
			return HashService::decrypt( $value );
		} else {
			return null;
		}
	}

	public function getAPMsGatewayId( OtherPaymentMethods $methods ): ?string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::A_P_M_S()->name,
				$methods->name,
				APMsSettings::GATEWAY_ID()->name,
			]
		);

		$value = $settingService->get_option( $key );

		if ( $value !== null ) {
			return HashService::decrypt( $value );
		} else {
			return null;
		}
	}

	public function isCardEnabled(): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::CARD()->name,
						CardSettings::ENABLE()->name,
					]
				)
			);
	}

	public function isBankAccountEnabled(): bool {
		return false;
	}

	public function isWalletEnabled( WalletPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		$result   = self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::WALLETS()->name,
						$methods->name,
						WalletSettings::ENABLE()->name,
					]
				)
			);
		$isApple  = WalletPaymentMethods::APPLE_PAY()->name === $methods->name;
		$isGoogle = WalletPaymentMethods::GOOGLE_PAY()->name === $methods->name;
		if ( $result && $isApple && ! $this->isSafariOrIOS ) {
			return false;
		}
		if ( $result && $isGoogle && $this->isSafariOrIOS ) {
			return false;
		}

		return $result;
	}

	public function getCard3DS(): string {
		$settingService = $this->getSettingsService();

		return $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::CARD()->name,
					CardSettings::DS()->name,
				]
			)
		);
	}

	public function getCard3DSServiceId(): string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::CARD()->name,
				CardSettings::DS_SERVICE_ID()->name,
			]
		);

		return HashService::decrypt( $settingService->get_option( $key ) );
	}

	public function getCardFraud(): string {
		$settingService = $this->getSettingsService();

		return $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::CARD()->name,
					CardSettings::FRAUD()->name,
				]
			)
		);
	}

	public function getCardFraudServiceId(): string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::CARD()->name,
				CardSettings::FRAUD_SERVICE_ID()->name,
			]
		);

		return HashService::decrypt( $settingService->get_option( $key ) );
	}

	public function getCardDirectCharge(): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::CARD()->name,
						CardSettings::DIRECT_CHARGE()->name,
					]
				)
			);
	}

	public function getCardSaveCardOption(): string {
		$settingService = $this->getSettingsService();

		return $this->getCardSaveCard() ? $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::CARD()->name,
					CardSettings::SAVE_CARD_OPTION()->name,
				]
			)
		) : '';
	}

	public function getCardSaveCard(): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::CARD()->name,
						CardSettings::SAVE_CARD()->name,
					]
				)
			);
	}

	public function getCardSupportedCardTypes(): string {
		$settingService = $this->getSettingsService();

		return $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::CARD()->name,
					CardSettings::SUPPORTED_CARD_TYPES()->name,
				]
			)
		);
	}

	public function getCardTypeExchangeOtt(): string {
		$settingService = $this->getSettingsService();

		return $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::CARD()->name,
					CardSettings::TYPE_EXCHANGE_OTT()->name,
				]
			)
		);
	}

	public function isAPMsEnabled( OtherPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::A_P_M_S()->name,
						$methods->name,
						APMsSettings::ENABLE()->name,
					]
				)
			);
	}

	public function getBankAccountSaveAccount(): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::BANK_ACCOUNT()->name,
						BankAccountSettings::SAVE_CARD()->name,
					]
				)
			);
	}

	public function getBankAccountSaveAccountOption(): string {
		$settingService = $this->getSettingsService();

		return $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::BANK_ACCOUNT()->name,
					BankAccountSettings::SAVE_CARD_OPTION()->name,
				]
			)
		);
	}

	public function isWalletFraud( WalletPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::WALLETS()->name,
						$methods->name,
						WalletSettings::FRAUD()->name,
					]
				)
			);
	}

	public function isWalletDirectCharge( WalletPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::WALLETS()->name,
						$methods->name,
						WalletSettings::DIRECT_CHARGE()->name,
					]
				)
			);
	}

	public function getWalletFraudServiceId( WalletPaymentMethods $methods ): string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::WALLETS()->name,
				$methods->name,
				WalletSettings::FRAUD_SERVICE_ID()->name,
			]
		);

		return HashService::decrypt( $settingService->get_option( $key ) );
	}

	public function isPayPallSmartButtonPayLater(): string {
		$settingService = $this->getSettingsService();

		return $settingService->get_option(
			$this->getOptionName(
				$settingService->id,
				[
					SettingGroups::WALLETS()->name,
					WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name,
					'pay_later',
				]
			)
		);
	}

	public function isAPMsFraud( OtherPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::A_P_M_S()->name,
						$methods->name,
						APMsSettings::FRAUD()->name,
					]
				)
			);
	}

	public function getAPMsFraudServiceId( OtherPaymentMethods $methods ): ?string {
		$settingService = $this->getSettingsService();

		$key = $this->getOptionName(
			$settingService->id,
			[
				SettingGroups::A_P_M_S()->name,
				$methods->name,
				APMsSettings::FRAUD_SERVICE_ID()->name,
			]
		);

		return HashService::decrypt( $settingService->get_option( $key ) );
	}

	public function isAPMsDirectCharge( OtherPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::A_P_M_S()->name,
						$methods->name,
						APMsSettings::DIRECT_CHARGE()->name,
					]
				)
			);
	}

	public function isAPMsSaveCard( OtherPaymentMethods $methods ): bool {
		$settingService = $this->getSettingsService();

		return self::ENABLED_CONDITION == $settingService->get_option(
				$this->getOptionName(
					$settingService->id,
					[
						SettingGroups::A_P_M_S()->name,
						$methods->name,
						APMsSettings::SAVE_CARD()->name,
					]
				)
			);
	}

	public function getWidgetPaymentCardTitle(): string {
		$setting = WidgetSettings::PAYMENT_CARD_TITLE();

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	private function getWidgetService(): WidgetSettingService {
		if ( is_null( $this->widgetService ) ) {
			$this->widgetService = new WidgetSettingService();
		}

		return $this->widgetService;
	}

	public function getWidgetPaymentCardDescription(): string {
		$setting = WidgetSettings::PAYMENT_CARD_DESCRIPTION();

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetPaymentBankAccountTitle(): string {
		$setting = WidgetSettings::PAYMENT_BANK_ACCOUNT_TITLE();

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetPaymentBankAccountDescription(): string {
		$setting = WidgetSettings::PAYMENT_BANK_ACCOUNT_DESCRIPTION();

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetPaymentWalletTitle( WalletPaymentMethods $methods ): string {
		switch ( $methods->name ) {
			case WalletPaymentMethods::APPLE_PAY()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_APPLE_PAY_TITLE();
				break;
			case WalletPaymentMethods::GOOGLE_PAY()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_GOOGLE_PAY_TITLE();
				break;
			case WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_PAYPAL_TITLE();
				break;
			case WalletPaymentMethods::AFTERPAY()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_AFTERPAY_V2_TITLE();
				break;
			default:
				$setting = '';
				break;
		}

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetPaymentWalletDescription( WalletPaymentMethods $methods ): string {
		switch ( $methods->name ) {
			case WalletPaymentMethods::APPLE_PAY()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_APPLE_PAY_DESCRIPTION();
				break;
			case WalletPaymentMethods::GOOGLE_PAY()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_GOOGLE_PAY_DESCRIPTION();
				break;
			case WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_PAYPAL_DESCRIPTION();
				break;
			case WalletPaymentMethods::AFTERPAY()->name:
				$setting = WidgetSettings::PAYMENT_WALLET_AFTERPAY_V2_DESCRIPTION();
				break;
			default:
				$setting = '';
				break;
		}

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetPaymentAPMTitle( OtherPaymentMethods $methods ): string {
		switch ( $methods->name ) {
			case OtherPaymentMethods::AFTERPAY()->name:
				$setting = WidgetSettings::PAYMENT_A_P_M_S_AFTERPAY_V1_TITLE();
				break;
			case OtherPaymentMethods::ZIPPAY()->name:
				$setting = WidgetSettings::PAYMENT_A_P_M_S_ZIP_TITLE();
				break;
			default:
				$setting = '';
				break;
		}

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetPaymentAPMDescription( OtherPaymentMethods $methods ): string {
		switch ( $methods->name ) {
			case OtherPaymentMethods::AFTERPAY()->name:
				$setting = WidgetSettings::PAYMENT_A_P_M_S_AFTERPAY_V1_DESCRIPTION();
				break;
			case OtherPaymentMethods::ZIPPAY()->name:
				$setting = WidgetSettings::PAYMENT_A_P_M_S_ZIP_DESCRIPTION();
				break;
			default:
				$setting = '';
				break;
		}

		return $this->getWidgetService()->get_option(
			$this->getOptionName( $this->getWidgetService()->id, [
				$setting->name,
			] ),
			$setting->getDefault()
		);
	}

	public function getWidgetStyles(): array {
		$data = [
			'background_color' => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_BACKGROUND_COLOR()->name ]
				                           )
			                           ),
			'text_color'       => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_TEXT_COLOR()->name ]
				                           )
			                           ),
			'border_color'     => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_BORDER_COLOR()->name ]
				                           )
			                           ),
			'error_color'      => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_ERROR_COLOR()->name ]
				                           )
			                           ),
			'success_color'    => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_SUCCESS_COLOR()->name ]
				                           )
			                           ),
			'font_size'        => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_FONT_SIZE()->name ]
				                           )
			                           ),
			'font_family'      => $this->getWidgetService()
			                           ->get_option(
				                           $this->getOptionName(
					                           $this->getWidgetService()->id,
					                           [ WidgetSettings::STYLE_FONT_FAMILY()->name ]
				                           )
			                           ),
		];

		$customStyles = json_decode( $this->getWidgetCustomStyles(), true );
		if ( null !== $customStyles && json_last_error() === JSON_ERROR_NONE ) {
			$data['custom_elements'] = $customStyles;
		}

		return $data;
	}

	public function getWidgetCustomStyles(): ?string {
		return $this->getWidgetService()
		            ->get_option(
			            $this->getOptionName(
				            $this->getWidgetService()->id,
				            [ WidgetSettings::STYLE_CUSTOM()->name ]
			            )
		            );
	}

	public function getWidgetScriptUrl(): string {
		if ( $this->isSandbox ) {
			$sdkUrl = 'https://widget.staging.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
		} else {
			$sdkUrl = 'https://widget.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
		}

		return strtr( $sdkUrl, [ '{version}' => self::getInstance()->getVersion() ] );
	}

	public function getVersion(): string {
		$versionKey       = $this->getOptionName( $this->getWidgetService()->id, [
			WidgetSettings::VERSION()->name,
		] );
		$customVersionKey = $this->getOptionName( $this->getWidgetService()->id, [
			WidgetSettings::CUSTOM_VERSION()->name,
		] );

		$version = $this->getWidgetService()->get_option( $versionKey );

		if ( WidgetSettings::VERSION()->getDefault() === $version ) {
			return $version;
		}

		return $this->getWidgetService()->get_option( $customVersionKey ) ?? $version;
	}

	public function getWidgetPaymentAPMsMinMax( OtherPaymentMethods $methods ): array {
		switch ( $methods->name ) {
			case OtherPaymentMethods::AFTERPAY()->name:
				$setting_min = WidgetSettings::PAYMENT_A_P_M_S_AFTERPAY_V1_MIN();
				$setting_max = WidgetSettings::PAYMENT_A_P_M_S_AFTERPAY_V1_MAX();
				break;
			case OtherPaymentMethods::ZIPPAY()->name:
				$setting_min = WidgetSettings::PAYMENT_A_P_M_S_ZIP_MIN();
				$setting_max = WidgetSettings::PAYMENT_A_P_M_S_ZIP_MAX();
				break;
			default:
				$setting_min = '';
				$setting_max = '';
				break;
		}

		return [
			'min' => $this->getWidgetService()->get_option(
				$this->getOptionName( $this->getWidgetService()->id, [
					$setting_min->name,
				] ),
				$setting_min->getDefault()
			),
			'max' => $this->getWidgetService()->get_option(
				$this->getOptionName( $this->getWidgetService()->id, [
					$setting_max->name,
				] ),
				$setting_max->getDefault()
			),
		];
	}


	public static function getInstance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

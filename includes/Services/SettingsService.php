<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;
use Paydock\Enums\APMsSettings;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\CredentialsTypes;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\SettingGroups;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;
use Paydock\Enums\WidgetSettings;
use Paydock\Services\Settings\LiveConnectionSettingService;
use Paydock\Services\Settings\SandboxConnectionSettingService;
use Paydock\Services\Settings\WidgetSettingService;

final class SettingsService
{
    private const ENABLED_CONDITION = 'yes';
    private static ?SettingsService $instance = null;

    private ?WidgetSettingService $widgetService = null;
    private ?AbstractSettingService $settingService = null;

    private bool $isSandbox = false;

    protected function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function isSandbox(): bool
    {
        $this->getSettingsService();

        return $this->isSandbox;
    }

    public function getAccessToken()
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName($settingService->id, [
            SettingGroups::CREDENTIALS()->name,
            CredentialSettings::ACCESS_KEY()->name,
        ]));
    }

    public function getSecretKey()
    {
        $settingService = $this->getSettingsService();

        $typeKey = $this->getOptionName($settingService->id, [
            SettingGroups::CREDENTIALS()->name,
            CredentialSettings::TYPE()->name,
        ]);

        if (CredentialsTypes::ACCESS_KEY()->name === $settingService->get_option($typeKey)) {
            return $this->getAccessToken();
        }

        return $settingService->get_option($this->getOptionName($settingService->id, [
            SettingGroups::CREDENTIALS()->name,
            CredentialSettings::SECRET_KEY()->name,
        ]));
    }

    public function getPublicKey(): ?string
    {
        $settingService = $this->getSettingsService();

        $typeKey = $this->getOptionName($settingService->id, [
            SettingGroups::CREDENTIALS()->name,
            CredentialSettings::TYPE()->name,
        ]);

        if (CredentialsTypes::ACCESS_KEY()->name === $settingService->get_option($typeKey)) {
            return $this->getAccessToken();
        }

        return $settingService->get_option($this->getOptionName($settingService->id, [
            SettingGroups::CREDENTIALS()->name,
            CredentialSettings::PUBLIC_KEY()->name,
        ]));
    }

    public function getCardGatewayId(): ?string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::CARD()->name,
                CardSettings::GATEWAY_ID()->name
            ]
        ));
    }

    public function getBankAccountGatewayId(): ?string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::BANK_ACCOUNT()->name,
                BankAccountSettings::GATEWAY_ID()->name,
            ]
        ));
    }

    public function getWalletGatewayId(WalletPaymentMethods $methods): ?string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::WALLETS()->name,
                $methods->name,
                WalletSettings::GATEWAY_ID()->name,
            ]
        ));
    }

    public function getAPMsGatewayId(OtherPaymentMethods $methods): ?string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::A_P_M_S()->name,
                $methods->name,
                APMsSettings::GATEWAY_ID()->name,
            ]
        ));
    }

    public function isCardEnabled(): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::CARD()->name,
                    CardSettings::ENABLE()->name
                ]
            ));
    }

    public function isBankAccountEnabled(): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::BANK_ACCOUNT()->name,
                    BankAccountSettings::ENABLE()->name
                ]
            ));
    }

    public function isWalletEnabled(WalletPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::WALLETS()->name,
                    $methods->name,
                    WalletSettings::ENABLE()->name,
                ]
            ));
    }

    public function getCard3DS(): string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::CARD()->name,
                CardSettings::DS()->name,
            ]
        ));
    }

    public function getCard3DSServiceId(): string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::CARD()->name,
                CardSettings::DS_SERVICE_ID()->name,
            ]
        ));
    }

    public function getCardFraud(): string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::CARD()->name,
                CardSettings::FRAUD()->name,
            ]
        ));
    }

    public function getCardFraudServiceId(): string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::CARD()->name,
                CardSettings::FRAUD_SERVICE_ID()->name,
            ]
        ));
    }

    public function getCardDirectCharge(): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::CARD()->name,
                    CardSettings::DIRECT_CHARGE()->name,
                ]
            ));
    }

    public function getCardSaveCard(): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::CARD()->name,
                    CardSettings::SAVE_CARD()->name,
                ]
            ));
    }

    public function getCardSaveCardOption(): string
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::CARD()->name,
                    CardSettings::SAVE_CARD_OPTION()->name,
                ]
            ));
    }

    public function getCardSupportedCardTypes(): string
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::CARD()->name,
                    CardSettings::SUPPORTED_CARD_TYPES()->name,
                ]
            ));
    }

    public function getCardTypeExchangeOtt(): string
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::CARD()->name,
                    CardSettings::TYPE_EXCHANGE_OTT()->name,
                ]
            ));
    }

    public function isAPMsEnabled(OtherPaymentMethods $methods): ?string
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::A_P_M_S()->name,
                    $methods->name,
                    APMsSettings::ENABLE()->name,
                ]
            ));
    }

    public function getBankAccountSaveAccount(): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::BANK_ACCOUNT()->name,
                    BankAccountSettings::SAVE_CARD()->name,
                ]
            ));
    }

    public function getBankAccountSaveAccountOption(): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::BANK_ACCOUNT()->name,
                    BankAccountSettings::SAVE_CARD_OPTION()->name,
                ]
            ));
    }

    public function isWalletFraud(WalletPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::WALLETS()->name,
                    $methods->name,
                    WalletSettings::FRAUD()->name,
                ]
            ));
    }

    public function isWalletDirectCharge(WalletPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::WALLETS()->name,
                    $methods->name,
                    WalletSettings::DIRECT_CHARGE()->name,
                ]
            ));
    }

    public function getWalletFraudServiceId(WalletPaymentMethods $methods): string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::WALLETS()->name,
                $methods->name,
                WalletSettings::FRAUD_SERVICE_ID()->name,
            ]
        ));
    }

    public function isPayPallSmartButtonPayLater(): string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::WALLETS()->name,
                WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name,
                'pay_later',
            ]
        ));
    }

    public function isAPMsFraud(OtherPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::A_P_M_S()->name,
                    $methods->name,
                    APMsSettings::FRAUD()->name,
                ]
            ));
    }

    public function getAPMsFraudServiceId(OtherPaymentMethods $methods): ?string
    {
        $settingService = $this->getSettingsService();

        return $settingService->get_option($this->getOptionName(
            $settingService->id, [
                SettingGroups::A_P_M_S()->name,
                $methods->name,
                APMsSettings::FRAUD_SERVICE_ID()->name,
            ]
        ));
    }

    public function isAPMsDirectCharge(OtherPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::A_P_M_S()->name,
                    $methods->name,
                    APMsSettings::DIRECT_CHARGE()->name,
                ]
            ));
    }

    public function isAPMsSaveCard(OtherPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::A_P_M_S()->name,
                    $methods->name,
                    APMsSettings::SAVE_CARD()->name,
                ]
            ));
    }

    public function isAPMsSaveCardOption(OtherPaymentMethods $methods): bool
    {
        $settingService = $this->getSettingsService();

        return self::ENABLED_CONDITION == $settingService->get_option($this->getOptionName(
                $settingService->id, [
                    SettingGroups::A_P_M_S()->name,
                    $methods->name,
                    APMsSettings::SAVE_CARD_OPTION()->name,
                ]
            ));
    }

    public function getWidgetTitle(): string
    {
        $setting = WidgetSettings::TITLE();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetDescription(): string
    {
        $setting = WidgetSettings::DESCRIPTION();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetPaymentCardTitle(): string
    {
        $setting = WidgetSettings::PAYMENT_CARD_TITLE();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetPaymentCardDescription(): string
    {
        $setting = WidgetSettings::PAYMENT_CARD_DESCRIPTION();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetPaymentBangAccountTitle(): string
    {
        $setting = WidgetSettings::PAYMENT_BANK_ACCOUNT_TITLE();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetPaymentBangAccountDescription(): string
    {
        $setting = WidgetSettings::PAYMENT_BANK_ACCOUNT_DESCRIPTION();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetPaymentWalletTitle(): string
    {
        $setting = WidgetSettings::PAYMENT_WALLET_TITLE();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetPaymentWalletDescription(): string
    {
        $setting = WidgetSettings::PAYMENT_WALLET_DESCRIPTION();

        return $this->getWidgetService()->get_option(
            $this->getOptionName($this->getWidgetService()->id, [
                $setting->name
            ]),
            $setting->getDefault()
        );
    }

    public function getWidgetStyles(): array
    {
        $data = [
            'background_color' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_BACKGROUND_COLOR()->name]
                )),
            'text_color' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_TEXT_COLOR()->name]
                )),
            'border_color' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_BORDER_COLOR()->name]
                )),
            'error_color' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_ERROR_COLOR()->name]
                )),
            'success_color' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_SUCCESS_COLOR()->name]
                )),
            'font_size' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_FONT_SIZE()->name]
                )),
            'font_family' => $this->getWidgetService()
                ->get_option($this->getOptionName(
                    $this->getWidgetService()->id,
                    [WidgetSettings::STYLE_FONT_FAMILY()->name]
                )),
        ];

        if ((
            $customStyles = json_decode($this->getWidgetCustomStyles(), true))
            && (JSON_ERROR_NONE === json_last_error())
        ) {
            $data['custom_elements'] = $customStyles;
        }

        return $data;
    }

    public function getWidgetCustomStyles(): ?string
    {
        return $this->getWidgetService()
            ->get_option($this->getOptionName(
                $this->getWidgetService()->id,
                [WidgetSettings::STYLE_CUSTOM()->name]
            ));
    }

    public function getOptionName(string $id, array $fragments): string
    {
        return implode('_', array_merge([$id], $fragments));
    }

    private function getWidgetService(): WidgetSettingService
    {
        if (is_null($this->widgetService)) {
            $this->widgetService = new WidgetSettingService();
        }

        return $this->widgetService;
    }

    private function getSettingsService(): AbstractSettingService
    {
        if (!is_null($this->settingService)) {
            return $this->settingService;
        }

        $this->settingService = new SandboxConnectionSettingService();

        $this->isSandbox = self::ENABLED_CONDITION == $this->settingService
                ->get_option($this->getOptionName($this->settingService->id, [
                    SettingGroups::CREDENTIALS()->name,
                    CredentialSettings::SANDBOX()->name,
                ]));

        if (!$this->isSandbox) {
            $this->settingService = new LiveConnectionSettingService();
        }

        return $this->settingService;
    }

    public function getVersion(): string
    {
        $versionKey = $this->getOptionName($this->getWidgetService()->id, [
            WidgetSettings::VERSION()->name
        ]);
        $customVersionKey = $this->getOptionName($this->getWidgetService()->id, [
            WidgetSettings::CUSTOM_VERSION()->name
        ]);

        $version = $this->getWidgetService()->get_option($versionKey);

        if (WidgetSettings::VERSION()->getDefault() === $version) {
            return $version;
        }

        return $this->getWidgetService()->get_option($customVersionKey) ?? $version;
    }
}

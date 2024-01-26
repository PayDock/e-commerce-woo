<?php

namespace Paydock\Abstract;

use Paydock\Enums\APMsSettings;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\CredentialsTypes;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\SaveCardOptions;
use Paydock\Enums\SettingGroups;
use Paydock\Enums\ValidationService;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;
use Paydock\PaydockPlugin;
use Paydock\Services\SettingsService;

abstract class AbstractSettingService extends \WC_Payment_Gateway
{
    public mixed $currentSection = null;
    public const LIVE_CONNECTION_TAB = PaydockPlugin::PLUGIN_PREFIX;
    public const SANDBOX_CONNECTION_TAB = PaydockPlugin::PLUGIN_PREFIX . '_sandbox';
    public const WIDGET_TAB = PaydockPlugin::PLUGIN_PREFIX . '_widget';
    public const LOG_TAB = PaydockPlugin::PLUGIN_PREFIX . '_logs';
    public const SECONDARY_PAYMENT_METHODS = [
        self::WIDGET_TAB,
        self::SANDBOX_CONNECTION_TAB,
        self::LOG_TAB,
    ];


    public function __construct()
    {
        $this->currentSection = $_GET['section'] ?? '';

        $this->enabled = 'yes';

        $this->method_title = __('Paydock Gateway', PaydockPlugin::PLUGIN_PREFIX);
        $this->method_description = __('Paydock simplify how you manage your payments. Reduce costs, technical'
            . ' headaches & streamline compliance using Paydock\'s payment orchestration.',
            PaydockPlugin::PLUGIN_PREFIX);
        $this->title = __('Paydock Gateway', PaydockPlugin::PLUGIN_PREFIX);

        $this->icon = plugins_url('assets/images/logo.svg');

        $this->init_settings();
        $this->init_form_fields();

        wp_register_script(
            PaydockPlugin::PLUGIN_PREFIX . '_admin_confirmation',
            plugins_url('assets/js/admin-tabs.js', PAY_DOCK_PLUGIN_FILE),
            ['jquery']
        );
        wp_register_script(
            PaydockPlugin::PLUGIN_PREFIX . '_admin_connections',
            plugins_url('assets/js/admin-connections.js', PAY_DOCK_PLUGIN_FILE),
            ['jquery']
        );
        wp_register_script(
            PaydockPlugin::PLUGIN_PREFIX . '_card_select',
            plugins_url('assets/js/card-select.js?01262024', PAY_DOCK_PLUGIN_FILE),
            ['jquery']
        );

        wp_enqueue_script(PaydockPlugin::PLUGIN_PREFIX . '_admin_connections');
        wp_enqueue_script(PaydockPlugin::PLUGIN_PREFIX . '_admin_confirmation');
        wp_enqueue_script(PaydockPlugin::PLUGIN_PREFIX . '_card_select');
        wp_enqueue_style(PaydockPlugin::PLUGIN_PREFIX . '_card_select',
            plugins_url('assets/css/card-select.css', PAY_DOCK_PLUGIN_FILE));
    }

    public function generate_settings_html($formFields = [], $echo = true): ?string
    {
        if (empty($form_fields)) {
            $formFields = $this->get_form_fields();
        }

        $tabs = $this->getTabs();
        ob_start();
        include_once plugin_dir_path(PAY_DOCK_PLUGIN_FILE) . 'templates/admin.php';

        $html = ob_get_contents();
        ob_end_clean();

        if ($echo) {
            echo $html;
        } else {
            return $html;
        }

        return null;
    }


    public function parentGenerateSettingsHtml($formFields = [], $echo = true): ?string
    {
        return parent::generate_settings_html($formFields, $echo);
    }

    public function init_form_fields(): void
    {
        $this->form_fields = match ($this->currentSection) {
            self::SANDBOX_CONNECTION_TAB => $this->getSandboxConnectionTabFields(),
            self::WIDGET_TAB => $this->getWidgetTabFields(),
            self::LOG_TAB => $this->getLogsTabFields(),
            default => $this->getLiveConnectionTabFields(),
        };
    }

    private function getTabs(): array
    {
        return [
            self::LIVE_CONNECTION_TAB => [
                'label' => __('Live Connection'),
                'active' => $this->currentSection == self::LIVE_CONNECTION_TAB,
            ],
            self::SANDBOX_CONNECTION_TAB => [
                'label' => __('Sandbox Connection'),
                'active' => $this->currentSection == self::SANDBOX_CONNECTION_TAB,
            ],
            self::WIDGET_TAB => [
                'label' => __('Widget Configuration'),
                'active' => $this->currentSection == self::WIDGET_TAB,
            ],
            self::LOG_TAB => [
                'label' => __('Logs'),
                'active' => $this->currentSection == self::LOG_TAB,
            ],
        ];
    }

    private function getLiveConnectionTabFields(): array
    {
        $service = SettingsService::getInstance();

        $fields = [];

        foreach (SettingGroups::cases() as $settingGroup) {
            $key = PaydockPlugin::PLUGIN_PREFIX . '_' . $service->getOptionName($this->id, [
                    $settingGroup->name,
                    'label',
                ]);

            if (SettingGroups::Card == $settingGroup) {
                $fields[$key . '_label'] = [
                    'type' => 'big_label',
                    'title' => __('Payment Methods:', PaydockPlugin::PLUGIN_PREFIX),
                ];
            }

            $fields[$key] = [
                'type' => 'big_label',
                'title' => __($settingGroup->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            $fields = array_merge($fields, match ($settingGroup) {
                SettingGroups::Credentials => $this->getCredentialOptions(),
                SettingGroups::Card => $this->getCardOptions(),
                SettingGroups::BankAccount => $this->getBankAccountOptions(),
                SettingGroups::Wallets => $this->getWalletsOptions(),
                SettingGroups::APMs => $this->getAPMsOptions()
            });
        }

        return $fields;
    }

    private function getCredentialOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (CredentialSettings::cases() as $credentialSettings) {
            if ($credentialSettings != CredentialSettings::Sandbox) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Credentials->name,
                    $credentialSettings->name,
                ]);
                $fields[$key] = [
                    'type' => $credentialSettings->getInputType(),
                    'title' => __($credentialSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
                ];
                if ($description = $credentialSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }

                if (CredentialSettings::Type == $credentialSettings) {
                    $fields[$key]['options'] = CredentialsTypes::toArray();
                }
            }
        }

        return $fields;
    }

    private function getCardOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (CardSettings::cases() as $cardSettings) {
            $key = $service->getOptionName($this->id, [SettingGroups::Card->name, $cardSettings->name]);
            $fields[$key] = [
                'type' => $cardSettings->getInputType(),
                'title' => __($cardSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            if ($description = $cardSettings->getDescription()) {
                $fields[$key]['description'] = $description;
                $fields[$key]['desc_tip'] = true;
            }

            $fields[$key]['options'] = match ($cardSettings) {
                CardSettings::DS => DSTypes::toArray(),
                CardSettings::Fraud => FraudTypes::toArray(),
                CardSettings::SaveCardOption => SaveCardOptions::toArray(),
                default => []
            };
        }

        return $fields;
    }

    private function getBankAccountOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (BankAccountSettings::cases() as $bankAccountSettings) {
            $key = $service->getOptionName($this->id, [
                SettingGroups::BankAccount->name,
                $bankAccountSettings->name,
            ]);

            $fields[$key] = [
                'type' => $bankAccountSettings->getInputType(),
                'title' => __($bankAccountSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            if ($description = $bankAccountSettings->getDescription()) {
                $fields[$key]['description'] = $description;
                $fields[$key]['desc_tip'] = true;
            }

            if (BankAccountSettings::SaveCardOption == $bankAccountSettings) {
                $fields[$key]['options'] = SaveCardOptions::toArray();
            }
        }

        return $fields;
    }

    private function getWalletsOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (WalletPaymentMethods::cases() as $walletPaymentMethods) {
            $fields[$service->getOptionName($this->id, [
                SettingGroups::Wallets->name,
                $walletPaymentMethods->name,
                'label',
            ])] = [
                'type' => 'label',
                'title' => __($walletPaymentMethods->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            foreach (WalletSettings::cases() as $walletSettings) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Wallets->name,
                    $walletPaymentMethods->name,
                    $walletSettings->name,
                ]);

                $fields[$key] = [
                    'type' => $walletSettings->getInputType(),
                    'title' => __($walletSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
                ];

                if ($description = $walletSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }
            }

            if (WalletPaymentMethods::PayPalSmartButton === $walletPaymentMethods) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Wallets->name,
                    $walletPaymentMethods->name,
                    'pay_later',
                ]);
                $fields[$key] = [
                    'type' => 'checkbox',
                    'title' => __('Pay Later', PaydockPlugin::PLUGIN_PREFIX),
                ];
            }
        }

        return $fields;
    }

    public function getAPMsOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (OtherPaymentMethods::cases() as $otherPaymentMethods) {
            $fields[$service->getOptionName($this->id, [
                SettingGroups::APMs->name,
                $otherPaymentMethods->name,
                'label',
            ])] = [
                'type' => 'label',
                'title' => __($otherPaymentMethods->name, PaydockPlugin::PLUGIN_PREFIX),
            ];

            foreach (APMsSettings::cases() as $APMsSettings) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Wallets->name,
                    $otherPaymentMethods->name,
                    $APMsSettings->name,
                ]);

                $fields[$key] = [
                    'type' => $APMsSettings->getInputType(),
                    'title' => __($APMsSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
                ];

                if ($description = $APMsSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }

                if (APMsSettings::SaveCardOption == $APMsSettings) {
                    $fields[$key]['options'] = SaveCardOptions::toArray();
                }
            }
        }

        return $fields;
    }

    private function getSandboxConnectionTabFields(): array
    {
        return array_merge([
            SettingsService::getInstance()
                ->getOptionName($this->id, [SettingGroups::Credentials->name, CredentialSettings::Sandbox->name]) => [
                'type' => CredentialSettings::Sandbox->getInputType(),
                'label' => __(
                    'To test your Paydock for WooCommerce Plugin, you can use the sandbox mode.',
                    PaydockPlugin::PLUGIN_PREFIX
                ),
                'title' => __(CredentialSettings::Sandbox->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ],
        ], $this->getLiveConnectionTabFields());
    }


    private function getWidgetTabFields(): array
    {
        return [];
    }

    private function getLogsTabFields(): array
    {
        return [];
    }

    public function generate_label_html($key, $value)
    {
        ob_start();

        include plugin_dir_path(PAY_DOCK_PLUGIN_FILE) . 'templates/label.php';

        return ob_get_clean();

    }

    public function generate_big_label_html($key, $value)
    {
        ob_start();

        include plugin_dir_path(PAY_DOCK_PLUGIN_FILE) . 'templates/big_label.php';

        return ob_get_clean();

    }

    public function generate_card_select_html($key, $data)
    {
        $field_key = $this->get_field_key($key);

        $defaults = [
            'title' => '',
            'disabled' => false,
            'class' => '',
            'css' => '',
            'placeholder' => '',
            'type' => 'text',
            'desc_tip' => false,
            'description' => '',
            'custom_attributes' => [],
            'options' => [],
        ];

        $data = wp_parse_args($data, $defaults);
        $value = $this->get_option($key);

        ob_start();

        include plugin_dir_path(PAY_DOCK_PLUGIN_FILE) . 'templates/card_select.php';

        return ob_get_clean();
    }

    public function process_admin_options()
    {
        $this->init_settings();
        $validationService = new ValidationService($this);
        $this->settings = array_merge($this->settings, $validationService->getResult());

        foreach ($validationService->getErrors() as $error) {
            \WC_Admin_Settings::add_error($error);

            $this->add_error($error);
        }

        $option_key = $this->get_option_key();
        do_action('woocommerce_update_option', ['id' => $option_key]);
        return update_option(
            $option_key,
            apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings),
            'yes'
        );
    }
}

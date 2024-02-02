<?php

namespace Paydock\Services\Validation;

use Paydock\Enums\CustomStylesElements;
use Paydock\Enums\WidgetSettings;
use Paydock\PaydockPlugin;
use Paydock\Services\Settings\WidgetSettingService;
use Paydock\Services\SettingsService;

class WidgetValidationService
{
    private const VALIDATED_FIELDS = [
        'TITLE',
        'DESCRIPTION',
        'PAYMENT_CARD_TITLE',
        'PAYMENT_CARD_DESCRIPTION',
        'PAYMENT_BANK_ACCOUNT_TITLE',
        'PAYMENT_BANK_ACCOUNT_DESCRIPTION',
        'PAYMENT_WALLET_TITLE',
        'PAYMENT_WALLET_DESCRIPTION',
    ];


    private const ENABLED_CONDITION = 'yes';
    private array $errors = [];

    private array $result = [];

    private array $data = [];

    private WidgetSettingService $service;

    public function __construct(WidgetSettingService $service)
    {
        $this->service = $service;
        $this->prepareFormData();
        $this->validate();

        $option_key = $service->get_option_key();
        do_action('woocommerce_update_option', ['id' => $option_key]);

        return update_option(
            $option_key,
            apply_filters('woocommerce_settings_api_sanitized_fields_' . $service->id, $service->settings),
            'yes'
        );
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function prepareFormData(): void
    {
        $post_data = $this->service->get_post_data();
        foreach ($this->service->get_form_fields() as $key => $field) {
            try {
                $this->data[$key] = $this->service->get_field_value($key, $field, $post_data);
                $this->result[$key] = $this->data[$key];

                if ('select' === $field['type'] || 'checkbox' === $field['type']) {
                    do_action('woocommerce_update_non_option_setting', [
                        'id' => $key,
                        'type' => $field['type'],
                        'value' => $this->data[$key],
                    ]);
                }
            } catch (\Exception $e) {
                $this->service->add_error($e->getMessage());
            }
        }
    }

    private function validate(): void
    {
        $versionKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            WidgetSettings::VERSION()->name
        ]);
        $customVersionKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            WidgetSettings::CUSTOM_VERSION()->name
        ]);
        $customStyleKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            WidgetSettings::STYLE_CUSTOM()->name
        ]);
        $validated = $this->getValidatedKeys();
        foreach ($this->data as $key => $value) {
            if (($settingName = array_search($key, $validated)) && empty($value)) {
                $this->errors[] = __(
                    WidgetSettings::{$settingName}()->getFullTitle() . " can`t be empty."
                    , PaydockPlugin::PLUGIN_PREFIX
                );
            }
            if (
                ($key == $customStyleKey)
                && !empty($value)
                && (
                    (
                        !($decoded = json_decode($value, true))
                        && (JSON_ERROR_NONE !== json_last_error())
                    )
                    || !$this->validateCustomStyles($decoded)
                )
            ) {
                $this->errors[] = __('Custom styles must be a valid JSON.', PaydockPlugin::PLUGIN_PREFIX);
            }
        }

        if ($this->data[$versionKey] == 'custom' && empty($this->data[$customVersionKey])) {
            $this->errors[] = __("Version can`t be empty.", PaydockPlugin::PLUGIN_PREFIX);
        }
    }

    private function validateCustomStyles(array $customStyles): bool
    {
        $elements = CustomStylesElements::getElements();
        foreach ($customStyles as $element => $styles) {
            if (!in_array($element, $elements)) {
                return false;
            }

            foreach ($styles as $style => $value) {
                if (!in_array($style, CustomStylesElements::getElementFor($element)->getStyleKeys())) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getValidatedKeys(): array
    {
        $service = SettingsService::getInstance();
        $result = [];

        foreach (self::VALIDATED_FIELDS as $field) {
            $result[$field] = $service->getOptionName($this->service->id, [
                WidgetSettings::{$field}()->name
            ]);
        }

        return $result;
    }
}
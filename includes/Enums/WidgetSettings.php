<?php

namespace Paydock\Enums;

use Paydock\Abstracts\AbstractEnum;

class WidgetSettings extends AbstractEnum
{
    protected const VERSION = 'VERSION';
    protected const CUSTOM_VERSION = 'CUSTOM_VERSION';
    protected const PAYMENT_CARD_TITLE = 'PAYMENT_CARD_TITLE';
    protected const PAYMENT_CARD_DESCRIPTION = 'PAYMENT_CARD_DESCRIPTION';
    protected const PAYMENT_BANK_ACCOUNT_TITLE = 'PAYMENT_BANK_ACCOUNT_TITLE';
    protected const PAYMENT_BANK_ACCOUNT_DESCRIPTION = 'PAYMENT_BANK_ACCOUNT_DESCRIPTION';
    protected const PAYMENT_WALLET_APPLE_PAY_TITLE = 'PAYMENT_WALLET_APPLE_PAY_TITLE';
    protected const PAYMENT_WALLET_APPLE_PAY_DESCRIPTION = 'PAYMENT_WALLET_APPLE_PAY_DESCRIPTION';
    protected const PAYMENT_WALLET_GOOGLE_PAY_TITLE = 'PAYMENT_WALLET_GOOGLE_PAY_TITLE';
    protected const PAYMENT_WALLET_GOOGLE_PAY_DESCRIPTION = 'PAYMENT_WALLET_GOOGLE_PAY_DESCRIPTION';
    protected const PAYMENT_WALLET_AFTERPAY_V2_TITLE = 'PAYMENT_WALLET_AFTERPAY_V2_TITLE';
    protected const PAYMENT_WALLET_AFTERPAY_V2_DESCRIPTION = 'PAYMENT_WALLET_AFTERPAY_V2_DESCRIPTION';
    protected const PAYMENT_WALLET_PAYPAL_TITLE = 'PAYMENT_WALLET_PAYPAL_TITLE';
    protected const PAYMENT_WALLET_PAYPAL_DESCRIPTION = 'PAYMENT_WALLET_PAYPAL_DESCRIPTION';
    protected const PAYMENT_A_P_M_S_AFTERPAY_V1_TITLE = 'PAYMENT_A_P_M_S_AFTERPAY_V1_TITLE';
    protected const PAYMENT_A_P_M_S_AFTERPAY_V1_DESCRIPTION = 'PAYMENT_A_P_M_S_AFTERPAY_V1_DESCRIPTION';
    protected const PAYMENT_A_P_M_S_ZIP_TITLE = 'PAYMENT_A_P_M_S_ZIP_TITLE';
    protected const PAYMENT_A_P_M_S_ZIP_DESCRIPTION = 'PAYMENT_A_P_M_S_ZIP_DESCRIPTION';
    protected const STYLE_BACKGROUND_COLOR = 'STYLE_BACKGROUND_COLOR';
    protected const STYLE_TEXT_COLOR = 'STYLE_TEXT_COLOR';
    protected const STYLE_BORDER_COLOR = 'STYLE_BORDER_COLOR';
    protected const STYLE_ERROR_COLOR = 'STYLE_ERROR_COLOR';
    protected const STYLE_SUCCESS_COLOR = 'STYLE_SUCCESS_COLOR';
    protected const STYLE_FONT_SIZE = 'STYLE_FONT_SIZE';
    protected const STYLE_FONT_FAMILY = 'STYLE_FONT_FAMILY';
    protected const STYLE_CUSTOM = 'STYLE_CUSTOM';

    public static function cases(): array
    {
        return parent::cases();
        $items = parent::cases();

        return array_filter($items, fn(self $item) => !in_array($item->name, [
            self::PAYMENT_BANK_ACCOUNT_DESCRIPTION,
            self::PAYMENT_BANK_ACCOUNT_TITLE,
        ]));
    }

    public function getTitle(): string
    {
        $result = explode('_', $this->name);
        $result = array_map(fn(string $item) => ucfirst(strtolower($item)), $result);
        $result = array_filter($result, fn(string $item) => !in_array(strtolower($item), [
            'style',
            'payment',
            'card',
            'bank',
            'account',
            'wallet',
            'a',
            'p',
            'm',
            's',
        ]));

        return implode(' ', $result);
    }

    public function getFullTitle(): string
    {
        $result = explode('_', $this->name);
        $result = array_map(fn(string $item) => ucfirst(strtolower($item)), $result);

        return implode(' ', $result);
    }

    public function getInputType(): string
    {
        switch ($this->name) {
            case self::CUSTOM_VERSION:
            case self::PAYMENT_CARD_TITLE:
            case self::PAYMENT_CARD_DESCRIPTION:
            case self::PAYMENT_BANK_ACCOUNT_TITLE:
            case self::PAYMENT_BANK_ACCOUNT_DESCRIPTION:
            case self::PAYMENT_WALLET_APPLE_PAY_TITLE:
            case self::PAYMENT_WALLET_APPLE_PAY_DESCRIPTION:
            case self::PAYMENT_WALLET_GOOGLE_PAY_TITLE:
            case self::PAYMENT_WALLET_GOOGLE_PAY_DESCRIPTION:
            case self::PAYMENT_WALLET_AFTERPAY_V2_TITLE:
            case self::PAYMENT_WALLET_AFTERPAY_V2_DESCRIPTION:
            case self::PAYMENT_WALLET_PAYPAL_TITLE:
            case self::PAYMENT_WALLET_PAYPAL_DESCRIPTION:
            case self::PAYMENT_A_P_M_S_AFTERPAY_V1_TITLE:
            case self::PAYMENT_A_P_M_S_AFTERPAY_V1_DESCRIPTION:
            case self::PAYMENT_A_P_M_S_ZIP_TITLE:
            case self::PAYMENT_A_P_M_S_ZIP_DESCRIPTION:
                return 'text';

            case self::VERSION:
            case self::STYLE_FONT_FAMILY:
            case self::STYLE_FONT_SIZE:
                return 'select';

            case self::STYLE_CUSTOM:
                return 'textarea';

            default:
                return 'color_picker';
        }
    }

    public function getOptions(): array
    {
        switch ($this->name) {
            case self::STYLE_FONT_SIZE:
                return $this->getFontSizes();
            case self::VERSION:
                return $this->getVersions();
            case self::STYLE_FONT_FAMILY:
                return $this->getFontFamily();
            default:
                return [];
        }
    }

    public function getFontSizes(): array
    {
        $result = [];

        for ($i = 8; $i <= 32; $i += 2) {
            $result[$i.'px'] = $i;
        }

        return $result;
    }

    public function getVersions(): array
    {
        return [
            'latest' => 'latest',
            'custom' => 'custom',
        ];
    }

    public function getFontFamily(): array
    {
        $fonts = [
            'Inter Regular',
            'serif',
            'sans-serif',
            'monospace',
            'cursive',
            'fantasy',
            'system-ui',
            'ui-serif',
            'ui-sans-serif',
            'ui-monospace',
            'ui-rounded',
            'emoji',
            'math',
            'fangsong',
        ];

        return array_combine($fonts, $fonts);
    }

    public function getDefault()
    {
        switch ($this->name) {
            case self::STYLE_FONT_SIZE:
                return '18px';
            case self::VERSION:
                return 'latest';
            case self::PAYMENT_CARD_TITLE:
                return 'Cards';
            case self::PAYMENT_CARD_DESCRIPTION:
                return 'Pay by cards';
            case self::PAYMENT_BANK_ACCOUNT_TITLE:
                return 'Bank Accounts';
            case self::PAYMENT_BANK_ACCOUNT_DESCRIPTION:
                return 'Pay by Bank Accounts';
            case self::PAYMENT_WALLET_APPLE_PAY_TITLE:
                return 'Apple Pay';
            case self::PAYMENT_WALLET_APPLE_PAY_DESCRIPTION:
                return 'Apple Pay is a safe, secure, and private way to pay.';
            case self::PAYMENT_WALLET_GOOGLE_PAY_TITLE:
                return 'Google Pay';
            case self::PAYMENT_WALLET_GOOGLE_PAY_DESCRIPTION:
                return 'Google Pay is a quick, easy, and secure way to pay online in store.';
            case self::PAYMENT_WALLET_AFTERPAY_V2_TITLE:
                return 'Afterpay v2';
            case self::PAYMENT_WALLET_AFTERPAY_V2_DESCRIPTION:
            case self::PAYMENT_A_P_M_S_AFTERPAY_V1_DESCRIPTION:
                return 'Shop as usual, then choose Afterpay as your payment method at checkout.';
            case self::PAYMENT_WALLET_PAYPAL_TITLE:
                return 'PayPal';
            case self::PAYMENT_WALLET_PAYPAL_DESCRIPTION:
                return 'PayPal is the faster, safer way to make an online payment.';
            case self::PAYMENT_A_P_M_S_AFTERPAY_V1_TITLE:
                return 'Afterpay v1';
            case self::PAYMENT_A_P_M_S_ZIP_TITLE:
                return 'Zip';
            case self::PAYMENT_A_P_M_S_ZIP_DESCRIPTION:
                return 'Zip Pay is an interest-free buy-now-pay-later service.';
            case self::STYLE_BACKGROUND_COLOR:
                return 'rgb(246, 240, 235)';
            case self::STYLE_TEXT_COLOR:
                return '#191919';
            case self::STYLE_BORDER_COLOR:
                return '#C9BCB9';
            case self::STYLE_ERROR_COLOR:
                return '#CD0000';
            case self::STYLE_SUCCESS_COLOR:
                return '#0B7F3B';
            default:
                return null;
        }
    }
}

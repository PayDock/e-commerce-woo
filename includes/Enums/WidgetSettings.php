<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class WidgetSettings extends AbstractEnum
{
    // protected const TITLE = 'TITLE';
    // protected const DESCRIPTION = 'DESCRIPTION';
    protected const VERSION = 'VERSION';
    protected const CUSTOM_VERSION = 'CUSTOM_VERSION';
    protected const PAYMENT_CARD_TITLE = 'PAYMENT_CARD_TITLE';
    protected const PAYMENT_CARD_DESCRIPTION = 'PAYMENT_CARD_DESCRIPTION';
    protected const PAYMENT_BANK_ACCOUNT_TITLE = 'PAYMENT_BANK_ACCOUNT_TITLE';
    protected const PAYMENT_BANK_ACCOUNT_DESCRIPTION = 'PAYMENT_BANK_ACCOUNT_DESCRIPTION';
    protected const PAYMENT_WALLET_TITLE = 'PAYMENT_WALLET_TITLE';
    protected const PAYMENT_WALLET_DESCRIPTION = 'PAYMENT_WALLET_DESCRIPTION';
    protected const PAYMENT_A_P_M_S_TITLE = 'PAYMENT_A_P_M_S_TITLE';
    protected const PAYMENT_A_P_M_S_DESCRIPTION = 'PAYMENT_A_P_M_S_DESCRIPTION';
    protected const STYLE_BACKGROUND_COLOR = 'STYLE_BACKGROUND_COLOR';
    protected const STYLE_TEXT_COLOR = 'STYLE_TEXT_COLOR';
    protected const STYLE_BORDER_COLOR = 'STYLE_BORDER_COLOR';
    protected const STYLE_ERROR_COLOR = 'STYLE_ERROR_COLOR';
    protected const STYLE_SUCCESS_COLOR = 'STYLE_SUCCESS_COLOR';
    protected const STYLE_FONT_SIZE = 'STYLE_FONT_SIZE';
    protected const STYLE_FONT_FAMILY = 'STYLE_FONT_FAMILY';
    protected const STYLE_CUSTOM = 'STYLE_CUSTOM';

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
            's'
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
        return match ($this->name) {
            self::CUSTOM_VERSION,
            self::PAYMENT_CARD_TITLE,
            self::PAYMENT_CARD_DESCRIPTION,
            self::PAYMENT_A_P_M_S_TITLE,
            self::PAYMENT_A_P_M_S_DESCRIPTION,
            self::PAYMENT_BANK_ACCOUNT_TITLE,
            self::PAYMENT_BANK_ACCOUNT_DESCRIPTION,
            self::PAYMENT_WALLET_TITLE,
            self::PAYMENT_WALLET_DESCRIPTION => 'text',
            self::VERSION,
            self::STYLE_FONT_FAMILY,
            self::STYLE_FONT_SIZE => 'select',
            self::STYLE_CUSTOM => 'textarea',
            default => 'color_picker',
        };
    }

    public function getOptions(): array
    {
        return match ($this->name) {
            self::STYLE_FONT_SIZE => $this->getFontSizes(),
            self::VERSION => $this->getVersions(),
            self::STYLE_FONT_FAMILY => $this->getFontFamily(),
            default => [],
        };
    }

    public function getFontSizes(): array
    {
        $result = [];

        for ($i = 8; $i <= 32; $i += 2) {
            $result[$i . 'px'] = $i;
        }

        return $result;
    }

    public function getVersions(): array
    {
        return [
            'latest' => 'latest',
            'custom' => 'custom'
        ];
    }

    public function getFontFamily(): array
    {
        $fonts = [
            'sans-serif',
            'serif',
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

    public function getDefault(): mixed
    {
        return match ($this->name) {
            self::STYLE_FONT_SIZE => '18px',
            self::VERSION => 'latest',
            self::PAYMENT_CARD_TITLE => 'Cards',
            self::PAYMENT_CARD_DESCRIPTION => 'Pay by cards',
            self::PAYMENT_A_P_M_S_TITLE => 'APMs',
            self::PAYMENT_A_P_M_S_DESCRIPTION => 'Pay by APMs',
            self::PAYMENT_BANK_ACCOUNT_TITLE => 'Bank Accounts',
            self::PAYMENT_BANK_ACCOUNT_DESCRIPTION => 'Pay by Bank Accounts',
            self::PAYMENT_WALLET_TITLE => 'Wallets',
            self::PAYMENT_WALLET_DESCRIPTION => 'Google Pay, Apple Pay, PayPal',
            self::STYLE_BACKGROUND_COLOR => 'rgb(235,235,235)',
            self::STYLE_TEXT_COLOR => '#1E1E1E',
            self::STYLE_BORDER_COLOR => '#B5B5B5',
            self::STYLE_ERROR_COLOR => '#e1001a',
            self::STYLE_SUCCESS_COLOR => '#00A000',
            default => null,
        };
    }
}
<?php

namespace Paydock\Enums;

use Paydock\Abstracts\AbstractEnum;

class CustomStylesElements extends AbstractEnum
{
    const INPUT = 'input';
    const LABEL = 'label';
    const TITLE = 'title';
    const DESCRIPTION = 'title_description';

    public static function getElements(): array
    {
        return array_map(function (self $element) {
            return $element->value;
        }, self::cases());
    }

    public static function getElementFor(string $value)
    {
        switch ($value) {
            case self::INPUT:
                return self::INPUT;
            case self::LABEL:
                return self::LABEL;
            case self::TITLE:
                return self::TITLE;
            case self::DESCRIPTION:
                return self::DESCRIPTION;
            default:
                break;
        }
    }

    public function getStyleKeys(): array
    {
        $styles = [];
        switch ($this->value) {
            case self::INPUT:
                $styles = CustomInputStyles::cases();
                break;
            case self::LABEL:
                $styles = CustomLabelStyles::cases();
                break;
            case self::TITLE:
                $styles = CustomTitleStyles::cases();
                break;
            case self::DESCRIPTION:
                $styles = CustomDescriptionStyles::cases();
                break;
        }

        return array_map(function ($element) {
            return $element->value;
        }, $styles);
    }
}

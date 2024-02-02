<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class CustomStylesElements extends AbstractEnum
{
    protected const INPUT = 'input';
    protected const LABEL = 'label';
    protected const TITLE = 'title';
    protected const DESCRIPTION = 'title_description';

    public static function getElements(): array
    {
        return array_map(fn(self $element) => $element->value, self::cases());
    }

    public static function getElementFor(string $value): self
    {
        return match ($value) {
            self::INPUT => self::INPUT(),
            self::LABEL => self::LABEL(),
            self::TITLE => self::TITLE(),
            self::DESCRIPTION => self::DESCRIPTION(),
        };
    }

    public function getStyleKeys(): array
    {
        $styles = match ($this->value) {
            self::INPUT => CustomInputStyles::cases(),
            self::LABEL => CustomLabelStyles::cases(),
            self::TITLE => CustomTitleStyles::cases(),
            self::DESCRIPTION => CustomDescriptionStyles::cases(),
        };

        return array_map(fn($elemnt) => $elemnt->value, $styles);
    }
}
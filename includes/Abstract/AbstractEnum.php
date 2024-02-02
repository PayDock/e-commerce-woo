<?php

namespace Paydock\Abstract;

abstract class AbstractEnum
{
    protected string $name;

    private const NAME_PROPERTY_NAME = 'name';

    private const VALUE_PROPERTY_NAME = 'value';

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @throws \Exception
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (defined(self::getConstFullName($name))) {
            return new static($name);
        }

        throw new \Exception('Wrong Enum declaration');
    }

    public function __get(string $name)
    {
        if (self::NAME_PROPERTY_NAME == $name) {
            return $this->name;
        }
        if (self::VALUE_PROPERTY_NAME == $name) {
            return constant(self::getConstFullName($this->name));
        } elseif (isset($this->{$name})) {
            return $this->{$name};
        }

        throw new \Exception('Try get access to non exists property.');
    }

    private static function getConstFullName(string $name): string
    {
        return static::class . '::' . $name;
    }

    public static function cases(): array
    {
        $RefClass = new \ReflectionClass(static::class);

        return array_map(function (string $name) {
            return static::{$name}();
        }, array_keys($RefClass->getConstants()));
    }
}
<?php

namespace PowerBoard\Abstracts;

use Exception;
use ReflectionClass;

abstract class AbstractEnum {
	private const NAME_PROPERTY_NAME  = 'name';
	private const VALUE_PROPERTY_NAME = 'value';
	protected $name;

	protected function __construct( string $name ) {
		$this->name = $name;
	}

	public static function __callStatic( string $name, array $arguments ) {
		if ( defined( self::get_const_full_name( $name ) ) ) {
			return new static( $name );
		}

		throw new Exception( 'Wrong Enum declaration' );
	}

	private static function get_const_full_name( string $name ): string {
		return static::class . '::' . $name;
	}

	public static function cases(): array {
		$ref_class = new ReflectionClass( static::class );

		return array_map(
			function ( string $name ) {
				return static::{$name}();
			},
			array_keys( $ref_class->getConstants() )
		);
	}

	public function __get( string $name ) {
		if ( self::NAME_PROPERTY_NAME === $name ) {
			return $this->name;
		}
		if ( self::VALUE_PROPERTY_NAME === $name ) {
			return constant( self::get_const_full_name( $this->name ) );
		} elseif ( isset( $this->{$name} ) ) {
			return $this->{$name};
		}

		throw new Exception( 'Try get access to non exists property.' );
	}
}

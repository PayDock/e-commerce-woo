<?php
declare( strict_types=1 );

namespace PowerBoard\Abstracts;

use ReflectionClass;

abstract class AbstractEnum {
	public static function cases(): array {
		$ref_class = new ReflectionClass( static::class );
		$constants = $ref_class->getConstants();

		return array_keys( $constants );
	}
}

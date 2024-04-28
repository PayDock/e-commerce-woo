<?php

namespace PowerBoard\Contracts;

interface Hook {
	public static function handle(): void;
}

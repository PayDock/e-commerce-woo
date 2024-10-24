<?php

namespace WooPlugin\Contracts;

interface Hook {
	public static function handle(): void;
}

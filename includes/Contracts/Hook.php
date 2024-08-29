<?php

namespace PayDock\Contracts;

interface Hook {
	public static function handle(): void;
}

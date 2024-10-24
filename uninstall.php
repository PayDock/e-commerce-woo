<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once 'vendor/autoload.php';

WooPlugin\Hooks\UninstallHook::handle();

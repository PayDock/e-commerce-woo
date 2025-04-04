<?php
declare( strict_types=1 );
/**
 * WooPlugin settings page
 *
 * @var array $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $data['template_service'] ) ) {
	$template_service = $data['template_service'];

	$template_service->setting_service->parent_generate_settings_html( $data['form_fields'], true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  --  the following require is safe it is not a user input.
}

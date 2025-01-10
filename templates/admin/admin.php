<?php
/**
 * @var array $form_fields
 * @var \PowerBoard\Services\TemplateService $template_service
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template_service->setting_service->parent_generate_settings_html( $form_fields, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  --  the following require is safe it is not a user input.

<?php
declare( strict_types=1 );

namespace PowerBoard\Helpers;

class MasterWidgetTemplatesHelper {
	public static function map_templates( ?array $data, bool $has_error, bool $is_optional = false ): array {
		if ( $has_error || empty( $data ) ) {
			return [];
		}

		$templates = [];
		foreach ( $data as $template ) {
			$templates[ $template['_id'] ] = $template['label'] . ' | ' . $template['_id'];
		}

		return ! empty( $templates ) ? [ '' => $is_optional ? 'Unselect template ID' : 'Select a template ID' ] + $templates : [];
	}

	/**
	 * Uses functions (get_option and update_option) from WordPress
	 */
	public static function validate_or_update_template_id( ?array $templates, bool $has_error, string $template_type_key, string $template_type_id ): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		$settings = get_option( 'woocommerce_power_board_settings' );
		if ( ! empty( $settings ) ) {
			$selected_template = ! empty( $settings[ $template_type_key ] ) ? $settings[ $template_type_key ] : [];
			if ( ! empty( $selected_template ) && ( $has_error || empty( $templates ) || ! array_key_exists( $selected_template, $templates ) ) ) {
				$settings[ $template_type_key ] = '';
				/* @noinspection PhpUndefinedFunctionInspection */
				update_option( 'woocommerce_power_board_settings', $settings );
				/* @noinspection PhpUndefinedFunctionInspection */
				set_transient( 'power_board_selected_' . $template_type_id . '_template_not_available', '1' );
			}
		}
	}
}

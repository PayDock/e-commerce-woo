<?php

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
		$templates = ! empty( $templates ) ? [ '' => $is_optional ? 'Default' : 'Select an ID' ] + $templates : [];

		return $templates;
	}

	public static function validate_or_update_template_id( ?array $templates, bool $has_error, string $template_type_key ) {
		$settings = get_option( 'woocommerce_power_board_settings' );
		if ( ! empty( $settings ) ) {
			$selected_template = ! empty( $settings[ $template_type_key ] ) ? $settings[ $template_type_key ] : [];
			if ( ! empty( $selected_template ) && ( $has_error || empty( $templates ) || ! array_key_exists( $selected_template, $templates ) ) ) {
				$settings[ $template_type_key ] = '';
				update_option( 'woocommerce_power_board_settings', $settings );
			}
		}
	}
}

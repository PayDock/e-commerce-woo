<?php

namespace PowerBoard\Helpers;

class MasterWidgetTemplatesHelper {
	public static function mapTemplates( ?array $data, string $has_error ) {
        if ( $has_error || empty( $data )) {
            return array();
        }

        $templates = array();
        foreach ( $data as $template ) {
            $templates[ $template['_id'] ] = $template['label'] . ' | ' . $template['_id'];
        }
        $templates = ! empty( $templates ) ? $templates + array( '' => '' ) : array();

        return $templates;
	}
}

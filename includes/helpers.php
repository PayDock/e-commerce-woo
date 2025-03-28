<?php

if ( ! function_exists( 'pb_get_access_token' ) ) {
	function pb_get_access_token(): ?string {
		return function_exists('__pb_token_accessor')
			? __pb_token_accessor()
			: null;
	}
}

if ( ! function_exists( 'pb_build_api_url' ) ) {
	function pb_build_api_url( string $path ): string {
		return \PowerBoard\API\ConfigService::build_api_url( $path );
	}
}

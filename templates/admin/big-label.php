<?php
/**
 * This file uses a function (esc_html) from WordPress
 *
 * @var array $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $data['value'] ) ) {
	$value = $data['value'];
}

?>
<tr>
	<th scope="row" class="titledesc">
		<h1>
		<?php
		/* @noinspection PhpUndefinedFunctionInspection */
		echo isset( $value['title'] ) ? esc_html( $value['title'] ) : '';
		?>
		</h1>
	</th>
	<td class="forminp">
	</td>
</tr>

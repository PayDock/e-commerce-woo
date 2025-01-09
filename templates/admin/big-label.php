<?php
/**
 * @var array $value
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<tr>
	<th scope="row" class="titledesc">
		<h1><?php echo isset( $value['title'] ) ? esc_html( $value['title'] ) : ''; ?></h1>
	</th>
	<td class="forminp">
	</td>
</tr>

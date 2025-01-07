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
		<h3><?php echo isset( $value['title'] ) ? wp_kses_post( $value['title'] ) : ''; ?></h3>
	</th>
	<td class="forminp">
	</td>
</tr>

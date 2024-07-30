<?php
echo wpautop( wp_kses_post( esc_attr( $description ) ) );
?>

<fieldset id="wc-classic-<?php echo esc_html( $id ) ?>" class="wc-credit-card-form wc-payment-form powerboard"
          style="background:transparent;">
	<?php do_action( 'woocommerce_credit_card_form_start', $id ); ?>
	<?php if ( 'power_board_gateway' === $id && $isUserLoggedIn && $isSaveCardEnable && 'SESSION_VAULT' != $card3DSFlow ): ?>
        <div class="power-board-select-saved-cards">
            <label style="font-size: 1rem; font-weight: bold; line-height: 2"
                   for="select-saved-cards">
				<?php echo esc_html( __( 'Saved payment details', 'power-board' ) ) ?>
            </label>
            <select id="select-saved-cards">
                <option value="">New card</option>
				<?php foreach ( $tokens as $option ): ?>
                    <option value="<?php echo esc_attr( $option['vault_token'] ) ?>">
						<?php echo esc_html(
							ucfirst( $option['card_scheme'] ) .
							' ' .
							str_pad( $option['expire_month'], 2, '0', STR_PAD_LEFT ) .
							'/' .
							$option['expire_year']
						) ?>
                    </option>
				<?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" id="power-board-selected-token" name="selectedtoken">
	<?php endif; ?>
    <div id="power-board-3ds-container"></div>
	<?php if ( in_array( $id, [ 'power_board_afterpay_a_p_m_s_gateway', 'power_board_zip_a_p_m_s_gateway' ] ) ): ?>
        <button id="classic-<?php echo esc_attr( $id ) ?>" class="btn-apm" type="button">
            <img src="/wp-content/plugins/power-board/assets/images/<?php echo esc_attr( str_replace( [
				'power_board_',
				'_a_p_m_s_gateway'
			], '', $id ) ) ?>.png">
        </button>
	<?php else: ?>
        <div id="classic-<?php echo esc_attr( $id ) ?>-wrapper">
            <div id="classic-<?php echo esc_attr( $id ) ?>"></div>
        </div>
	<?php endif; ?>
    <div id="classic-<?php echo esc_attr( $id ) ?>-error" class="power-board-validation-error" style="display: none;">
		<?php echo esc_html( __( 'Please fill in the required fields of the form to display payment methods.', 'power-board' ) ) ?>
    </div>
    <div id="classic-<?php echo esc_attr( $id ) ?>-error-countries" class="power-board-validation-error"
         style="display: none;">
		<?php echo esc_html( __( 'The payment method is not available in your country.', 'power-board' ) ) ?>
    </div>
	<?php if ( 'power_board_gateway' === $id ): ?>
        <input id="charge3dsid" type="hidden" name="charge3dsid">
	<?php endif; ?>
    <input id="classic-<?php echo esc_attr( $id ) ?>-token" type="hidden" name="payment_source[]">
    <input id="classic-<?php echo esc_attr( $id ) ?>-nonce" type="hidden" name="_wpnonce"
           value="<?php echo esc_attr( $nonce ) ?>">
    <input id="classic-<?php echo esc_attr( $id ) ?>-settings" type="hidden"
           value='<?php echo esc_attr( wc_esc_json( $settings ) ); ?>'>
	<?php if ( 'power_board_gateway' === $id && $isUserLoggedIn && $isSaveCardEnable && 'SESSION_VAULT' != $card3DSFlow ): ?>
        <label for="card_save_card">
            <input class=""
                   id="card_save_card"
                   type="checkbox"
                   value="yes"
                   name="cardsavecardchecked">
            <span class="wc-block-components-checkbox__label">
            <?php echo esc_html( __( 'Save payment details', 'power-board' ) ) ?>
        </span>
        </label>
	<?php endif; ?>
	<?php do_action( 'woocommerce_credit_card_form_end', $id ); ?>
    <div class="clear"></div>
</fieldset>

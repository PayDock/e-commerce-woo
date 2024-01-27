<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($field_key); ?>">
            <?php echo wp_kses_post($data['title']); ?><?php echo $this->settingService->get_tooltip_html($data); ?>
        </label>
    </th>
    <td class="forminp">
        <fieldset>
            <div id="multiselect" class="multiselect select">
                <div class="value">Please select payment methods...</div>
                <div class="error-text">Value is required and can't be empty</div>
            </div>
            <input name="<?php echo esc_attr($field_key); ?>"
                   style="visibility: hidden" id='card-select'
                   value="<?php echo $value ?>">
        </fieldset>
    </td>
</tr>

<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($key); ?>">
            <?php echo wp_kses_post($data['title']); ?><?php echo @$this->settingService->get_tooltip_html($data); ?>
        </label>
    </th>
    <td class="forminp">
        <fieldset>
            <input name="<?php echo @esc_attr($key); ?>"
                   class="input-text regular-input"
                   type="color"
                   value="<?php echo $value ?>">
        </fieldset>
    </td>
</tr>
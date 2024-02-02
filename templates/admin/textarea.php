<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($field_key); ?>">
            <?php echo wp_kses_post($data['title']); ?>
            <?php echo @$this->settingService->get_tooltip_html($data); // WPCS: XSS ok. ?>
        </label>
    </th>
    <td class="forminp">
        <fieldset>
            <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
            <textarea rows="16" cols="20" class="input-text regular-input <?php echo esc_attr($data['class']); ?>"
                      type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field_key); ?>"
                      id="<?php echo esc_attr($field_key); ?>" style="<?php echo esc_attr($data['css']); ?>"
                      placeholder="<?php echo esc_attr($data['placeholder']); ?>"
                <?php disabled($data['disabled'], true); ?>
                <?php echo @$this->settingService->get_custom_attribute_html($data); // WPCS: XSS ok. ?>
            ><?php echo esc_textarea(@$this->settingService->get_option($key)); ?></textarea>
            <?php echo @$this->settingService->get_description_html($data); // WPCS: XSS ok. ?>
        </fieldset>
    </td>
</tr>
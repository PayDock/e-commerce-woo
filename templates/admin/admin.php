<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
    <?php foreach ($tabs as $key => $tab): ?>
        <a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=<?php echo $key ?>"
           class="nav-tab <?php if ($tab['active']) echo 'nav-tab-active' ?>"><?php echo $tab['label']; ?></a>
    <?php endforeach; ?>
</nav>
<?php if ('pay_dock_log' !== $this->currentSection): ?>
    <?php echo $this->settingService->parentGenerateSettingsHtml($form_fields, false) ?>
<?php else: ?>
    <table class="wp-list-table widefat fixed striped table-view-list orders wc-orders-list-table wc-orders-list-table-shop_order">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-order_status">Id</th>
            <th scope="col" class="manage-column column-order_status">Date</th>
            <th scope="col" class="manage-column column-order_status">Operation</th>
            <th scope="col" class="manage-column column-order_status">Status</th>
            <th scope="col" class="manage-column column-order_status">Message</th>
        </tr>
        </thead>
        <tbody id="the-list" data-wp-lists="list:order">
        <?php if (empty($records)): ?>
            <tr class="no-items">
                <td class="colspanchange" colspan="3">No items found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td>
                        <?php echo $record->id; ?>
                    </td>
                    <td class="order_date column-order_date">
                        <?php echo $record->created_at; ?>
                    </td>
                    <td>
                        <?php echo $record->operation; ?>
                    </td>
                    <td>
                        <mark
                            <?php if ($record->type == 1): ?>
                                class="order-status status-processing tips"
                            <?php elseif ($record->type == 2): ?>
                                class="order-status status-on-hold tips"
                            <?php else: ?>
                                class="order-status status-pending tips"
                            <?php endif; ?>
                        >
                            <span><?php echo $record->status; ?></span>
                        </mark>
                    </td>
                    <td>
                        <?php echo $record->message; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-order_status">Id</th>
            <th scope="col" class="manage-column column-order_status">Date</th>
            <th scope="col" class="manage-column column-order_status">Operation</th>
            <th scope="col" class="manage-column column-order_status">Status</th>
            <th scope="col" class="manage-column column-order_status">Message</th>
        </tr>
        </tfoot>
    </table>
<?php endif; ?>

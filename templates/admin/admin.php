<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
    <?php foreach ($tabs as $key => $tab): ?>
        <a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=<?php echo $key ?>"
           class="nav-tab <?php if ($tab['active']) echo 'nav-tab-active' ?>"><?php echo $tab['label']; ?></a>
    <?php endforeach; ?>
</nav>
<?php if ('power_board_log' !== $this->currentSection): ?>
    <?php echo $this->settingService->parentGenerateSettingsHtml($form_fields, false) ?>
<?php else: ?>
    <table class="wp-list-table widefat fixed striped table-view-list orders wc-orders-list-table wc-orders-list-table-shop_order">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'id',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>ID</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'created_at',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>Date</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'id',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>Operation</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'status',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>Status</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status">Message</th>
        </tr>
        </thead>
        <tbody id="the-list" data-wp-lists="list:order">
        <?php if (empty($records['data'])): ?>
            <tr class="no-items">
                <td class="colspanchange" colspan="3">No items found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($records['data'] as $record): ?>
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
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'id',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>ID</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'created_at',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>Date</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'id',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>Operation</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status sorted
            <?php if (!empty($records['order']) && ($records['order'] == 'asc') && ($records['orderBy'] == 'id')): ?>
              asc
              <?php else: ?>
              desc
             <?php endif; ?>
            ">
                <a href="<?php echo add_query_arg([
                    'orderBy' => 'status',
                    'order' => $records['order'] == 'desc' ? 'asc' : 'desc'
                ]); ?>">
                    <span>Status</span>
                    <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                </a>
            </th>
            <th scope="col" class="manage-column column-order_status">Message</th>
        </tr>
        </tfoot>
    </table>
    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
        </div>
        <div class="alignleft actions"></div>
        <div class="tablenav-pages">
            <span class="displaying-num">
                <?php echo 'From ' . $records['from'] . ' to ' . $records['to'] . ' of ' . $records['count']; ?>
                records
            </span>
            <span class="pagination-links">
                <?php if ($records['current'] <= 1): ?>
                    <span class="tablenav-pages-navspan button disabled"
                          aria-hidden="true">«</span>
                    <span class="tablenav-pages-navspan button disabled"
                          aria-hidden="true">‹</span>
                <?php else: ?>
                    <a class="next-page button"
                       href="<?php echo add_query_arg(['page_number' => 1]); ?>">
                        <span class="screen-reader-text">First page</span>
                        <span aria-hidden="true">«</span>
                    </a>
                    <a class="last-page button"
                       href="<?php echo add_query_arg(['page_number' => $records['current'] - 1]); ?>">
                        <span class="screen-reader-text">Prev page</span>
                        <span aria-hidden="true">‹</span>
                    </a>
                <?php endif; ?>
                <span class="screen-reader-text">Current Page</span>
                <span id="table-paging" class="paging-input">
                    <span class="tablenav-paging-text">
                        <?php echo $records['current'] ?> of <span
                                class="total-pages"><?php echo $records['last_page'] ?></span>
                    </span>
                </span>
                <?php if ($records['current'] >= $records['last_page']): ?>
                    <span class="tablenav-pages-navspan button disabled"
                          aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan button disabled"
                          aria-hidden="true">»</span>
                <?php else: ?>
                    <a class="next-page button"
                       href="<?php echo add_query_arg(['page_number' => $records['current'] + 1]); ?>">
                        <span class="screen-reader-text">Next page</span>
                        <span aria-hidden="true">›</span>
                    </a>
                    <a class="last-page button"
                       href="<?php echo add_query_arg(['page_number' => $records['last_page']]); ?>">
                        <span class="screen-reader-text">Last page</span>
                        <span aria-hidden="true">»</span>
                    </a
                <?php endif; ?>
            </span>
        </div>
        <br class="clear">
    </div>
    <?php for ($i = 1; $i <= $records['last_page']; $i++): ?>

    <?php endfor; ?>
<?php endif; ?>

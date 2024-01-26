<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
    <?php foreach ($tabs as $key => $tab): ?>
        <a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=<?php echo $key ?>"
           class="nav-tab <?php if ($tab['active']) echo 'nav-tab-active' ?>"><?php echo $tab['label']; ?></a>
    <?php endforeach; ?>
</nav>

<?php echo $this->parentGenerateSettingsHtml($formFields, false) ?>

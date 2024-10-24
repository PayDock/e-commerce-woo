jQuery(function ($) {
    $(document).ready(function () {
        const pluginName = window.widgetSettings.pluginName;
        const deactivateClass = '#deactivate-' + pluginName.toLowerCase().replaceAll(' ', '-');

        $(deactivateClass).on('click', function (e) {
            e.preventDefault();

            let urlRedirect = jQuery(this).attr('href');
            let label = jQuery(this).attr('aria-label');

            if (confirm('Are you sure ' + label + ' ?')) {
                window.location.href = urlRedirect;
            }
        });
    });
})

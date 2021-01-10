(function ($) {
    $(document).ready(() => {
        // Handle dismissible notifications.
        $('.custom-php-settings-notice.notice.is-dismissible').each((a, el) => {
            $('.notice-dismiss', el).on('click', () => {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'custom_php_settings_dismiss_notice',
                        _ajax_nonce: data._nonce,
                        id: $(el).attr('id').split('-')[1],
                    },
                })
                    .done(() => {
                        if (data.debug) {
                            console.log('success');
                        }
                        el.remove();
                    })
                    .fail(() => {
                        if (data.debug) {
                            console.log('error');
                        }
                    })
                    .always(() => {
                        if (data.debug) {
                            console.log('complete');
                        }
                    });
            });
        });
    });
})(jQuery);

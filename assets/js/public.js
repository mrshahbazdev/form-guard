(function($) {
    'use strict';

    $('.fg-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $response = $form.find('.fg-response');
        var $btn = $form.find('.fg-submit');

        $btn.prop('disabled', true).text('Sending...');
        $response.removeClass('success error').hide();

        $.post(
            fg_public.ajax_url,
            $form.serialize() + '&action=fg_submit',
            function(res) {
                $btn.prop('disabled', false).text('Submit');
                if (res.success) {
                    $response.addClass('success').text(res.data.message).show();
                    $form[0].reset();
                } else {
                    $response.addClass('error').text(res.data.message).show();
                }
            },
            'json'
        ).fail(function() {
            $btn.prop('disabled', false).text('Submit');
            $response.addClass('error').text('An error occurred. Please try again.').show();
        });
    });
})(jQuery);

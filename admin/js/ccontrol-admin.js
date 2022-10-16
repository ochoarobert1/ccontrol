(function($) {
    'use strict';

    $(document).on('ready', function() {
        console.log('loaded');

        $('#printQuote').on('click', function(e) {
            e.preventDefault();
            console.log('clicked');
            window.open(ajaxurl + '?action=ccontrol_create_pdf&postid=' + jQuery('#printQuote').data('id'), '_blank');
        });

        $('#sendQuote').on('click', function(e) {
            e.preventDefault();
            console.log('clicked');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'ccontrol_create_pdf_send',
                    postid: jQuery('#sendQuote').data('id')
                },
                success: function(response) {
                    console.log(response);
                   
                }
            });
        });
    });

})(jQuery);
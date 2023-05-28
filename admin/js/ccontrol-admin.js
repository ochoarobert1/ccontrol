(function ($) {
    'use strict';

    $(document).on('ready', function () {
        console.log('loaded');

        $('#printQuote').on('click', function (e) {
            e.preventDefault();
            console.log('clicked');
            window.open(ajaxurl + '?action=ccontrol_create_pdf&postid=' + jQuery('#printQuote').data('id'), '_blank');
        });

        $('#sendQuote').on('click', function (e) {
            e.preventDefault();
            console.log('clicked');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'ccontrol_create_pdf_send',
                    postid: jQuery('#sendQuote').data('id')
                },
                success: function (response) {
                    console.log(response);

                }
            });
        });

        $('#printInvoice').on('click', function (e) {
            e.preventDefault();
            console.log('clicked');
            window.open(ajaxurl + '?action=ccontrol_create_invoice_pdf&postid=' + jQuery('#printInvoice').data('id'), '_blank');
        });

        $('#sendInvoice').on('click', function (e) {
            e.preventDefault();
            console.log('clicked');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'ccontrol_create_invoice_pdf_send',
                    postid: jQuery('#sendInvoice').data('id')
                },
                success: function (response) {
                    console.log(response);

                }
            });
        });

        $('#upload-btn').click(function (e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Upload Image',
                multiple: false
            }).open()
                .on('select', function (e) {
                    var uploaded_image = image.state().get('selection').first();
                    var image_url = uploaded_image.toJSON().url;
                    $('#image_url').val(image_url);
                    $('#ccontrol_logo').attr('src', image_url);
                });
        });
    });

})(jQuery);
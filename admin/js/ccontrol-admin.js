(function($) {
    'use strict';

    $(document).on('ready', function() {
        console.log('loaded');

        $('#printQuote').on('click', function(e) {
            e.preventDefault();
            console.log('clicked');

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'ccontrol_create_pdf',
                    postid: jQuery('#printQuote').data('id')
                },
                success: function(result) {
					console.log(result);
                    var blob = new Blob([result]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "test.pdf";
                    link.click();
                }
            });
        });
    });

})(jQuery);
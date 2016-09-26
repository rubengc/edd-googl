jQuery(document).ready(function ($) {
    $('#edd-googl-update-all-button').click(function() {
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'edd_googl_update_all'
            }
        });
    });
});
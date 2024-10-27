//AGRWC plugin with AGRWC_VARS array parameter passed 
jQuery(document).ready(function() {
    jQuery(AGRWC_VARS.selTags).on('click', function(e) {
        //loop through the required checkboxes of our class_alias
        var datax = new Object();
        var element = e.target.nodeName
        if (element != "A") return true;
        var hr = jQuery(this).attr('href');
        jQuery('.agrwc-cbx').each(function(index, obj) {
            if (!jQuery(this).prop('checked') && jQuery(this).hasClass('required')) {
				var idx = jQuery(this).prop('id');
                if (AGRWC_VARS[idx]) alert(AGRWC_VARS[idx]);
                else alert(AGRWC_VARS.alertText);
                //or highlight the field
                e.preventDefault();
                return false;
            }
            datax[jQuery(this).prop("name")] = jQuery(this).val();
        });

        if (Object.keys(datax).length > 0 && element == "A") {
            e.stopPropagation();
            datax['action'] = 'agrwc_post';
            e.preventDefault();
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(AGRWC_VARS.ajURL, datax, function(response) {
                //alert(response);
                window.location.href = hr;
            });
           return false;
        }
    });
    jQuery(AGRWC_VARS.selFTags).on('submit', function(e) {
        //loop through the required checkboxes of our class_alias
        var hasagrerr = 0;
        jQuery('.agrwc-cbx').each(function(index, obj) {
            if (!jQuery(this).prop('checked') && jQuery(this).hasClass('required')) {
                var idx = jQuery(this).prop('id');
                if (AGRWC_VARS[idx]) alert(AGRWC_VARS[idx]);
                else alert(AGRWC_VARS.alertText);
                hasagrerr = 1;
                //or highlight the field
                e.preventDefault();
                return false;
            }
        });
        if (hasagrerr == 1) {
            e.preventDefault();
            return false;
        }
    });





        jQuery("form.checkout").on('change', "input.agrwc-cbx", function() {
            var datax = new Object();
			

			
            if (jQuery(this).is(":checked")) {
                datax[jQuery(this).prop("name")] = jQuery(this).val();
            } else
                datax[jQuery(this).prop("name")] = 0;
            datax['action'] = 'agrwc_post';
            datax['security'] = wc_checkout_params.update_order_review_nonce;
            jQuery.post(AGRWC_VARS.ajURL, datax, function(response) {
                jQuery('body').trigger('update_checkout');
            });
        });

});
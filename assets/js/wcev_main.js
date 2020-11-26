/**
  * @description: This file is part of the WC External Variations plugin for Wordpress
  * @author: Impossible Dreams Network (https://web.impossibledreams.net)
  * @requires: jquery
  * @version: 1.0.9
  * @link: https://web.impossibledreams.net
  *
  * @copyright: Copyright (c) 2018-2020 Impossible Dreams Network (email: wp-plugins@impossibledreams.net)
  * @license: GNU General Public License v3.0
  */
(function($){
  $(document).ready(function(){
    // Change button text back when variations are reset
    $('body').on('reset_data', '.variations_form', function ( event ) {
        if (this.dataset.old_add_to_cart_text) {
           var add_to_cart_button = $(this).find('.single_add_to_cart_button')[0];
           add_to_cart_button.textContent = this.dataset.old_add_to_cart_text;
        }
    });

    // Hook onto 'show_variation' in order to change the 'Add to Cart' button
    $('body').on('show_variation', '.variations_form', function ( event, variation ) {
      var add_to_cart_button = $(this).find('.single_add_to_cart_button')[0];

      if (variation._wcev_add_to_cart_text) {
        // Save old button text before setting the new one
        if (!this.dataset.old_add_to_cart_text) {
           this.dataset.old_add_to_cart_text = add_to_cart_button.textContent;
        }

        // Set the button text if needed
        add_to_cart_button.textContent = variation._wcev_add_to_cart_text;
      } else {
        // Check if old cart text is saved, then reset. This is for an edge case when button text isn't set for one variation.
        if (this.dataset.old_add_to_cart_text) {
          add_to_cart_button.textContent = this.dataset.old_add_to_cart_text;
        }
      }
    });

    // Hook custom event on the 'Add to Cart' button in WooCommerce, for variations only
    $('body').on('click', '.variations_form .single_add_to_cart_button', function ( event, variation ) {
      // Error handling in case there is an issue with the URL
      try {
	    // Check if 'Add to Cart' button is still disabled, that means the variation has not been selected yet
	    if (!this.classList.contains('disabled')) {
		// Get variation ID and variations data form the form
		var form = $(this.form);
		var variationId = form.find("[name='variation_id']").val();
		var rawData = this.form.getAttribute('data-product_variations');

		// Find the URL for the external product
		if (variationId && rawData) {
                    // Extra the variations data and parse into JSON
		    var variationsData = $.parseJSON(rawData).find(x => x.variation_id === parseInt(variationId))

		    // Get the URL, open it and clear out opener for security (if needed)
		    if (variationsData && variationsData._wcev_external_url) {
		      if (variationsData._wcev_link_target && variationsData._wcev_link_target == 'same_window') {
                window.location = variationsData._wcev_external_url;
		      } else {
                var newWindow = window.open();
                newWindow.opener = null;
                newWindow.location = variationsData._wcev_external_url;
              }

		      // Stop propogating events so the item doesn't get added to cart
		      return false;
		    }
		}
	    }
      } catch (error) {
	alert('WC External Variations: An error has occured, see console');
        if (console) {
	    console.error(error);
        }
      }

      // Otherwise, continue event propogation so normal WooCommerce processing continues
      return true;
    });
  });
})(jQuery);

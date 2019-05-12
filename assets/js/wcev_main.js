/**
  * @description: This file is part of the WC External Variations plugin for Wordpress
  * @author: Impossible Dreams Network (https://web.impossibledreams.net)
  * @requires: jquery
  * @version: 1.0.4
  * @link: https://web.impossibledreams.net
  *
  * @copyright: Copyright (c) 2018-2019 Impossible Dreams Network (email: wp-plugins@impossibledreams.net)
  * @license: GNU General Public License v3.0
  */
(function($){
  $(document).ready(function(){
    // Hook custom event on the 'Add to Cart' button in WooCommerce, for variations only
    $('.woocommerce-variation-add-to-cart .single_add_to_cart_button').click(function(event) {
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

		    // Get the URL, open in a new window, and clear out opener for security
		    if (variationsData && variationsData._wcev_external_url) {
		      var newWindow = window.open();
		      newWindow.opener = null;
		      newWindow.location = variationsData._wcev_external_url;

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

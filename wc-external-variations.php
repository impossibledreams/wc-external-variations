<?php
/**
 * Plugin Name: WC External Variations
 * Plugin URI: https://github.com/impossibledreams/wc-external-variations
 * Version: 1.0.0
 *
 * GitHub Plugin URI: https://github.com/impossibledreams/wc-external-variations
 * Description: Adds basic support for external products to WooCommerce variations/variable products
 * Author: Impossible Dreams Network
 * Author URI: https://web.impossibledreams.net
 * Contributors: impossibledreams, yakovsh
 *
 * WC requires at least: 3.4.0
 * WC tested up to: 3.4.0
 *
 * Copyright: Copyright (c) 2018 Impossible Dreams Network (email: wp-plugins@impossibledreams.net)
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Prevent users from direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
     // Check if class exists or not
     if ( ! class_exists( 'WC_ExternalVariations' ) ) {
	load_plugin_textdomain( 'wc-external-variations', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

	class WC_ExternalVariations {
	    /**
	     * Constructor for the class, runs when the class is instantiated
	     */
	    public function __construct() {
		// Hooks into the WooCommerce initialize hook, so code in this class gets initialized after WooCommerce
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	    }

	    /**
	     * Runs whatever code needs to be initialized after WooCommerce has loaded
	     */
	    public function init() {
		// Hooks into the product editing code so custom fields can be shown and saved
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'wcev_show_fields'), 10, 3 );
		add_action( 'woocommerce_save_product_variation',            array( $this, 'wcev_save_fields'), 10, 2 );

		// Hooks into the user-facing code so the add to cart clicks can be redirected to external links
		add_filter( 'woocommerce_available_variation',               array( $this, 'wcev_add_variation_data'), 10, 3 );
		add_action( 'wp_enqueue_scripts',                            array( $this, 'wcev_load_scripts') );
	    }

	    /**
      	     * Function to show custom fields when editing products
	     */
	    function wcev_show_fields( $loop, $variation_data, $variation ) {
	       // Load and show the External URL field
	       woocommerce_wp_text_input( 
		array( 
		    'id'          => '_wcev_external_url[' . $variation->ID . ']', 
		    'label'       => __( 'External URL', 'woocommerce' ), 
		    'placeholder' => 'https://',
		    'desc_tip'    => 'true',
		    'description' => __( 'Enter the URL of the external product.', 'woocommerce' ),
		    'value'       => get_post_meta( $variation->ID, '_wcev_external_url', true )
		)
	       );

	       // Load and show the External SKU field
	       woocommerce_wp_text_input( 
		array( 
		    'id'          => '_wcev_external_sku[' . $variation->ID . ']', 
		    'label'       => __( 'External SKU', 'wc-external-variations' ), 
		    'placeholder' => '',
		    'desc_tip'    => 'true',
		    'description' => __( 'Enter the SKU of the external product', 'wc-external-variations' ),
		    'value'       => get_post_meta( $variation->ID, '_wcev_external_sku', true )
		)
	       );
	    }

	    /**
	      * Function to save custom fields when editing products, also sanitizes the content before saving it
	      */
	    function wcev_save_fields( $variation_id, $i ) {
		// Save the External URL field
		if ( isset( $_POST['_wcev_external_url'][ $variation_id ] ) ) {
	    	    update_post_meta( $variation_id, '_wcev_external_url',  wc_clean( $_POST['_wcev_external_url'][ $variation_id ] ) );
		}

		// Save the External SKU field
		if ( isset( $_POST['_wcev_external_sku'][ $variation_id ] ) ) {
	    	    update_post_meta( $variation_id, '_wcev_external_sku',  wc_clean( $_POST['_wcev_external_sku'][ $variation_id ] ) );
		}
	    }
	 
	    /**
	      * Function to load the external URL into the front end HTML so the Javascript interceptor can find it and use it
	      * Also provides support to search/replace of the external SKU placeholder in both the external URL and variation description.
	      * Also provides shortcode support for the external URL which can be used to set standard links across the installation.
	      */
	    function wcev_add_variation_data( $data, $product, $variation ) {
		// Load the custom fields from the post
		$external_url = get_post_meta( $variation->get_id(), '_wcev_external_url', true );
		$external_sku = get_post_meta( $variation->get_id(), '_wcev_external_sku', true );

		// Prepare the External URL field for the front by running it through shortcode processing and replacing SKU if needed
		if ( isset( $external_url ) ) {
		    // Process shortcodes
		    $external_url = do_shortcode( $external_url);

		    // If needed, replace the external SKU placeholder
		    if( isset( $external_sku ) and strpos( $external_url, '%externalsku%' ) ) {
			$external_url = str_replace( '%externalsku%', $external_sku, $external_url );
		    }

		    // Set the field to be returned to the front end, with sanitizing
		    $data['_wcev_external_url']  = esc_url( $external_url );
		}

		// Replace SKU in variation description if present, with sanitizing
		$description = $data['variation_description'];
		if( isset( $description ) and isset( $external_sku ) and strpos( $description, '%externalsku%' ) ) {
		    $data['variation_description'] = wc_clean( str_replace( '%externalsku%', $external_sku, $description ) );
		}

		// Return data for the frond end
		return $data;
	    } 

	     /**
	      * This function loads Javascript files for this plugin which do the actual interception of the Add To Cart clicks
	      */
	    function wcev_load_scripts() {
		wp_enqueue_script( 'wcev_main', plugin_dir_url( __FILE__ ) . 'assets/js/wcev_main.js', array('jquery'), null, true );
	    }
	} /* end of class */

	// Get an instance of the class and add it to the globals
	$GLOBALS['wc_external_variations'] = new WC_ExternalVariations();
    } /* end of class existence check */
} /* end of woocommerce plugin check */

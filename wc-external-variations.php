<?php
/**
 * Plugin Name: WC External Variations
 * Plugin URI: https://github.com/impossibledreams/wc-external-variations
 * Version: 1.0.10
 *
 * GitHub Plugin URI: https://github.com/impossibledreams/wc-external-variations
 * Description: Adds basic support for external products to WooCommerce variations/variable products
 * Author: Impossible Dreams Network
 * Author URI: https://web.impossibledreams.net
 * Contributors: impossibledreams, yakovsh
 *
 * WC requires at least: 4.0.0
 * WC tested up to: 4.8.0
 *
 * Copyright: Copyright (c) 2018-2020 Impossible Dreams Network (email: wp-plugins@impossibledreams.net)
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
            // Adds shortcodes
            add_shortcode('wcev_product_attr', 	array( $this, 'wcev_product_getattribute_shortcode') );
            add_shortcode('wcev_var_field',  	array( $this, 'wcev_variation_customfield_shortcode') );
            add_shortcode('wcev_var_postdate',  array( $this, 'wcev_variation_postdate_shortcode') );

            // Hooks into the product editing code so custom fields can be shown and saved in the editor
            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'wcev_filter_show_fields'), 10, 3 );
            add_action( 'woocommerce_save_product_variation',            array( $this, 'wcev_filter_save_fields'), 10, 2 );

            // Hooks into the user-facing code so the add to cart clicks can be redirected to external links in the front end
            add_filter( 'woocommerce_available_variation',               array( $this, 'wcev_filter_add_variation_data'), 10, 3 );

            // Loads Javascript for the front end code.
            add_action( 'wp_enqueue_scripts',                            array( $this, 'wcev_load_scripts') );

            // This filter is used to remember the variation ID being processed, and is unhooked in woocommerce_product_after_variable_attributes
            add_filter( 'woocommerce_show_variation_price', 	     array( $this, 'wcev_filter_remember_variation_id'), 10, 3 );

	        // Add settings
	        add_filter( 'woocommerce_get_sections_products', array( $this, 'wcev_filter_add_settings_section') );
	        add_filter( 'woocommerce_get_settings_products', array( $this, 'wcev_filter_add_settings_details'), 10, 2 );
	    }

	    /**
	     * Adds settings section to the WooCommerce > Settings > Products
	     */
        function wcev_filter_add_settings_section( $sections ) {
            $sections['external_variations'] = __( 'External Variations', 'wcev-domain' );
            return $sections;
        }

	    /**
	     * Adds plugin settings
	     */
        function wcev_filter_add_settings_details( $settings, $current_section ) {
            // Make sure setting are being added to the right place
            if ( 'external_variations' === $current_section ) {
                $new_settings = array(
                    array(
                        'title'     => __( 'External Variations', 'wcev-domain' ),
                            'type'      => 'title',
                            'id'        => 'external_variations_section',
                    ),

                    array(
                        'id'       => 'wcev_links_target',
                        'type'     => 'radio',
                        'title'    => __( 'Open links in', 'wcev-domain' ),
                        'options'  => array(
                            'new_window'  => __( 'New window/tab', 'wcev-domain' ),
                            'same_window' => __( 'Same window/tab', 'wcev-domain' ),
                        ),
                        'default'  => 'new_window',
                        'desc'     => __( 'Configures whether external links open in a new or existing window', 'wcev-domain' ),
                        'desc_tip' => true,
                    ),

                    array(
                        'id'       => 'wcev_links_trigger',
                        'type'     => 'radio',
                        'title'    => __( 'Open links when', 'wcev-domain' ),
                        'options'  => array(
                            'cart_button_selected'  => __( 'The Add to Cart button is selected', 'wcev-domain' ),
                            'variation_selected'    => __( 'When the variation is selected', 'wcev-domain' ),
                        ),
                        'default'  => 'cart_button_selected',
                        'desc'     => __( 'Configures what action triggers the opening of external links', 'wcev-domain' ),
                        'desc_tip' => true,
                    ),

                    array(
                        'id'       => 'wcev_add_to_cart_text',
                        'type'     => 'text',
                        'title'    => __( 'Text to the "Add to Cart" button', 'wcev-domain' ),
                        'options'  => array(
                            'new_window'  => __( 'New window/tab', 'wcev-domain' ),
                            'same_window' => __( 'Same window/tab', 'wcev-domain' ),
                        ),
                        'default'  => '',
                        'desc'     => __( 'Configures custom text for the "Add to Cart" button, leave empty not to override', 'wcev-domain' ),
                        'desc_tip' => true,
                    ),

                    array(
                        'type'  => 'sectionend',
                        'id'    => 'external_variations_section',
                    ),
                );
                return $new_settings;

            // Otherwise keep going
            } else {
                return $settings;
            }
        }

	    /**
	     * Remembers the variation ID to be used for shortcode processing, unset in woocommerce_product_after_variable_attributes
	     */
	    function wcev_filter_remember_variation_id( $price, $instance, $variation ) {
            global $wcev_variation_id;
            $wcev_variation_id = $variation->get_id();
            return $price;
        }

	    /**
      	 * Shortcode function to retrieve and show an attribute for a WooCommerce product.
	     */
	    function wcev_product_getattribute_shortcode( $atts, $content = null ) {
            // Parse the shortcode attributes
            $atts = shortcode_atts( array('name' => '', ), $atts, 'wcev_product_attr' );
            $field_name = $atts['name'];

            // Default return value is empty
            $return = '';

            // Try to get the attribute and return
            global $product;
            if ( isset( $product ) and !empty( $field_name ) ) {
                $value = $product->get_attribute( $field_name );
                            if ( !empty( $value ) ) {
                    $return = $value;
                }
            }

            // Return value
            return $return;
	    }

	    /**
      	     * Shortcode function to retrieve and show a custom field for a variation.
	     */
	    function wcev_variation_customfield_shortcode( $atts, $content = null ) {
		// Parse the shortcode attributes
		$atts = shortcode_atts( array('id' => '', ), $atts, 'wcev_var_field' );
		$id = $atts['id'];

		// Default return value is empty
		$return = '';

		// Try to get the attribute and return
		global $wcev_variation_id;
		if ( isset( $wcev_variation_id ) and !empty( $id ) ) {
			$values = get_post_meta( $wcev_variation_id, $id );
                        if ( !empty( $values ) ) {
				foreach ( $values as $value ) {
					$return .= $value;
				}
			}
		}

		// Return value
		return $return;
	    }

	    /**
      	     * Shortcode function to show the post creation date for a variation.
	     */
	    function wcev_variation_postdate_shortcode( $atts, $content = null ) {
		global $wcev_variation_id;
		if ( isset( $wcev_variation_id ) ) {
			return get_the_date('', $wcev_variation_id ) . ' ' . get_the_time('', $wcev_variation_id );
		} else {
			return '';
		}
	    }

	    /**
      	 * Function to show custom fields when editing products
	     */
	    function wcev_filter_show_fields( $loop, $variation_data, $variation ) {
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

	       // Load and show the External Status field
	       woocommerce_wp_text_input(
		array(
		    'id'          => '_wcev_external_status[' . $variation->ID . ']',
		    'label'       => __( 'External Status', 'wc-external-variations' ),
		    'placeholder' => '',
		    'desc_tip'    => 'true',
		    'description' => __( 'Enter the status of the external product', 'wc-external-variations' ),
		    'value'       => get_post_meta( $variation->ID, '_wcev_external_status', true )
		)
	       );

         // Load and show the "Add to Cart button text"
         woocommerce_wp_text_input(
    array(
        'id'          => '_wcev_external_add_to_cart_text[' . $variation->ID . ']',
        'label'       => __( 'External "Add To Cart" Text', 'wc-external-variations' ),
        'placeholder' => '',
        'desc_tip'    => 'true',
        'description' => __( 'Enter the text for the "Add to Cart Button"', 'wc-external-variations' ),
        'value'       => get_post_meta( $variation->ID, '_wcev_external_add_to_cart_text', true )
    )
         );
	    }

	    /**
	      * Function to save custom fields when editing products, also sanitizes the content before saving it
	      */
	    function wcev_filter_save_fields( $variation_id, $i ) {
		// Save the External URL field
		if ( isset( $_POST['_wcev_external_url'][ $variation_id ] ) ) {
	    	    update_post_meta( $variation_id, '_wcev_external_url',  esc_url_raw( wp_unslash( $_POST['_wcev_external_url'][ $variation_id ] ) ) );
		}

		// Save the External SKU field
		if ( isset( $_POST['_wcev_external_sku'][ $variation_id ] ) ) {
	    	    update_post_meta( $variation_id, '_wcev_external_sku',  wc_clean( $_POST['_wcev_external_sku'][ $variation_id ] ) );
		}

		// Save the External status field
		if ( isset( $_POST['_wcev_external_status'][ $variation_id ] ) ) {
	    	    update_post_meta( $variation_id, '_wcev_external_status',  wc_clean( $_POST['_wcev_external_status'][ $variation_id ] ) );
		}

		// Save the External Add To Cart text field
		if ( isset( $_POST['_wcev_external_add_to_cart_text'][ $variation_id ] ) ) {
	    	    update_post_meta( $variation_id, '_wcev_external_add_to_cart_text',  wc_clean( $_POST['_wcev_external_add_to_cart_text'][ $variation_id ] ) );
		}
	    }

	    /**
	      * Function to load the external URL into the front end HTML so the Javascript interceptor can find it and use it
	      * Also provides shortcode support for the external URL which can be used to set standard links across the installation.
	      */
	    function wcev_filter_add_variation_data( $data, $product, $variation ) {
            // Load the custom fields from the post
            $external_url = get_post_meta( $variation->get_id(), '_wcev_external_url', true );

            // Prepare the External URL field for the front by running it through shortcode processing
            if ( isset( $external_url ) ) {
                // Process shortcodes
                $external_url = do_shortcode( $external_url);

                // Set the field to be returned to the front end
                $data['_wcev_external_url']  = $external_url;

                // Set setting fields
                $data['_wcev_links_target']   = WC_Admin_Settings::get_option('wcev_links_target',  'new_window');
                $data['_wcev_links_trigger']  = WC_Admin_Settings::get_option('wcev_links_trigger', 'cart_button_selected');

                // Set 'add to cart' button text
                $settings_text = WC_Admin_Settings::get_option('wcev_add_to_cart_text', '');
                $data_text = sanitize_text_field( get_post_meta( $variation->get_id(), '_wcev_external_add_to_cart_text', true ) );
                $data['_wcev_add_to_cart_text']  = !empty( $data_text ) ? $data_text : $settings_text;
		    }

            // Unset the global variable used for shortcodes
            unset( $GLOBALS['wcev_variation_id'] );

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

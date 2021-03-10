=== WC External Variations ===
Contributors: impossibledreams, yakovsh
Donate link: https://www.paypal.com/biz/fund?id=8JX776MCWU338
Tags: woocommerce, external, variations, variable
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.7
Tested up to: 5.6.0
Stable tag: trunk
Requires PHP: 5.2.4

A WordPress plugin that adds basic support for external products to WooCommerce variations/variable products

== Description ==
Adds basic support for external products to WooCommerce
variations/variable products. This plugin allows you to define an external
URL on any variation and will then open that link when the user
clicks on the *Add To Cart* button. Because the Javascript is used for this,
any adding to cart that is done via the backend such as with APIs will not
redirect properly.

= Shortcode support  =

This plugin provides two shortcodes that you can use within your site:
1. [wcev_product_attr] - allows you to display an product attribute, use the "name" attribute to find the right attribute.
2. [wcev_var_field] - allows you to display a custom field, attribute or data element for a variation, use the "id" attribute to point to the right field.
The order of precendence is as follows: custom fields, attributes, then data elements.
3. [wcev_var_postdate] - displays the formatted post date and time for a variation, doesn't take attributes.

= External SKU and status fields =
In addition to the external URL field, a second field called "External SKU" is provided for each variation.
You can set this field and then use the provided shortcode to display it.
The purpose is to track the external SKU separately from the internal one.

There is now ability to specify the 'Add to Button' text in a specific variation. If the global setting is set, the variation-specific field will override it.

= Settings =
To change settings, go to WooCommerce Settings -> Products -> External Variations. The following are available:
* Ability to change if links open in new or same window
* Ability to change if links open when the 'Add to Cart' button is clicked or when the variation is selected
* Ability to override the 'Add to Cart' button text for all external variations

= More Details =
Please note that this plugin has only been tested in a vanilla WordPress / WooCommerce
installation without any other plugins. If you have other plugins that modify
the functionality of the *Add to Cart* button, this plugin may not work or
cause unintended consequences.

Development of this plugin is done at [Github](https://github.com/impossibledreams/wc-external-variations)

You can find this plugin at [WordPress.org](https://wordpress.org/plugins/wc-external-variations/)

You can make a donation to support this project [here](https://www.paypal.com/biz/fund?id=8JX776MCWU338)

== Installation ==

1. Make sure you are running WooCommerce v4.0 or higher.
2. Either add the plugin via **Plugins &gt; Add New**, place the entire
plugin into the '/wp-content/plugins/' directory or upload it via the
**Plugins &gt; Upload section**.
3. Activate the plugin through the **'Plugins'** menu in WordPress
4. Go into any variable product, and specify the external URL in a variation.
5. Save the product, view it, select the variation with the external URL and
click on *Add to Cart*.
6. A new window should open with the external URL.
7. To change settings, go to WooCommerce Settings -> Products -> External Variations.

== Upgrade Notice ==
Added ability to retrieve variation attributes and data elements within the "[wcev_var_field]" shortcode.
Added a check to make sure external variations cannot be added to the cart if Javascript fails.

== Known Issues ==
When the number of variations exceeds 30, the plugin stops working. This is due
to a WooCommerce limit and can be bypassed by using a filter as described here:
https://docs.woocommerce.com/document/change-limit-on-number-of-variations-for-dynamic-variable-product-dropdowns/

== Changelog ==
= 1.0.12 =
* Minor bug fixes

= 1.0.11 =
* Added ability to retrieve variation attributes and data elements within the "[wcev_var_field]" shortcode.
* Added a check to make sure external variations cannot be added to the cart if Javascript fails.
* Minor cleanup of internationalization code

= 1.0.10 =
* Fixed conflict with the Product Bundles extension

= 1.0.9 =
* Fixed encoding/decoding in the external URL
* Added a setting to open external link when the variation is selected

= 1.0.8 =
* Refactored Javascript code to fix issues with quick view plugins and themes (#9)
* Added donation link

= 1.0.7 =
* Tested with Wordpress v5.5 and WooCommerce v4.4.1
* Added ability to change the 'Add to Cart' text on global and variation-specific basis (#3).

= 1.0.6 =
* Added settings option to control whether links open in the same or new tab (#4).
* Tested with Wordpress v5.4.1 and WooCommerce v4.1.1

= 1.0.5 =
* Tested with Wordpress v5.3.1 and WooCommerce v3.8.1

= 1.0.4 =
* Tested with Wordpress v5.2, updated copyright dates

= 1.0.3 =
* Added shortcode for variation external status
* Remove the external SKU shortcode

= 1.0.2 =
* Added shortcodes for variation custom fields and post date

= 1.0.1 =
* Moved external SKU replacement into a shortcode
* Added a shortcode for product attributes

= 1.0.0 =
* Initial Release

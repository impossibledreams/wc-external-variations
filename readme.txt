=== WC External Variations ===
Contributors: impossibledreams, yakovsh
Tags: woocommerce, external, variations, variable
Requires at least: 4.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.7
Tested up to: 5.4.1
Stable tag: trunk
Requires PHP: 5.2.4

A WordPress plugin that adds basic support for external products to WooCommerce variations/variable products

== Description ==

Adds basic support for external products to WooCommerce
variations/variable products. This plugin allows you to define an external
URL on any variation and will then open that link in a new window when the user
clicks on the *Add To Cart* button. Because the Javascript is used for this,
any adding to cart that is done via the backend such as with APIs will not
redirect properly.

= Shortcode support  =

This plugin provides two shortcodes that you can use within your site:
1. [wcev_product_attr] - allows you to display an product attribute, use the "name" attribute to find the right attribute.
2. [wcev_var_field] - allows you to display a custom field for a variation, use the "id" attribute to point to the right field.
3. [wcev_var_postdate] - displays the formatted post date and time for a variation, doesn't take attributes.

= External SKU and status fields =

In addition to the external URL field, a second field called "External SKU" is provided for each variation. You can
set this field and then use the provided shortcode to display it. The purpose is to track the external SKU separately from the internal one.

There is also an external status field for tracking things like stock status separately from internal WooCommerce functionality.

= More Details =

Please note that this plugin has only been tested in a vanilla WordPress / WooCommerce
installation without any other plugins. If you have other plugins that modify
the functionality of the *Add to Cart* button, this plugin may not work or 
cause unintended consequences.

Development of this plugin is done at [Github](https://github.com/impossibledreams/wc-external-variations)

You can find this plugin at [WordPress.org](https://wordpress.org/plugins/wc-external-variations/)

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
This update adds settings to control whether links open in the same or new tab.

== Changelog ==

= 1.0.6 =
* Added settings option to control whether links open in the same or new tab
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

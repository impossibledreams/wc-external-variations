=== WC External Variations ===
Contributors: yakovsh
Tags: woocommerce, external, variations, variable
Requires at least: 4.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.7
Tested up to: 4.9.6
Stable tag: 1.0
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

The external URL field supports shortcodes which you can use to define standard
links across the entire installation.

= External SKU field =

A second field called "External SKU" is provided for each variation. You can
set this field and then use the placeholder *%externalsku%* within both
the external URL field and the variation description. The combination of this
substitution and the shortcode support would allow you to set a standard
set of URLs across the entire site, and then only change the external SKU within
each item.

= More Details =

Please note that this plugin has only been tested in a vanilla WordPress / WooCommerce
installation without any other plugins. If you have other plugins that modify
the functionality of the *Add to Cart* button, this plugin may not work or 
cause unintended consequences.

Development of this plugin is done at [Github](https://github.com/impossibledreams/wc-external-variations)

== Installation ==

1. Make sure you are running WooCommerce v3.4 or higher.
2. Either add the plugin via **Plugins &gt; Add New**, place the entire
plugin into the '/wp-content/plugins/' directory or upload it via the
**Plugins &gt; Upload section**.
3. Activate the plugin through the **'Plugins'** menu in WordPress
4. Go into any variable product, and specify the external URL in a variation.
5. Save the product, view it, select the variation with the external URL and
click on *Add to Cart*.
6. A new window should open with the external URL.

== Changelog ==

= 1.0.0 =
* Initial Release

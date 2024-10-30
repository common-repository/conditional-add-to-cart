=== Conditional Add To Cart for WooCommerce ===
Contributors: nlemsieh
Donate link: paypal.me/nlemsieh
Tags: woocommerce, add to cart, restrict, remove add to cart, add to cart rules, condition,  disable add to cart, replace add to cart, add to cart text, add to cart rules, add to cart country, add to cart login
Requires at least: 4.0
Tested up to: 6.6
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 0.2.5

Conditionally control the visibility and behavior, as well as customize the appearance and content of your "Add to cart" button in WooCommerce.

== Description ==

The "Conditional Add to Cart" plugin allows you to control the visibility and behavior, as well as customize the appearance and content of the "Add to cart" button in WooCommerce based on a set of conditions. This gives you greater control and flexibility over how the button behaves and appears in your WooCommerce store.

Currently available conditions:

**Product:** For individual products in your WooCommerce store, for example, you can disable the button for out-of-stock or discontinued products, or enable it only for products on sale.

**Product category:** For groups of products based on their category, for example, you can disable the button for all products in the "Clearance" category, or enable it only for products in the "Featured" category.

**Cart content:** Based on the contents of the customer's cart, for example, you can disable the button if the cart contains a certain product or category, or enable it only if the cart contains a minimum number of items.

**Cart quantity:** Based on the total quantity of items in the customer's cart, for example, you can disable the button if the cart contains more than a certain number of items, or enable it only if the cart contains a minimum quantity of items.

**Cart total:** Based on the total value of the customer's cart, for example, you can disable the button if the cart total is less than a certain amount, or enable it only if the cart total is above a certain threshold.

**Cart subtotal:** Based on the subtotal of the customer's cart (i.e. the total value of the items in the cart before any discounts or taxes are applied), for example, you can disable the button if the subtotal is less than a certain amount, or enable it only if the subtotal is above a certain threshold.

**User country:** Based on the customer's country, for example, you can disable the button for customers in certain countries, or enable it only for customers in specific regions.

**User role:** Based on the customer's user role, for example, you can enable the button only for  customers with a specific user role (e.g. "VIP", "Subscriber").

**User session status:** Based on the customer's session status, for example, you can disable the button for customers who are currently logged out, or enable it only for customers who are currently logged in.

You can trigger actions such as disabling or enabling the button, or changing its text and appearance. You can even replace the button entirely with your own custom HTML or shortcode. 

**NOTE:** More conditions and actions will be added in future updates, so if you have any feedback or feature requests, please feel free to share them on [our forum](https://wordpress.org/support/plugin/conditional-add-to-cart)

== Installation ==

1. Upload `conditional-add-to-cart` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Settings page - Setup conditions
2. Settings page - Set action to perform when conditions match.

== Changelog ==


= 0.2.5 =

- Declare compatibility with High-Performance Order Storage (HPOS)
- Declare compatibility with WooCommerce v8.7 and WP v6.5

= 0.2.4 =

- Add global theme support

= 0.2.3 =

- Add support for themes with custom product pages templates
- Declared compatibility with WooCommerce 6.3
- Fixed "Product" rule "Not equal to" not working properly.
- Fixed "Cart quantity" rule.


= 0.2.2 =

- Stability improvements


= 0.2.1 =

- Fixed "Cart total"  and "Cart subtotal" condition rules. 


= 0.2.0 =

- Added "Cart total"  and "Cart subtotal" condition rules. 

= 0.1.1 =

- Fixed a compatibility issue with WooCommerce 4.0
- Added new product condition. 

= 0.1.0 =

Initial release.

== Upgrade Notice ==

= 0.1.0 =
0.1.0 is the initial release of the plugin.

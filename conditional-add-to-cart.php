<?php

/**
 * Plugin Name:     Conditional Add To Cart
 * Plugin URI:      https://wordpress.org/plugins/conditional-add-to-cart
 * Description:     Conditionally restrict, remove, customize, or replace "Add to Cart" button for WooCommerce.
 * Author:          Nabil Lemsieh
 * Author URI:      https://wordpress.org/plugins/conditional-add-to-cart
 * Text Domain:     conditional-add-to-cart
 * Domain Path:     /languages
 * Version:         0.2.5
 * WC requires at least: 3.0.0
 * WC tested up to: 9.3
 *
 * @package         Conditional_Add_To_Cart
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

require_once( __DIR__ . '/vendor/autoload.php' );
use \ConditionalAddToCart\Plugin;

add_action('plugins_loaded', [ Plugin::instance(), 'run']);
    


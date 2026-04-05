<?php
/**
 * Plugin Name: Chopsticks
 * Description: Umami analytics for WordPress with optional WooCommerce ecommerce tracking.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: chopsticks
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHOPSTICKS_VERSION', '1.0.0' );
define( 'CHOPSTICKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'CHOPSTICKS_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, function () {
	add_option( 'chopsticks_settings', [
		'enabled'             => false,
		'website_id'          => '',
		'script_url'          => 'https://cloud.umami.is/script.js',
		'domains'             => '',
		'woocommerce_enabled' => false,
	] );
} );

add_action( 'plugins_loaded', function () {
	require_once CHOPSTICKS_DIR . 'includes/class-admin.php';
	require_once CHOPSTICKS_DIR . 'includes/class-frontend.php';

	new Chopsticks_Admin();
	new Chopsticks_Frontend();

	$settings = get_option( 'chopsticks_settings', [] );
	if ( ! empty( $settings['woocommerce_enabled'] ) && class_exists( 'WooCommerce' ) ) {
		require_once CHOPSTICKS_DIR . 'includes/class-woocommerce.php';
		new Chopsticks_WooCommerce();
	}
} );

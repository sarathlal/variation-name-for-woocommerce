<?php

/**
 *
 * @link              https://tinylab.dev
 * @since             1.0
 *
 * @wordpress-plugin
 * Plugin Name: Variation Name for WooCommerce
 * Description: Customize and display unique names for WooCommerce product variations, replacing dropdowns with descriptive names or options.
 * Plugin URI:        https://tinylab.dev
 * Version:           1.0
 * Author:            TinyLab
 * Author URI:        https://tinylab.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       variation-name-for-woocommerce
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 * Requires at least: 6.2
 * Tested up to: 6.6
 * WC requires at least: 8.9
 * WC tested up to: 9.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include the necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-vnwoo-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-vnwoo-public.php';

// Initialize the plugin
function vnwoo_init() {
    if (is_admin()) {
        new VNWOO_Admin(); // Initialize admin-specific functionality
    } else {
        new VNWOO_Public(); // Initialize public-specific functionality
    }
}
add_action('plugins_loaded', 'vnwoo_init');

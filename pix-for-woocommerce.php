<?php

/**
 * Plugin Name: InCuca Tech - Pix for WooCommerce
 * Plugin URI: https://github.com/InCuca/woocommerce-pix
 * Description: Accept payments with Pix technology.
 * Author: InCuca Tech
 * Author URI: https://incuca.net
 * Version: 1.2.0
 * Tested up to: 5.5.6
 * License: GNU General Public License v3.0
 *
 * @package Pix_For_WooCommerce
 */

defined('ABSPATH') or exit;

define( 'WC_PIX_VERSION', '1.2.0' );
define( 'WC_PIX_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_Pix' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-pix.php';
	add_action( 'plugins_loaded', array( 'WC_Pix', 'init' ) );
}
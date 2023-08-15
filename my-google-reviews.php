<?php
/**
 * Plugin Name: my Google Reviews
 * Plugin URI:  https://my.co.jp/
 * Description: Plugin for displaying Google reviews.
 * Version:     1.0.0
 * Author:      Rocket Martue
 * Author URI:  https://github.com/rocket-martue
 * License:     GPL2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Directory url of this plugin
 *
 * @var string
 */
define( 'MGR_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Directory path of this plugin
 *
 * @var string
 */
define( 'MGR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

require_once plugin_dir_path( __FILE__ ) . 'functions/functions.php';
require_once plugin_dir_path( __FILE__ ) . 'functions/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'functions/shortcode.php';

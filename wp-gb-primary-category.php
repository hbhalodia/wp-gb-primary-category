<?php
/**
 * Plugin Name:       WP GB Primary Category
 * Description:       WordPress plugin to add and select the primary category for classic as well as GB editors, stored in post meta and can use anytime in post.
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Hit Bhalodia
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-gb-primary-category
 *
 * @package            wp-gb-primary-category
 */

define( 'WP_GB_PRIMARY_CATEGORY_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WP_GB_PRIMARY_CATEGORY_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );


require_once WP_GB_PRIMARY_CATEGORY_PATH . '/inc/classes/class-primary-category-metabox.php';

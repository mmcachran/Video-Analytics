<?php
/**
 * @package   Video Analytics
 * @author    mmcachran
 * @license   GPL-2.0+
 *
 * Plugin Name: Video Analytics
 * Description: Embed multiple videos and track play using GA
 * Version:           0.5.0
 * Author:            mmcachran
 * Text Domain:       video_analytics
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_VIDEO_ANALYTICS_VERSION', '0.5.0' );

// Are we in DEV mode?
if ( ! defined( 'WP_VIDEO_ANALYTICS' ) ) {
	define( 'WP_VIDEO_ANALYTICS', true );
}

// load the plugin
require_once( plugin_dir_path( __FILE__ ) . 'lib/video-analytics.php' );	
add_action( 'plugins_loaded', array( 'Video_Analytics', 'get_instance' ) );
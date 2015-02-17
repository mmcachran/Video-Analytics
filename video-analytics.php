<?php
/**
 * Plugin Name: Video Analytics
 * Description: Embed multiple videos and track play using GA
 * Version:     0.3.0
 * Author:      mmcachran
 * License:     GPLv2+
 * Text Domain: video_analytics
 * Domain Path: /languages
 */

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
if( ! class_exists( 'Video_Analytics' ) ):

class Video_Analytics {
	const VERSION = '0.3.0';
	public static $url  = '';
	public static $path = '';
	public static $name = '';

	/**
	 * Sets up our plugin
	 * @since  0.1.0
	 */
	public function __construct() {
		// Useful variables
		self::$url  = trailingslashit( plugin_dir_url( __FILE__ ) );
		self::$path = trailingslashit( dirname( __FILE__ ) );
		self::$name = __( 'Video Analytics', 'video_analytics' );
	}
	
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );

		// Add JS to head
		add_action( 'wp_head', array( $this, 'do_video_analytics' ), 1 );
		
		// init process for button control
		add_filter( 'tiny_mce_version', 'my_refresh_mce' );
		add_action( 'init', array( $this, 'add_videos_button' ) );
		
		// add shortcode
		add_shortcode( 'video_analytics', array( $this, 'add_shortcode' ) );
	}

	/**
	 * Init hooks
	 * @since  0.1.0
	 * @return null
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'video_analytics' );
		load_textdomain( 'video_analytics', WP_LANG_DIR . '/video-analytics/video-analytics-' . $locale . '.mo' );
		load_plugin_textdomain( 'video_analytics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Add Video Analytics JS to wp_head()
	 * @since  0.1.0
	 * @return null
	 */
	public function do_video_analytics() {
		wp_enqueue_script( 'video-analytics', plugins_url( '/video-analytics.js', __FILE__ ), array( 'jquery' ) );
	}
	
	public function add_videos_button() {
		add_filter('mce_external_plugins', array( $this, 'add_video_tinymce_plugin' ) );
		add_filter('mce_buttons', array( $this, 'register_video_button' ) );
	}

	public function register_video_button($buttons) {
		array_push( $buttons, '|', 'video_analytics' );
		return $buttons;
	}

	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	public function add_video_tinymce_plugin( $plugin_array ) {
		$plugin_array['video_analytics'] = plugins_url( '/editor_plugin.js', __FILE__ );
		return $plugin_array;
	}
	
	public function add_shortcode( $params, $content = null ) {
		if(
			( isset( $params['video_id']) || isset($params['playlist_id']) ) 
			&& isset( $params['type'] )
		) {
			$html = '<div class="article-media"><div class="video-container">';
			
			if ( 'youtube' === $params['type'] ) {
				$id = isset ($params['video_id'] ) ? $params['video_id'] : $params['playlist_id'];
				$html .= '<div id="yt-frame-'.$id.'" data-key="'.$params['video_id'].'" data-playlist-key="'.$params['playlist_id'].'"></div>';
			}
		}
		
		return $html . '</div></div>';
	}
}

// init our class
$video_analytics = new Video_Analytics();
$video_analytics->hooks();

endif;
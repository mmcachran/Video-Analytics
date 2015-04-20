<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Video_Analytics {
	const VERSION = '0.4.0';

	public static 
		$url,
		$path,
		$name;

	/**
	 * Instance of this class.
	 *
	 * @since   0.4.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.4.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Sets up our plugin
	 * @since  0.1.0
	 */
	private function __construct() {
		// Useful variables
		self::$url  = trailingslashit( plugin_dir_url( __FILE__ ) );
		self::$path = trailingslashit( dirname( __FILE__ ) );
		self::$name = __( 'Video Analytics', 'video_analytics' );

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
		wp_enqueue_script( 'video-analytics', self::$url . 'video-analytics.js', array( 'jquery' ) );
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
		$plugin_array['video_analytics'] = self::$url . 'editor_plugin.js';
		return $plugin_array;
	}
	
	public function add_shortcode( $params, $content = null ) {
		if(
			( isset( $params['video_id'] ) || isset( $params['playlist_id'] ) ) 
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
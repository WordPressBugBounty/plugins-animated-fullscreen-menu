<?php
// Create a class that will handle some date of the plugin. starting with Heatmap (saving some data in the database) and display it in the wp-admin panel
// Path: inc/class-analytics.php
// Compare this snippet from animated-fullscreen-menu.php:
//  * Author URI: https://wp-fullscreen-menu.com/
//  * Text Domain: animated-fullscreen-menu
//  * Domain Path: /languages
//  **/
//
if ( ! defined( 'ABSPATH' ) || ! function_exists( 'add_action' ) ) {
	exit;
}

// Create a class AFSMenu_Analytics
class AFSMenu_Analytics {
	public $analytics_table_version = '1.0.3';
	public $analytics_table_name    = 'animatedfsmenu_analytics';
	// Create a function that will handle the data
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'check_analyitics_db' ] );

		// Register the REST route
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_analytics_js' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_analytics_js' ] );

		add_action( 'admin_init', [ $this, 'render_heatmap' ] );

		add_filter( 'animatedfsmenu_cmb2_tabs', [ $this, 'add_analytics_tab' ], 10, 1 );
		add_action( 'animatedfsm_add_fields', [ $this, 'add_analytics_fields' ], 999, 2 );
		
	}

	public function enqueue_analytics_js() {
		wp_enqueue_script( 'animatedfsmenu-analytics', plugin_dir_url( dirname( __FILE__ ) ) . 'frontend/js/heatmap.js', array(), '1.0.0', true );
		
	}

	public function admin_enqueue_analytics_js() {
		wp_enqueue_script( 'animatedfsmenu-analytics', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/cmb2.js', array(), '1.0.0', true );
		wp_enqueue_script( 'animatedfsmenu-analytics', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/heatmap.js', array(), '1.0.0', true );
		
	
	}
	// Create a function that will handle the data
	public function add_analytics_tab( $cmb2_args ) {

		$cmb2_args['tabs']['analytics'] = array(
			'label' => __( 'Analytics', 'animated-fullscreen-menu' ),
			'icon'  => 'dashicons-chart-area',
		);

		return $cmb2_args;
	}

	public function save_heatmap( WP_REST_Request $request ) {
		global $wpdb;
		$atts = $request->get_params();

		$atts = isset( $atts['data'] ) ? $atts['data'] : false;
		if ( ! $atts ) {
			return new WP_REST_Response( 'No data to save.', 400 );
		}
		$atts = json_decode($atts, true);
		
		$current_time = current_time( 'mysql' );

		$data = array(
			'time'   => $current_time,
			'data'   => json_encode(
				array(
					'coordinates' => array(
						'x' => $atts['percentageCoordinates']['x'],
						'y' => $atts['percentageCoordinates']['y'],
					),
					''        => array(
						'title' => $atts['pageName'],
						'url'   => $atts['pageUrl'],
					),
				),
			),
			'device' => $atts['isMobile'] ? 'mobile' : 'desktop',
		);

		// Insert the data into the database
		$wpdb->insert( $wpdb->prefix . $this->analytics_table_name, $data );

		return new WP_REST_Response( 'Analytics data saved successfully.', 200 );
	}

	// Create a table to save my data, if it doesn't exist
	public function create_analytics_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->analytics_table_name;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			data longtext NOT NULL,
			device varchar(255) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'animatedfsmenu_analytics_db_version', $this->analytics_table_version );

	}

	public function check_analyitics_db() {
		if ( get_site_option( 'animatedfsmenu_analytics_db_version' ) != $this->analytics_table_version ) {
				$this->create_analytics_table();
		}
	}

	public function register_rest_route() {
		register_rest_route( 'animatedfsmenu/v1', '/saveheatmap', array(
			'methods'  => 'GET',
			'callback' => [ $this, 'save_heatmap' ],
		) );
	}


	public function render_heatmap() {
		if ( current_user_can( 'administrator' ) ) {
			add_action( 'wp_ajax_render_heatmap', [ $this, 'ajax_render_heatmap' ] );
			add_action( 'wp_ajax_nopriv_render_heatmap', [ $this, 'ajax_render_heatmap' ] );
		}

	}

	public function ajax_render_heatmap() {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->analytics_table_name;
		$heatmap_data = $wpdb->get_results( "SELECT * FROM $table_name" );
		$heatmap_data = json_encode( $heatmap_data );


		wp_send_json_success( $heatmap_data );
	}

	public function add_analytics_fields( $cmb_options, $pro_user ) {

		
		$cmb_options->add_field( array(
			'name' => __( 'Heatmap', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsmenu_heatmap',
			'type' => 'text',
			'tab'  => 'analytics',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),

	
		) );
	}
}

new AFSMenu_Analytics();
<?php

/**
 * Plugin Name: Fullscreen Menu
 * Plugin URI: animated-fullscreen-menu
 * Description: Fullscreen Menu for your Website. Create a fullscreen menu with a nice animation effect and a mobile friendly navigation. Customize the menu colors, fonts, background, animations, buttons and more.
 * Author: Samuel Silva
 * Version: 2.8.0
 * Author URI: https://wp-fullscreen-menu.com/
 * Text Domain: animated-fullscreen-menu
 * Domain Path: /languages
 **/

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'add_action' ) ) {
	exit;
}

function animatedfsmenu_load_textdomain() {
	load_plugin_textdomain( 'animated-fullscreen-menu', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}


function animatedfsmenu_get_plugin_version() {
	$plugin_data = get_plugin_data( __FILE__ );
	return $plugin_version = $plugin_data['Version'];
}

add_action( 'plugins_loaded', 'animatedfsmenu_load_textdomain' );

if ( ! function_exists( 'animatedfsm' ) ) {
	// Create a helper function for easy SDK access.
	function animatedfsm() {

		global $animatedfsm;
		if ( ! isset( $animatedfsm ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';
			$pro = false;

			$animatedfsm = fs_dynamic_init( array(
				'id'                  => '3887',
				'slug'                => 'animated-fullscreen-menu',
				'premium_slug'        => 'animated-fullscreen-menu',
				'type'                => 'plugin',
				'public_key'          => 'pk_95d707fced75c19ff9b793853ac8a',
				'is_premium'          => $pro,
				'premium_suffix'      => ( $pro ? 'Pro' : 'Free' ),
				'has_premium_version' => $pro,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'trial'               => array(
					'days'               => 7,
					'is_require_payment' => true,
				),
				'menu'                => array(
					'slug'           => 'animatedfsm_settings',
					'first-path'     => 'admin.php?page=animatedfsm_settings',
					'contact'        => ( $pro ? true : false ),
					'support'        => true,
				),

			) );
		}

		return $animatedfsm;
	}

	// Init Freemius.
	animatedfsm();
	// Signal that SDK was initiated.
	do_action( 'animatedfsm_loaded' );

	animatedfsm()->override_i18n(
		array(
			'start-trial' => __( 'Free 7 Day Pro Trial', 'interactive-geo-maps' ),
			'upgrade'     => __( 'Get Pro Features', 'interactive-geo-maps' ),
		)
	);

}


class AnimatedfsMenu {

	//register plugin
	function register() {
		require_once dirname( __FILE__ ) . '/cmb.php';
		//require_once dirname( __FILE__ ) . '/inc/class-analytics.php';
		add_action( 'init', array( $this, 'register_menu' ) );
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

	}
	
	function activate() {

	}

	function register_menu() {
		register_nav_menu( 'animated-fullscreen-menu', __( 'Fullscreen Menu', 'animated-fullscreen-menu' ) );
	}
	function register_block() {
		register_block_type( __DIR__ );
	}

	// Enqueue the json object from get_option( 'animatedfsm_settings' ) in the backend (Menu Editor, gutenberg only)
	function enqueue_block_editor_assets() {
	
		wp_localize_script(
			'animated-fullscreen-menu-hamburger-editor-script',
			'animatedfsmenu',
			array(
				'animatedfsmenu_settings' => get_option( 'animatedfsm_settings' ),
			)
		);
	}
}


if ( class_exists( 'AnimatedfsMenu' ) ) {
	$animated_fs_menu = new AnimatedfsMenu();
	$animated_fs_menu->register();
}


register_activation_hook( __FILE__, array( $animated_fs_menu, 'activate' ) );


if ( isset( get_option( 'animatedfsm_settings' )['animatedfsm_on'] ) && 'on' === get_option( 'animatedfsm_settings' )['animatedfsm_on'] ) {
	require_once dirname( __FILE__ ) . '/frontend-animatedfsmenu.php';
}


if ( ! empty( $_GET['afs_preview_menu'] ) && 'true' === $_GET['afs_preview_menu'] ) {
	require_once dirname( __FILE__ ) . '/frontend-animatedfsmenu.php';
	
}
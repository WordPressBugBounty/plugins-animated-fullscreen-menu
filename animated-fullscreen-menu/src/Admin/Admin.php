<?php
/**
 * Admin Bootstrap Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Admin;

use AnimatedFullscreenMenu\Utilities\Options;

/**
 * Handles all admin-related functionality.
 */
class Admin {

	/**
	 * Options instance.
	 *
	 * @var Options
	 */
	private Options $options;

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	private string $plugin_path;

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	private string $plugin_url;

	/**
	 * Constructor.
	 *
	 * @param Options $options     Options instance.
	 * @param string  $plugin_path Plugin directory path.
	 * @param string  $plugin_url  Plugin directory URL.
	 */
	public function __construct( Options $options, string $plugin_path, string $plugin_url ) {
		$this->options     = $options;
		$this->plugin_path = $plugin_path;
		$this->plugin_url  = $plugin_url;

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		// Load CMB2 bootstrap on init with high priority (after default init actions).
		// CMB2 itself hooks to init with priority 9958 to load its includes.
		// We load the bootstrap early, but CMB2's actual loading happens at its priority.
		// This is AFTER plugins_loaded where textdomain is loaded, fixing the warning.
		add_action( 'init', array( $this, 'load_cmb2_bootstrap' ), 9 );

		// Load settings configuration on cmb2_admin_init (fired by CMB2 during admin_init).
		add_action( 'cmb2_admin_init', array( $this, 'load_cmb2_settings' ) );

		// Enqueue admin assets.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Add admin notices.
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
	}

	/**
	 * Load CMB2 bootstrap file.
	 * This runs on init (priority 9) to load CMB2's bootstrap.
	 * CMB2 will then load its core on init priority 9958.
	 * This is AFTER plugins_loaded, fixing the _load_textdomain_just_in_time warning.
	 *
	 * @return void
	 */
	public function load_cmb2_bootstrap(): void {
		// Load CMB2 bootstrap - this sets up CMB2 to load on init at priority 9958.
		$cmb2_init = $this->plugin_path . 'vendor/CMB2/init.php';
		if ( file_exists( $cmb2_init ) && ! defined( 'CMB2_LOADED' ) ) {
			require_once $cmb2_init;
		}

		// Load CMB2 extensions.
		$this->load_cmb2_extensions();
	}

	/**
	 * Load CMB2 settings configuration.
	 * This runs on cmb2_admin_init hook (fired by CMB2 during admin_init).
	 *
	 * This will be replaced with native Settings API in Phase 2.
	 *
	 * @return void
	 */
	public function load_cmb2_settings(): void {
		// Load the settings configuration file.
		$cmb_file = $this->plugin_path . 'cmb.php';
		if ( file_exists( $cmb_file ) ) {
			require_once $cmb_file;
		}

		// Call the registration function directly.
		// We can't use add_action('cmb2_admin_init', ...) because that hook
		// is already firing when this method runs.
		if ( function_exists( 'animatedfsmenu_register_theme_options_metabox' ) ) {
			animatedfsmenu_register_theme_options_metabox();
		}
	}

	/**
	 * Load CMB2 extension libraries.
	 *
	 * @return void
	 */
	private function load_cmb2_extensions(): void {
		// CMB2 Tabs.
		$tabs_file = $this->plugin_path . 'vendor/cmb2-tabs/cmb2-tabs.php';
		if ( file_exists( $tabs_file ) ) {
			require_once $tabs_file;
		}

		// CMB2 Conditionals.
		$conditionals_file = $this->plugin_path . 'vendor/cmb2-conditionals/cmb2-conditionals.php';
		if ( file_exists( $conditionals_file ) ) {
			require_once $conditionals_file;
		}

		// CMB2 Font Awesome Icon Select.
		$faiconselect_file = $this->plugin_path . 'vendor/cmb2-field-faiconselect/cmb2_field_faiconselect.php';
		if ( file_exists( $faiconselect_file ) ) {
			require_once $faiconselect_file;
		}
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		// Only load on plugin settings page.
		if ( 'toplevel_page_animatedfsm_settings' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'animatedfsm-admin',
			$this->plugin_url . 'admin/css/styles.css',
			array(),
			$this->get_version()
		);

		wp_enqueue_script(
			'animatedfsm-admin',
			$this->plugin_url . 'admin/js/cmb2.js',
			array( 'jquery' ),
			$this->get_version(),
			true
		);
	}

	/**
	 * Display admin notices.
	 *
	 * @return void
	 */
	public function display_notices(): void {
		// PHP version notice.
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			$this->render_notice(
				sprintf(
					/* translators: %s: Required PHP version */
					__( 'Fullscreen Menu requires PHP version 7.4 or higher. You are running PHP %s.', 'animated-fullscreen-menu' ),
					PHP_VERSION
				),
				'error'
			);
		}
	}

	/**
	 * Render an admin notice.
	 *
	 * @param string $message Notice message.
	 * @param string $type    Notice type (error, warning, success, info).
	 * @return void
	 */
	private function render_notice( string $message, string $type = 'info' ): void {
		printf(
			'<div class="notice notice-%s"><p><strong>%s:</strong> %s</p></div>',
			esc_attr( $type ),
			esc_html__( 'Fullscreen Menu', 'animated-fullscreen-menu' ),
			esc_html( $message )
		);
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	private function get_version(): string {
		return \AnimatedFullscreenMenu\Plugin::VERSION;
	}
}

<?php
/**
 * Main Plugin Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu;

use AnimatedFullscreenMenu\Admin\Admin;
use AnimatedFullscreenMenu\Frontend\Frontend;
use AnimatedFullscreenMenu\Blocks\BlockManager;
use AnimatedFullscreenMenu\Integrations\Freemius as FreemiusIntegration;
use AnimatedFullscreenMenu\Utilities\Options;

/**
 * Main plugin class using singleton pattern.
 */
final class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public const VERSION = '3.0.0';

	/**
	 * Minimum PHP version required.
	 *
	 * @var string
	 */
	public const MIN_PHP_VERSION = '7.4';

	/**
	 * Minimum WordPress version required.
	 *
	 * @var string
	 */
	public const MIN_WP_VERSION = '5.0';

	/**
	 * Plugin instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Options instance.
	 *
	 * @var Options
	 */
	private Options $options;

	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	private string $plugin_path;

	/**
	 * Plugin directory URL.
	 *
	 * @var string
	 */
	private string $plugin_url;

	/**
	 * Plugin base file.
	 *
	 * @var string
	 */
	private string $plugin_file;

	/**
	 * Get the singleton instance.
	 *
	 * @param string $plugin_file Main plugin file path.
	 * @return Plugin
	 */
	public static function instance( string $plugin_file = '' ): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_file );
		}
		return self::$instance;
	}

	/**
	 * Private constructor.
	 *
	 * @param string $plugin_file Main plugin file path.
	 */
	private function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_path = plugin_dir_path( $plugin_file );
		$this->plugin_url  = plugin_dir_url( $plugin_file );
		$this->options     = new Options();

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		// Load textdomain.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Register menu location.
		add_action( 'init', array( $this, 'register_menu_location' ) );

		// Check version and run migrations.
		add_action( 'plugins_loaded', array( $this, 'check_version' ), 20 );

		// Initialize components after plugins are loaded.
		add_action( 'plugins_loaded', array( $this, 'init_components' ), 25 );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'animated-fullscreen-menu',
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/languages'
		);
	}

	/**
	 * Register the menu location.
	 *
	 * @return void
	 */
	public function register_menu_location(): void {
		register_nav_menu(
			'animated-fullscreen-menu',
			__( 'Fullscreen Menu', 'animated-fullscreen-menu' )
		);
	}

	/**
	 * Check plugin version and run migrations if needed.
	 *
	 * @return void
	 */
	public function check_version(): void {
		$current_version = get_option( 'animatedfsm_version', '0.0.0' );

		if ( version_compare( $current_version, self::VERSION, '<' ) ) {
			// Backup current settings before any migration.
			$current_settings = get_option( 'animatedfsm_settings', array() );
			if ( ! empty( $current_settings ) ) {
				update_option( 'animatedfsm_settings_backup_' . $current_version, $current_settings );
			}

			// Update version number.
			update_option( 'animatedfsm_version', self::VERSION );

			// Hook for future migrations.
			do_action( 'animatedfsm_after_update', $current_version, self::VERSION );
		}
	}

	/**
	 * Initialize plugin components.
	 *
	 * @return void
	 */
	public function init_components(): void {
		// Initialize Freemius integration.
		new FreemiusIntegration( $this->plugin_file );

		// Initialize admin.
		if ( is_admin() ) {
			new Admin( $this->options, $this->plugin_path, $this->plugin_url );
		}

		// Initialize frontend (conditional based on settings).
		new Frontend( $this->options, $this->plugin_path, $this->plugin_url );

		// Initialize blocks.
		new BlockManager( $this->options, $this->plugin_path, $this->plugin_url );
	}

	/**
	 * Get the options instance.
	 *
	 * @return Options
	 */
	public function get_options(): Options {
		return $this->options;
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function get_plugin_path(): string {
		return $this->plugin_path;
	}

	/**
	 * Get the plugin URL.
	 *
	 * @return string
	 */
	public function get_plugin_url(): string {
		return $this->plugin_url;
	}

	/**
	 * Get the plugin file.
	 *
	 * @return string
	 */
	public function get_plugin_file(): string {
		return $this->plugin_file;
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return self::VERSION;
	}

	/**
	 * Prevent cloning.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 *
	 * @return void
	 * @throws \Exception When attempting to unserialize.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}
}

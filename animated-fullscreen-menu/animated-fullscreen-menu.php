<?php
/**
 * Plugin Name: Fullscreen Menu
 * Plugin URI: animated-fullscreen-menu
 * Description: Fullscreen Menu for your Website. Create a fullscreen menu with a nice animation effect and a mobile friendly navigation. Customize the menu colors, fonts, background, animations, buttons and more.
 * Author: Samuel Silva
 * Version: 3.0.3
 * Author URI: https://samuelsilva.pt/
 * Text Domain: animated-fullscreen-menu
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 *
 * @package AnimatedFullscreenMenu
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) || ! function_exists( 'add_action' ) ) {
	exit;
}

// Define plugin constants.
define( 'ANIMATEDFSM_VERSION', '3.0.1' );
define( 'ANIMATEDFSM_PLUGIN_FILE', __FILE__ );
define( 'ANIMATEDFSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANIMATEDFSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check PHP version compatibility.
 *
 * @return bool
 */
function animatedfsm_check_php_version() {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action( 'admin_notices', 'animatedfsm_php_version_notice' );
		return false;
	}
	return true;
}

/**
 * Display PHP version error notice.
 *
 * @return void
 */
function animatedfsm_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<strong><?php esc_html_e( 'Fullscreen Menu', 'animated-fullscreen-menu' ); ?>:</strong>
			<?php
			printf(
				/* translators: %s: Required PHP version */
				esc_html__( 'This plugin requires PHP version 7.4 or higher. You are running PHP %s. Please contact your hosting provider to upgrade.', 'animated-fullscreen-menu' ),
				esc_html( PHP_VERSION )
			);
			?>
		</p>
	</div>
	<?php
}

// Check PHP version before proceeding.
if ( ! animatedfsm_check_php_version() ) {
	return;
}

/**
 * Load Composer autoloader.
 *
 * @return bool
 */
function animatedfsm_load_autoloader() {
	$autoloader = ANIMATEDFSM_PLUGIN_DIR . 'vendor/autoload.php';

	if ( file_exists( $autoloader ) ) {
		require_once $autoloader;
		return true;
	}

	// Fallback: manually require classes if autoloader doesn't exist.
	// This ensures the plugin works even without running composer.
	animatedfsm_manual_autoload();
	return true;
}

/**
 * Manual autoload fallback for when Composer autoload is not available.
 *
 * @return void
 */
function animatedfsm_manual_autoload() {
	spl_autoload_register(
		function ( $class ) {
			// Only handle our namespace.
			$prefix = 'AnimatedFullscreenMenu\\';
			$len    = strlen( $prefix );

			if ( strncmp( $prefix, $class, $len ) !== 0 ) {
				return;
			}

			// Get the relative class name.
			$relative_class = substr( $class, $len );

			// Build the file path.
			$file = ANIMATEDFSM_PLUGIN_DIR . 'src/' . str_replace( '\\', '/', $relative_class ) . '.php';

			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	);
}

/**
 * Initialize the plugin.
 *
 * @return void
 */
function animatedfsm_init() {
	// Load autoloader.
	animatedfsm_load_autoloader();

	// Initialize the plugin.
	\AnimatedFullscreenMenu\Plugin::instance( ANIMATEDFSM_PLUGIN_FILE );
}

// Initialize on plugins_loaded to ensure all dependencies are available.
add_action( 'plugins_loaded', 'animatedfsm_init', 10 );

/**
 * Plugin activation hook.
 *
 * @return void
 */
function animatedfsm_activate() {
	// Ensure autoloader is available.
	animatedfsm_load_autoloader();

	// Set default options if not exists.
	if ( false === get_option( 'animatedfsm_settings' ) ) {
		update_option( 'animatedfsm_settings', array() );
	}

	// Store activation time for potential future use.
	if ( false === get_option( 'animatedfsm_activation_time' ) ) {
		update_option( 'animatedfsm_activation_time', time() );
	}

	// Flush rewrite rules.
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'animatedfsm_activate' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function animatedfsm_deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'animatedfsm_deactivate' );

/**
 * Global helper function for Freemius backward compatibility.
 *
 * This function provides backward compatibility with existing code that uses animatedfsm().
 *
 * @return \Freemius|null
 */
if ( ! function_exists( 'animatedfsm' ) ) {
	function animatedfsm() {
		// Ensure autoloader is loaded.
		if ( ! class_exists( '\AnimatedFullscreenMenu\Integrations\Freemius' ) ) {
			animatedfsm_load_autoloader();
		}

		return \AnimatedFullscreenMenu\Integrations\Freemius::get_instance();
	}
}

/**
 * Helper function to get plugin version.
 *
 * Backward compatibility wrapper.
 *
 * @return string
 */
function animatedfsmenu_get_plugin_version() {
	return ANIMATEDFSM_VERSION;
}

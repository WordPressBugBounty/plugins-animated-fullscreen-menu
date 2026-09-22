<?php
/**
 * Uninstaller Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Utilities;

/**
 * Removes plugin data on uninstall when the user opted in.
 *
 * Runs through the Freemius `after_uninstall` action (see Integrations\Freemius)
 * rather than an uninstall.php file. WordPress runs uninstall.php INSTEAD of the
 * registered uninstall hook, which would silently bypass the SDK's own handler.
 */
class Uninstaller {

	/**
	 * Delete plugin data if "Remove data on uninstall" is enabled.
	 *
	 * @return void
	 */
	public static function run(): void {
		$settings = get_option( 'animatedfsm_settings', array() );

		// The option is missing when the plugin was never configured, so guard the lookup.
		if ( ! is_array( $settings ) || empty( $settings['animatedfsm_removedata_on'] ) ) {
			return;
		}

		global $wpdb;

		delete_option( 'animatedfsm_settings' );
		delete_option( 'animatedfsm_version' );
		delete_option( 'animatedfsm_activation_time' );

		// Settings backups are created on every version upgrade (see Plugin::check_version()).
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( 'animatedfsm_settings_backup_' ) . '%'
			)
		);
	}
}

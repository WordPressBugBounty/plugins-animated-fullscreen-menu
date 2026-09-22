<?php
/**
 * @package AnimatedfsMenu
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$animatedfsm_settings = get_option( 'animatedfsm_settings', array() );

// The option is missing when the plugin was never configured, so guard the lookup.
if ( is_array( $animatedfsm_settings ) && ! empty( $animatedfsm_settings['animatedfsm_removedata_on'] ) ) {
	global $wpdb;

	delete_option( 'animatedfsm_settings' );
	delete_option( 'animatedfsm_version' );

	// Settings backups are created on every version upgrade (see Plugin::check_version()).
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( 'animatedfsm_settings_backup_' ) . '%'
		)
	);
}
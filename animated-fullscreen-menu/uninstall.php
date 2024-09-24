<?php
/**
 * @package AnimatedfsMenu
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$animatedfsm_settings = get_option( 'animatedfsm_settings' );

if ( $animatedfsm_settings['animatedfsm_removedata_on'] ) {
	delete_option( 'animatedfsm_settings' );
}
<?php
/**
 * Trigger this file on Plugin uninstall
 * @package EvelynApi
 */

namespace Inc\Base;

class Deactivate {
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'autoSyncOrdersWithEvelyn' );
    	wp_unschedule_event( $timestamp, 'autoSyncOrdersWithEvelyn' );

    	flush_rewrite_rules();
	}
}
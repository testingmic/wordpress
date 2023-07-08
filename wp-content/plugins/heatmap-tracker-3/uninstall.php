<?php
/**
 * Trigger this file on Plugin uninstall
 * @package HeatmapTracker
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

$option_name = '_heatmap_data';

delete_option( $option_name );

// for site options in Multisite
delete_site_option( $option_name );
<?php
/**
 * Plugin Name: Heatmap Revenue Tracker
 * Plugin URI: https://heatmap.com/
 * Description: Heatmap.com is a premium heatmap solution that tracks revenue from clicks, scroll-depth, and any customer interaction on your website. Heatmap.com gives you access to on-site metrics that you can’t see in any other heatmap solutions, primarily around revenue attribution.
 * Version: 1.1
 * Author: Heatmap.com
 * Author URI: https://heatmap.com/
 * Text Domain: heatmap-tracker
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * 
 * @package HeatmapTracker
*/
use Inc\HeatmapTracker;
use Inc\Base\HeatmapActivate;
use Inc\Base\HeatmapDeactivate;

defined( 'ABSPATH' ) or die('Hey, what are you doing here? Silly human!');

if(file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}

defined('HEATMAP_APP_URL') or define('HEATMAP_APP_URL', 'https://dashboard.heatmap.com/index.php?module=API&method=PaymentIntegration.getIdByURL&platform=wordpress');

// Include the main HeatmapTracker class.
if ( ! defined( 'HT_PLUGIN_FILE' ) ) {
	define( 'HT_PLUGIN_FILE', __DIR__ );
	define( 'HT_DIR_NAME', basename(dirname(__FILE__)));
	define( 'HT_PLUGIN_ROOT_DIR', plugin_basename(__FILE__));
}

function heatmapActivateHeatmap() {
	HeatmapActivate::activate();
}

function heatmapDeActivateHeatmap() {
	HeatmapDeactivate::deactivate();
}

register_activation_hook(__FILE__, 'heatmapActivateHeatmap');
register_deactivation_hook(__FILE__, 'heatmapDeActivateHeatmap');
  
if( class_exists('Inc\\HeatmapTracker')) {
	(new HeatmapTracker)->init();
}
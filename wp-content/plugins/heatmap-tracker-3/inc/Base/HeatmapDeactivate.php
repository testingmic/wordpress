<?php
namespace Inc\Base;

class HeatmapDeactivate {

	public static function deactivate() {
		update_option('_heatmap_data', '');
    	flush_rewrite_rules();
	}
	
}
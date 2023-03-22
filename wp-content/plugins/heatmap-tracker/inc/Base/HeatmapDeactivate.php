<?php
namespace Inc\Base;

class HeatmapDeactivate {

	public static function deactivate() {
    	flush_rewrite_rules();
	}
	
}
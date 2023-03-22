<?php
namespace Inc\Base;

class HeatmapActivate {

	public static function activate() {
		flush_rewrite_rules();
	}
	
}
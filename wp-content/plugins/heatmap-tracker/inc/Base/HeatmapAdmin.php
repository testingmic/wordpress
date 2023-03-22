<?php 
namespace Inc\Base;

class HeatmapAdmin {

    private $plugin_name = 'Heamap Tracker';

    public function heatmapAdminMenu() {
        add_menu_page(
            $this->plugin_name,
            $this->plugin_name, 
            'administrator', 
            'heatmap-tracker', [$this, 'displayPluginAdminDashboard'], 
            'dashicons-chart-area', 70
        );
    }

    public function displayPluginAdminDashboard() {

    }

}
?>
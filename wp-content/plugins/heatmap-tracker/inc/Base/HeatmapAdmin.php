<?php 
namespace Inc\Base;

use Inc\HeatmapTracker;
class HeatmapAdmin extends HeatmapTracker {

    public function HeatmapAdminMenu() {
        add_menu_page(
            $this->plugin_label, $this->plugin_label, 
            'administrator', $this->plugin_name, 
            [$this, 'DisplayHeatmapAdminDashboard'], 'dashicons-chart-area', 70
        );
        wp_enqueue_style('evelynpos', $this->plugin_url . $this->plugin_name . '/templates/assets/style.css');
    }

    public function HeatmapAdminSettingLink( array $links ) {
        $url = get_admin_url() . "admin.php?page={$this->plugin_name}";
        $settings_link = '<a href="' . $url . '">' . __('Settings', 'textdomain') . '</a>';
          $links[] = $settings_link;
        return $links;
    }

    public function DisplayHeatmapAdminDashboard() {
        $variables = [
            'site_name'     => $this->site_name,
            'description'   => $this->description,
            'plugin_option' => $this->plugin_option
        ];
        require_once HT_PLUGIN_FILE . '/templates/heatmap-tracker-settings.php';
    }

}
?>
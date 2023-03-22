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

    public function HeatmapManageSettings() {

        if(!isset($_POST['status'])) {
            wp_die("unknown_request");
        }

        $status = (int) substr($_POST['status'], 0, 2);
        if(!in_array($status, [1, 2])) {
            wp_die("unknown_request");
        }

        if($status == 2) {
            update_option($this->option_name, '');
        }
        else {
            $content = $this->HeatmapHeatagScript(true);
            if(isset($content['idsite'])) {
                $content['heatLastTimestamp'] = time();
                $content['heatLastUpdated'] = date('l, F, jS Y');
                update_option($this->option_name, json_encode($content));
            }
        }

        flush_rewrite_rules();

        wp_die("option_updated");
    }

    public function DisplayHeatmapAdminDashboard() {
        
        $plugin_option = get_option('_heatmap_data');
        $plugin_option = !empty($plugin_option) ? json_decode($plugin_option, true) : [];

        $variables = [
            'plugin_url'    => $this->plugin_url,
            'plugin_name'   => $this->plugin_name,
            'site_name'     => $this->site_name,
            'description'   => $this->description,
            'plugin_option' => $plugin_option
        ];
        require_once HT_PLUGIN_FILE . '/templates/heatmap-tracker-settings.php';
    }
}
?>
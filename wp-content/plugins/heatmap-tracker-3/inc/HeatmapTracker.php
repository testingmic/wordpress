<?php
namespace Inc;

use WP_Http_Curl;

use Inc\Base\HeatmapAdmin;

class HeatmapTracker {

    public $plugin;
	public $plugin_url;
    public $plugin_option;
    public $site_name = 'Heatmap.com';
    public $option_name = '_heatmap_data';
    public $plugin_label = 'Heatmap.com';
    public $plugin_name = 'heatmap-tracker';

    public $description = 'Heatmap.com is a premium heatmap solution that tracks revenue from clicks, 
        scroll-depth, and any customer interaction on your website.';

    public function __construct() {
    
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/heatmap-tracker.php';

    }

	public function init() {

        $this->plugin_option = get_option($this->option_name);

        if(!empty($this->plugin_option)) {
            add_action('wp_head', [$this, 'HeatmapHeatagScript'], 10);
            if ( has_action( 'woocommerce_checkout_order_processed' ) ) {
                add_action('woocommerce_checkout_order_processed', [$this, 'HeatmapTrackOrder'], 5, 1);
            }
            if( has_action('woocommerce_checkout_order_processed') ) {
                add_action('edd_complete_purchase', ['pw_edd_on_complete_purchase']);
            }

            foreach(['refunded', 'failed', 'cancelled', 'completed'] as $status) {
                $actionHook = "woocommerce_order_status_{$status}";
                $modelName = "HeatmapOrder" . ucwords($status);
                if(method_exists($this, $modelName)) {
                    add_action($actionHook, [$this, $modelName], 5, 1);
                }
            }

        }

        if(current_user_can('activate_plugins')) {      
            $adminObject = new HeatmapAdmin();
            add_filter('plugin_action_links_' . HT_PLUGIN_ROOT_DIR, [$adminObject, 'HeatmapAdminSettingLink']);
            add_action('admin_menu', [$adminObject, 'HeatmapAdminMenu'], 9);
            add_action('wp_ajax_HeatmapManageSettings', [$adminObject, 'HeatmapManageSettings']);
        }

    }

    private function HeatmapURL($url, $hostOnly = false) {

        $parse = parse_url($url);
        $url = ($parse['scheme'] ?? "https") . "://" . $parse['host'] . (
            (isset($parse['port']) && !in_array($parse['port'], [80, 443]) ? ":{$parse['port']}" : null)
        ) . ($parse['path'] ?? null);
    
        $url = trim(str_ireplace("www.", "", $url), '/');
    
        return $hostOnly ? $parse['scheme'] . "://" . $parse['host'] : $url;
    
    }
    
    private function HeatmapCURL($apiURL, $data = [], $method = 'POST') {
        
        try {

            $request = (new WP_Http_Curl)->request($apiURL, [
                'method'        => $method,
                'timeout'       => 10,
                'body'          => $data,
                'stream'        => 0,
                'filename'      => null,
                'decompress'    => false,
                'headers'       => [
                    'User-Agent' => $_SERVER["HTTP_USER_AGENT"],
                    'Content:Type: application/json'
                ]
            ]);
    
            return $request['body'] ?? "";
            
        } catch(\Exception $e) {
            return "";
        }

    }
    
    private function HeatmapGetIdSite($url, $return = false, $plainURL = null) {
    
        $heatData = !$return ? $this->plugin_option : null;
    
        if(empty($heatData) || $return) {
    
            $content = $this->HeatmapCURL($url, [], 'GET');
            if( empty($content) ) {
                return;
            }
    
            $content = sanitize_text_field($content);
            $content = json_decode($content, true);
            
            if(!$return && isset($content['idsite'])) {
                $content['heatURL'] = $plainURL;
                $content['heatLastUpdated'] = time();
                update_option($this->option_name, wp_json_encode($content));
            }
    
            if($return) {
                $content['apiURL'] = $plainURL;
                return $content;
            }
            
            return $content['idsite'] ?? 0;
        }
    
        $content = json_decode($heatData, true);
    
        return $content['idsite'] ?? 0;
    
    }
    
    public function HeatmapHeatagScript($getIdSite = false) {
    
        $apiURL = HEATMAP_APP_URL;
        $apiURL .= "&getId=1&url=" . get_site_url();
    
        $heatURL = $this->HeatmapURL($apiURL, true);
        $heatURL = trim($heatURL, '/') . '/';
    
        if($getIdSite) {
            return $this->HeatmapGetIdSite($apiURL, true, $heatURL);
        }
    
        $idSite = $this->HeatmapGetIdSite($apiURL, false, $heatURL);
        if($idSite > 0) {
        ?>
        <!-- heatmap.com snippet -->
        <script>
        (function() {      
            var _paq = window._paq = window._paq || [];
            var heatUrl = window.heatUrl = '<?= $heatURL; ?>';
            function loadScript(url) {
            var script = document.createElement('script'); script.type = 'text/javascript'; 
            script.src = url; script.async = false; script.defer = true; document.head.appendChild(script);
            }
            loadScript(heatUrl+'preprocessor.min.js?sid=<?= $idSite ?>');
            window.addEventListener('DOMContentLoaded', function (){	 
                if(typeof paq != 'object' || paq.length == 0) {     
                _paq.push(['setTrackerUrl', heatUrl+'sttracker.php']);
                loadScript(heatUrl+'heatmap-light.min.js?sid=<?= $idSite ?>'); 
                } 
            });
        })();
        </script>
        <?php
        }
    }

    public function HeatmapTrackOrder( $order_id ) {

        if ( !$order_id ) {
            return;
        }
    
        $apiData = get_option($this->option_name);
        $apiData = !is_array($apiData) ? json_decode($apiData, true) : $apiData;
    
        if(isset($apiData['idsite'])) {
    
            $order = wc_get_order( $order_id );
    
            // This is the order total
            $revenue = $order->get_total();
            
            $order_data = [
                'idorder'           => $order_id,
                'idsite'            => $apiData['idsite'],
                'status'            => 'completed',
                '_id'               => $_COOKIE['mr_vid'] ?? null,
                'revenue'           => $revenue,
                'quicktransaction'  => 1,
                'device_type'       => $_SERVER['HTTP_USER_AGENT'] ?? null
            ];
    
            $items = [];
        
            // This loops over line items
            foreach ( $order->get_items() as $item ) {
                // This will be a product
                $product = $item->get_data();
    
                $items[] = [
                    // 'sku'   => $item->get_product()->get_sku(),
                    'price' => $product['total'],
                    'title' => $product['name'],
                    'quantity' => $product['quantity']
                ];
            }
    
            $order_data['items'] = $items;
    
            // push the data to the api
            $this->HeatmapCURL($this->HeatmapURL(HEATMAP_APP_URL, true) . "/sttracker.php", json_encode($order_data));
    
        }
    
    }

    public function HeatmapOrderCompleted( $order_id ) {
        return $this->HeatmapChangeOrderStatus($order_id, 'completed');
    }

    public function HeatmapOrderRefunded( $order_id ) {
        return $this->HeatmapChangeOrderStatus($order_id, 'refunded');
    }

    public function HeatmapOrderFailed( $order_id ) {
        return $this->HeatmapChangeOrderStatus($order_id, 'failed');
    }

    public function HeatmapOrderCancelled( $order_id ) {
        return $this->HeatmapChangeOrderStatus($order_id, 'cancelled');
    }

    public function HeatmapChangeOrderStatus($order_id, $status) {

        if ( !$order_id ) {
            return;
        }

        $apiData = get_option($this->option_name);
        $apiData = !is_array($apiData) ? json_decode($apiData, true) : $apiData;

        if(isset($apiData['idsite'])) {

            $order_data = [
                'status'            => $status,
                'idorder'           => $order_id,
                'idsite'            => $apiData['idsite'],
                'hook'              => $status,
                'quicktransaction'  => 1
            ];

            // push the data to the api
            $this->HeatmapCURL($this->HeatmapURL(HEATMAP_APP_URL, true) . "/sttracker.php", json_encode($order_data));

        }

    }
    
}
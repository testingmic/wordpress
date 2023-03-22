<?php
namespace Inc;

use WP_Http_Curl;

use Inc\Base\HeatmapAdmin;

final class HeatmapTracker {

	public function init() {

        add_action('wp_head', [$this, 'heatmapHeatagScript'], 10);
        add_action('woocommerce_checkout_order_processed', [$this, 'heatmapTrackOrder'], 10, 1);

        if(current_user_can('activate_plugins')) {            
            $adminObject = new HeatmapAdmin();
            add_action('admin_menu', [$adminObject, 'heatmapAdminMenu'], 9);
        }

    }

    private function heatmapURL($url, $hostOnly = false) {

        $parse = parse_url($url);
        $url = ($parse['scheme'] ?? "https") . "://" . $parse['host'] . (
            (isset($parse['port']) && !in_array($parse['port'], [80, 443]) ? ":{$parse['port']}" : null)
        ) . ($parse['path'] ?? null);
    
        $url = trim(str_ireplace("www.", "", $url), '/');
    
        return $hostOnly ? $parse['scheme'] . "://" . $parse['host'] : $url;
    
    }
    
    private function heatmapCURL($apiURL, $data = [], $method = 'POST') {
        
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
    
    private function heatmapGetIdSite($url, $return = false, $plainURL = null) {
    
        $heatData = !$return ? get_option('_heatmap_data') : null;
    
        if(empty($heatData) || $return) {
    
            $content = $this->heatmapCURL($url, [], 'GET');
            if( empty($content) ) {
                return;
            }
    
            $content = sanitize_text_field($content);
            $content = json_decode($content, true);
            
            if(!$return && isset($content['idsite'])) {
                $content['heatURL'] = $plainURL;
                $content['heatLastUpdated'] = time();
                update_option('_heatmap_data', wp_json_encode($content));
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
    
    public function heatmapHeatagScript($getIdSite = false) {
    
        $apiURL = HEATMAP_APP_URL;
        $apiURL .= "&getId=1&url=" . get_site_url();
    
        $heatURL = $this->heatmapURL($apiURL, true);
        $heatURL = trim($heatURL, '/') . '/';
    
        if($getIdSite) {
            return $this->heatmapGetIdSite($apiURL, true, $heatURL);
        }
    
        $idSite = $this->heatmapGetIdSite($apiURL, false, $heatURL);
        if($idSite > 0) {
        ?>
        <script>
            var _paq = window._paq = window._paq || [];
            var heatUrl = "<?= $heatURL; ?>";
            var heatENV = {};
            (function() {
                _paq.push(['setTrackerUrl', heatUrl+'sttracker.php']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.async=true; g.src=heatUrl+'heatmap.js?sid=<?= $idSite ?>'; s.parentNode.insertBefore(g,s);
            })();
        </script>
        <?php
        }
    }

    public function heatmapTrackOrder( $order_id ) {

        if ( !$order_id ) {
            return;
        }
    
        $apiData = get_option('_heatmap_data');
        $apiData = !is_array($apiData) ? json_decode($apiData, true) : $apiData;
    
        if(isset($apiData['idsite'])) {
    
            $order = wc_get_order( $order_id );
    
            // This is the order total
            $revenue = $order->get_total();
            
            $order_data = [
                'idorder'   => $order_id,
                'idsite'     => $apiData['idsite'],
                'status'    => 'completed',
                '_id'       => $_COOKIE['mr_vid'] ?? null,
                'revenue'   => $revenue,
                'quicktransaction' => 1,
                'device_type' => $_SERVER['HTTP_USER_AGENT'] ?? null
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
            $this->heatmapCURL($this->heatmapURL(HEATMAP_APP_URL, true) . "/sttracker.php", json_encode($order_data));
    
        }
    
    }
    
}
<?php
/**
 * Plugin Name: Heatmap Tracker
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
if (defined('ABSPATH') && function_exists('add_action')) {

    defined('HEATMAP_APP_URL') or define('HEATMAP_APP_URL', 'http://localhost/index.php?format=json&module=API&method=PaymentIntegration.getIdByURL');

    register_activation_hook(
        __FILE__,
        'heatmapActivateHeatmap'
    );

    register_deactivation_hook(
        __FILE__,
        'heatmapDeActivateHeatmap'
    );

    register_uninstall_hook(
        __FILE__,
        'heatmapDeActivateHeatmap'
    );

    add_action('wp_head', 'heatmapHeatagScript');
    add_action('woocommerce_checkout_order_processed', 'heatmapTrackOrder', 10, 1);

    function heatmapActivateHeatmap() {
        heatmapHeatagScript(true);
    }

    function heatmapDeActivateHeatmap() {
        $option_name = '_heatmap_data';

        delete_option( $option_name );
        
        delete_site_option( $option_name );
    }

    function heatmapURL($url, $hostOnly = false) {

        $parse = parse_url($url);
        $url = ($parse['scheme'] ?? "https") . "://" . $parse['host'] . (
            (isset($parse['port']) && !in_array($parse['port'], [80, 443]) ? ":{$parse['port']}" : null)
        ) . ($parse['path'] ?? null);

        $url = trim(str_ireplace("www.", "", $url), '/');

        return $hostOnly ? $parse['scheme'] . "://" . $parse['host'] : $url;

    }

    function heatmapCURL($apiURL, $data = [], $method = 'POST') {
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
    
    function heatmapGetIdSite($url, $return = false, $plainURL = null) {

        $heatData = !$return ? get_option('_heatmap_data') : null;

        if(empty($heatData) || $return) {

            $content = heatmapCURL($url, [], 'GET');
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

    function heatmapHeatagScript($getIdSite = false) {

        $apiURL = HEATMAP_APP_URL;
        $apiURL .= "&getId=1&url=" . get_site_url();

        $heatURL = heatmapURL($apiURL, true);
        $heatURL = trim($heatURL, '/') . '/';

        if($getIdSite) {
            return heatmapGetIdSite($apiURL, true, $heatURL);
        }

        $idSite = heatmapGetIdSite($apiURL, false, $heatURL);
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

    function heatmapTrackOrder( $order_id ) {

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
                    'sku'   => $item->get_product()->get_sku(),
                    'price' => $product['total'],
                    'title' => $product['name'],
                    'quantity' => $product['quantity']
                ];
            }

            $order_data['items'] = $items;

            // push the data to the api
            heatmapCURL(heatmapURL(HEATMAP_APP_URL, true) . "/sttracker.php", json_encode($order_data));

        }

    }

}
?>
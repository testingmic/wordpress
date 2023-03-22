<?php
/**
 * Trigger this file on Plugin uninstall
 * @package EvelynApi
 */

namespace Inc\Base;

class InventoryManager {
		
	public $userData;
	public $prefix;

	public function __construct() {
		global $wpdb;

		$this->evdb = $wpdb;
		$this->prefix = $this->evdb->prefix;
	}

	final function ordersList($userData) {

		$this->userData = $userData;

		$processedIds = [];

		foreach($this->userData['orders'] as $processedOrder) {
			$processedIds[] = $processedOrder['woocommerce'];
		}

		$stmt = $this->evdb->get_results("
			SELECT id AS order_id, guid, DATE(post_date) AS post_date,  post_title
			FROM {$this->prefix}posts 
			WHERE post_type='shop_order' AND post_status = 'wc-completed'
			ORDER BY id DESC LIMIT 100
		");

		$ordersList = [];

		// loop through the list of orderss
		foreach($stmt as $eachOrder) {
			
			// if the order id is not in the list of processed ids
			if(!in_array($eachOrder->order_id, $processedIds)) {

				// fetch the product details of the order
				$eachOrder->lineItems = $this->lineItems($eachOrder->order_id);
				
				// load the customer details 
				$eachOrder->customerDetails = $this->customerDetails($eachOrder->order_id)[0];

				$ordersList[] = $eachOrder;
			}
		}

		return $ordersList;
	}

	private function lineItems($orderId) {

		$stmt = $this->evdb->get_results("
			SELECT 
				a.order_item_name, b.meta_key, b.meta_value,
				(
					SELECT c.meta_id 
					FROM wp_woocommerce_order_itemmeta c 
					WHERE c.meta_key = '_product_id' AND c.order_item_id = a.order_item_id
				) AS row_meta_id
			FROM 
				{$this->prefix}woocommerce_order_items a
            LEFT JOIN {$this->prefix}woocommerce_order_itemmeta b ON b.order_item_id = a.order_item_id
			WHERE 
				a.order_id = '{$orderId}' AND a.order_item_type='line_item'
		");

		$itemsList = [];
		foreach ($stmt as $key => $value) {
			if($value->meta_key == '_product_id') {
				$itemsList[$value->order_item_name]['_pid'] = $this->correspondingId($value->meta_value);
				$itemsList[$value->order_item_name]['_unique_id'] = $this->eachItem($value->meta_value);
			}
		    $itemsList[$value->order_item_name][$value->meta_key] = $value->meta_value;
		}

		return $itemsList;

	}

	private function eachItem($itemId) {
		return get_post($itemId)->post_name;
	}

	private function correspondingId($itemId) {

		$productIds = $this->userData['inventory']['productIds'];

		foreach($productIds as $eachProduct) {

			$thisPid = explode('_', $eachProduct);

			if($thisPid[0] == $itemId) {
				return $thisPid[1];
				break;
			}
		}
	}

	private function customerDetails($orderId) {

		$stmt = $this->evdb->get_results("
			SELECT 
				a.customer_id,
				b.first_name AS firstname, b.last_name AS lastname,
				b.email, b.state, b.city, b.postcode, b.country,
				(
					SELECT c.meta_value FROM {$this->prefix}usermeta c 
					WHERE c.user_id = a.customer_id AND c.meta_key='billing_phone'
				) AS contact
			FROM {$this->prefix}wc_order_stats a
			LEFT JOIN {$this->prefix}wc_customer_lookup b ON b.customer_id = a.customer_id
			WHERE a.order_id = '{$orderId}'
		");

		return $stmt;		

	}

}
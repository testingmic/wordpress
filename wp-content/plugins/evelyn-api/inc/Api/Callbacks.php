<?php
/**
 * Trigger this file on Plugin uninstall
 * @package EvelynApi
 */

namespace Inc\Api;

use \Inc\Api\Curl;
use \Inc\Base\ImportHandler;
use \Inc\Base\BaseController;
use \Inc\Base\SessionManager;
use \Inc\Base\InventoryManager;

class Callbacks extends BaseController {

	public $sessions;

	public function __construct() {
		parent::__construct();

		session_start();

		add_action('wp_ajax_updateAccountDetails', Callbacks::updateAccountDetails());
		add_action('wp_ajax_loadInventory', Callbacks::loadInventory());
		add_action('wp_ajax_deleteProduct', Callbacks::deleteProduct());
		add_action('wp_ajax_importData', Callbacks::importData());
		add_action('wp_ajax_syncronizeData', Callbacks::syncronizeData());

		if (! wp_next_scheduled ( 'autoSyncOrdersWithEvelyn' )) {
	    	wp_schedule_event(time(), 'hourly', 'autoSyncOrdersWithEvelyn');
	    }
		add_action( 'autoSyncOrdersWithEvelyn', Callbacks::autoSyncOrdersWithEvelyn());
	}

	public function autoSyncOrdersWithEvelyn() {
		// $this->syncronizeData(true);
	}

	public function adminDashboard() {
		require_once $this->plugin_path.'templates/dashboard.php';
	}

	public function inventoryDashboard() {
		require_once $this->plugin_path.'templates/inventory.php';
	}

	public function customersDashboard() {
		require_once $this->plugin_path.'templates/customers.php';
	}

	public function updateAccountDetails() {

		// initializing
		$response = (Object) [
			'status' => 'error',
			'result' => 'Error processing request'
		];

		// convert to object
		$postData = (Object) array_map('sanitize_text_field', $_POST);

		// confirm that the form has been submitted
		if(isset($postData->action) && ($postData->action == "updateAccountDetails")) {
			
			// confirm that all variables has been parsed
			if(isset($postData->company_name, $postData->email, $postData->website, $postData->primary_contact, $postData->secondary_contact, $postData->address)){

				// validate the content parsed
				if(empty($postData->company_name)) {
					$response->result = "Sorry! The Store Name cannot be empty";
				} elseif(!empty($postData->email) && !filter_var($postData->email, FILTER_VALIDATE_EMAIL)) {
					$response->result = "Sorry! Please provide a valid email address";
				} elseif(!empty($postData->primary_contact) && !preg_match('/^[+0-9]+$/', $postData->primary_contact)) {
					$response->result = "Sorry! Please provide a valid contact number";
				} elseif(!empty($postData->secondary_contact) && !preg_match('/^[+0-9]+$/', $postData->secondary_contact)) {
					$response->result = "Sorry! Please provide a valid contact number";
				} else {

					// convert to object and set the new values
					$uData = $this->user_data;
					$uData['message']['address'] = $postData->address;
					$uData['message']['client_email'] = $postData->email;
					$uData['message']['client_name'] = $postData->company_name;
					$uData['message']['client_website'] = $postData->website;
					$uData['message']['primary_contact'] = $postData->primary_contact;
					$uData['message']['secondary_contact'] = $postData->secondary_contact;
					// $uData['limit'] = 10;

					// update the database information
					update_option('evelynpos_api', $uData);

					$payload = [
						'updateCompanyDetail' => true,
						'company_name' => $postData->company_name,
						'email' => $postData->email,
						'primary_contact' => $postData->primary_contact,
						'secondary_contact' => $postData->secondary_contact,
						'website' => $postData->website,
						'address' => $postData->address
					];

					// sync the data
					$updateData = Curl::curlHandler(
						$payload, 'branchManagment/settingsManager/updateCompanyDetail', 
						'POST', $this->user_data
					);

					// send the information to the api for processing
					$response->status = "success";
					$response->result = "Account details was successfully updated";

					// if the curl response was successful
					if($updateData['status'] == 200) {
						$response->result = "Account details was successfully synchronized.";						
					}
					// parse the response message
					print json_encode($response);
					
				}
				wp_die();
			}

		}

		// validate api keys
		elseif(isset($postData->action) && ($postData->action == "validateApiKeys")) {
			
			// confirm that all variables has been parsed
			if(isset($postData->username, $postData->api_key)){

				// set the payload data
				$payload = [
					'api_uname' => $postData->username,
					'api_key' => $postData->api_key
				];

				// submit the data for processing
				$verify = $this->verifyKey($payload);

				// response message
				if($verify) {
					$response->status = "success";
					$response->result = "Api Keys successfully verified.";
				}

				print json_encode($response);
				
			}
			wp_die();
		}

	}

	public function loadInventory() {

		// initializing
		$response = (Object) [
			'status' => 'error',
			'result' => 'Error processing request'
		];

		// convert to object
		$postData = (Object) array_map('sanitize_text_field', $_POST);

		// confirm that the form has been submitted
		if(isset($postData->action) && ($postData->action == "loadInventory") && ($postData->forceImport)) {
			
			// process the data parsed
			$startIndex = isset($postData->startIndex) ? (int) $postData->startIndex : 0;
			$endIndex = isset($postData->endIndex) ? (int) $postData->endIndex : $this->user_data['limit'];

			// set the payload
			$payload = [
				'request' => true,
				'limit' => "{$startIndex},".($endIndex)
			];

			$queryString = "inventoryManagement/getAllProducts?request=true&limit={$startIndex},{$endIndex}";

			// delete the session if a force import was parsed
			if($postData->forceImport == 'yes') {
				SessionManager::deleteSession('loadInventory', $queryString);
			}

			// if the session already exists
			if(SessionManager::checkSession('loadInventory', $queryString)) {

				$response->result = SessionManager::manageSessions(
					'loadInventory', $queryString, $loadInventory['result']
				);
				$response->status = true;

			} else {
				// make a curl call
				$loadInventory = Curl::curlHandler(
					$payload, 'inventoryManagement/getAllProducts', 
					'GET', $this->user_data
				);

				// if no query beyond that limit range
				if($loadInventory['result'] == "Limit exceeded") {
					$response->result = 'Limit Exceeded';
					$response->status = true;
				}

				// confirm that a valid response was parsed
				if($loadInventory['status'] == true) {
					
					// set the response data
					$response->result = $loadInventory['result'];
					$response->status = true;
					
					// add the data to the session
					SessionManager::addSessions('loadInventory', $queryString, $loadInventory['result']);
				}
			}

			// navigation mechanism
			if($startIndex == 0) {
				$prev = '';

				// show the next button if the limit has not been exceeded
				if($response->result != 'Limit Exceeded') {

					// show button if the query is at least $this->user_data['limit'] 
					$next = "<a href=\"admin.php?page=evelynpos_inventory&start=".($endIndex)."&end=".($this->user_data['limit'])."\">View More</a>";
				}
			} else {
				$prev = "<a href=\"admin.php?page=evelynpos_inventory&start=".($startIndex-$this->user_data['limit'])."&end=".($this->user_data['limit'])."\">Previous</a>";
				
				// show the next button if the limit has not been exceeded
				if($response->result != 'Limit Exceeded') {
					$next = "<a href=\"admin.php?page=evelynpos_inventory&start=".($startIndex+$this->user_data['limit'])."&end=".($this->user_data['limit'])."\">View More</a>";
				}
			}

			// confirm that each product has already been uploaded
			if($response->status) {
				$products = [];

				// loop through the list of items
				foreach($response->result as $e) {

					// confirm if the product has been imported
					if(in_array($e['product_id']."_".$e['branchId'], $this->user_data['inventory']['uniqueIds'])) {
						$e['imported'] = true;
					} else {
						$e['imported'] = false;
					}
					$products[] = $e;
				}

				$response->result = $products;
			}

			$response->navLink = '<tr>
				<td colspan="5"></td>
				<td class="text-right">'.$prev.'</td>
				<td class="text-right">'.$next.'</a></td>
			</tr>';

			// print out the results
			print json_encode($response);
			wp_die();

		}
	}

	public function importData() {

		// initializing
		$response = (Object) [
			'status' => 'error',
			'result' => []
		];

		// convert to object
		$postData = (Object) array_map('sanitize_text_field', $_POST);

		// confirm that the form has been submitted
		// this function will handle the import of multiple dataset 
		// from EvelynPOS into the store
		if(isset($postData->action) && ($postData->action == "importData")) {

			// set the payload 
			$payload = [
				'request' => true,
				'limit' => "0,1000"
			];

			// make a curl call for the inventory list
			$loadInventory = Curl::curlHandler(
				$payload, 'inventoryManagement/getAllProducts', 
				'GET', $this->user_data
			);

			// // push it to the import handler for processing
			$importClass = new ImportHandler($this->woocommerce_state, $this->user_data, $this->userId);

			// // confirm that inventory query returned true
			if($loadInventory['status'] == true) {
				
				$response->result = 'This is the inventory records';

				$response->result = $importClass->importInventory($loadInventory['result']);

			}

			$response->status = 'success';

			print json_encode($response);
			wp_die();

		}

	}

	public function syncronizeData($autoData = null) {

		// initializing
		$response = (Object) [
			'status' => 'error',
			'result' => []
		];

		// convert to object
		if(!empty($autoData)) {
			$continue = true;
		} else {
			// continue the process
			$postData = (Object) array_map('sanitize_text_field', $_POST);

			// confirm that the form has been submitted
			// this function will handle the syncronize of multiple dataset 
			if(isset($postData->action) && ($postData->action == "syncronizeData")) {
				$continue = true;
			}
		}
		
		// from EvelynPOS into the store
		if(!empty($continue)) {

			// set the payload 
			$payload = [
				'request' => true,
				'limit' => "0,1000"
			];

			// create a new object
			$inventoryClass = new InventoryManager();

			// make a curl call for the inventory list
			$ordersList = $inventoryClass->ordersList($this->user_data);

			$payload = [
				'ordersList' => $ordersList,
				'shopName' => get_option('blogname'),
				'shopUrl' => get_option('siteurl')
			];

			// load the user account information for any additional updates
			$accountInfo = Curl::curlHandler(
				['getData' => true], 'apiAccountInformation', 
				'POST', $this->user_data
			);

			// if the information request was successful
			if(!empty($accountInfo['result']['clientId'])) {

				// assign the new data to update
				$uData = $this->user_data;
				$uData['message'] = $accountInfo['result'];

				// update the information on in wordpress
				$response->result = $uData;
				update_option('evelynpos_api', $uData);
			}

			// confirm that inventory query returned true
			if(!empty($ordersList)) {
				
				// sync the data
				$result = Curl::curlHandler(
					$payload, 'apiOrdersHandler/syncronizeInventory', 
					'POST', $this->user_data
				);

				// if the query returned successful
				if($result['status'] == 'success') {
					$response->status = "success";
					
					foreach($result['result'] as $eachOrder) {
						array_push($this->user_data['orders'], $eachOrder);
					}
					$this->user_data['orders_last_sync'] = date("Y-m-d H:iA");

					$response->result = "Data was successfully syncronized!";
					
					// update the client data
					update_option('evelynpos_api', $this->user_data);
				}
			} else {
				$response->status = "success";
				$response->result = "Data was successfully syncronized!";
			}

			//print the response
			if(empty($autoData)) {
				print json_encode($response);
				wp_die();
			}
		}

	}

	public function deleteProduct() {

	}
	
}
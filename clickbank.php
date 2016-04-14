<?php
	/*
	* developer	: ariful alam
	* email 	: ariful-alam@hotmail.com
	* website 	: http://syncmachine.com
	* ClickBank API DOC: https://support.clickbank.com/entries/22821303-ClickBank-API
	*/

	class ClickBank{
		function __construct($clickbank_api){
			$this->base_url 	= "https://api.clickbank.com/rest/1.3/";
			foreach ($clickbank_api as $key => $value) {
				$this->$key = $value;
			}
		}

		function cURL($access_url, $page = 1, $method = 'GET', $params = array()){
			$page = "Page: $page";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->base_url . $access_url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			if($method !== 'GET'){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			}
			#curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/{$this->return}", "Authorization: {$this->dev_key}:{$this->api_key}", $page));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: {$this->dev_key}:{$this->api_key}", $page));

			$result = curl_exec($ch);

			$curl = curl_getinfo($ch);
			curl_close($ch);

			$data = json_decode($result, true);
			return array($curl, $data);
		}

		#########################################################
		####################### ORDER API #######################
		#########################################################

		/*
		*	Get Orders
		*/
		function getOrderByEmail($email){
			$access_url = "orders/list/?email=$email";
			list($curl, $order) = $this->getOrders($access_url);
			return $order;
		}

		function getOrderByReceipt($receipt, $sku = ''){
			$sku = (!empty($sku)) ? "/?sku=$sku" : '';
			$access_url = "orders/{$receipt}{$sku}";
			list($curl, $order) = $this->getOrders($access_url);
			return $order;
		}

		function getOrderByItemID($itemID){
			$access_url = "orders/list/?item=$itemID";
			list($curl, $order) = $this->getOrders($access_url);
			return $order;
		}

		function getOrderByDate($startDate, $endIDate = ''){
			$endDate = (empty($endDate)) ? date('Y-m-d') : $endDate;
			$access_url = "orders/list/?startDate=$startDate&endDate=$endDate";
			list($curl, $order) = $this->getOrders($access_url);
			return $order;
		}

		function getOrderByParam($params = array()){
			$access_url = "orders/list/?";
			if(is_array($params)){
				if(isset($params['startDate']) && (!isset($params['endDate']) || empty($params['endDate']))){
					$params['endDate'] = date('Y-m-d');
				}
				if(isset($params['endDate']) && (!isset($params['startDate']) || empty($params['startDate']))){
					unset($params['endDate']);
				}
				if(!empty($params)){
					foreach ($params as $key => $value) {
						$access_url .= "{$key}={$value}&";
					}
					$access_url = trim($access_url, "&");
				}
			}
			list($curl, $order) = $this->getOrders($access_url);
			return $order;
		}

		/*
		*	Order Count
		*/
		function getOrderCountByEmail($email){
			$access_url = "orders/count/?email=$email";
			list($curl, $count) = $this->cURL($access_url, 1);
			return $count;
		}

		function getOrderCountByDate($startDate = '', $endDate = ''){
			$endDate = (empty($startDate)) ? '' : ((empty($endDate)) ? date('Y-m-d') : $endDate);

			if(empty($startDate)) $access_url = "orders/count/";
			else $access_url = "orders/count/?startDate=$startDate&endDate=$endDate";
			
			list($curl, $count) = $this->cURL($access_url, 1);
			return $count;
		}

		function getOrderCountByParam($params = array()){
			$access_url = "orders/count/?";
			if(is_array($params)){
				if(isset($params['startDate']) && (!isset($params['endDate']) || empty($params['endDate']))){
					$params['endDate'] = date('Y-m-d');
				}
				if(isset($params['endDate']) && (!isset($params['startDate']) || empty($params['startDate']))){
					unset($params['endDate']);
				}
				if(!empty($params)){
					foreach ($params as $key => $value) {
						$access_url .= "{$key}={$value}&";
					}
					$access_url = trim($access_url, "&");
				}
			}
			list($curl, $count) = $this->cURL($access_url, 1);
			return $count;
		}

		/*
		*	GET ORDER IS ACTIVE OR NOT
		*/
		function getOrderStatus($receipt, $sku = ''){
			$sku = (!empty($sku)) ? "/?sku=$sku" : '';
			$access_url = "orders/{$receipt}{$sku}";
			list($curl, $result) = $this->cURL($access_url, 1, 'HEAD');

			if((int)$curl['http_code'] === 403){
				//$message['code'] 	= 403;
				//$message['message'] = $this->get_http_codes_msg((int)$curl['http_code']);
				$message['active'] 	= 0;//"Product is not active";
			}else if((int)$curl['http_code'] === 204){
				//$message['code'] 	= 204;
				//$message['message'] = $this->get_http_codes_msg((int)$curl['http_code']);
				$message['active'] 	= 1;//"Product is active";
			}else{
				$_err = $this->get_http_codes_msg((int)$curl['http_code']);
				if(!empty($_err)) {
					$message['code'] 	= $curl['http_code'];
					$message['message'] = $this->get_http_codes_msg((int)$curl['http_code']);
					$message['active'] 	= 0;	
				}else{
					$message['message'] = 'Nothing found.';
					$message['active']  = 0;
				}
			}
			return $message;
		}

		/*
		*	GET UPSELL TRANSACTION (if any)
		*/
		function getUpsellByReceipt($receipt){
			$access_url = "orders/{$receipt}/upsells";
			list($curl, $order) = $this->getOrders($access_url);
			
			if((int)$curl['http_code'] === 403){
				$message ['code'] 		= 403;
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				$message ['message'] 	= "Transaction does not exist, or there are no upsells for this transaction";
				return $message;
			}else if((int)$curl['http_code'] !== 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				return $message;
			}
			return $order;
		}

		/*
		*	restart a cancelled subscription sending receipt
		*/
		function reinstateSubscription($receipt){
			$access_url = "orders/{$receipt}/reinstate";
			list($curl, $order) = $this->cURL($access_url, 1, 'POST');
			if((int)$curl['http_code'] !== 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				//$message ['message'] 	= "";
				return $message;
			}
			return $order;
		}

		/*
		*	Pause billing till a future date
		*/
		function pauseSubscription($receipt, $restartDate){
			$access_url = "orders/{$receipt}/pause/?restartDate=$restartDate";
			list($curl, $order) = $this->cURL($access_url, 1, 'POST');
			if((int)$curl['http_code'] !== 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				//$message ['message'] 	= "";
				return $message;
			}
			return $order;
		}

		/*
		*	Extends billing period
		*/
		function extendSubscription($receipt, $numPeriods){
			$access_url = "orders/{$receipt}/extend/?numPeriods=$numPeriods";
			list($curl, $order) = $this->cURL($access_url, 1, 'POST');
			if((int)$curl['http_code'] !== 204){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				//$message ['message'] 	= "";
				return $message;
			}else if((int)$curl['http_code'] === 204){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				$message ['message'] 	= "Subscription extended to $numPeriods";
				return $message;
			}
		}

		/*
		*	Change a product to another
		*/
		function changeProduct($receipt, $oldSku, $newSku, $carryAffiliate = ''){
			$access_url = "orders/{$receipt}/changeProduct/?oldSku=$oldSku&newSku=$newSku";
			$access_url .= (!empty($carryAffiliate)) ? "&carryAffiliate=$carryAffiliate" : "";
			list($curl, $result) = $this->cURL($access_url, 1, 'POST');

			//print_r("RESULT: ".$result);
			
			if((int)$curl['http_code'] !== 204){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				return $message;
			}else if((int)$curl['http_code'] === 204){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				$message ['message'] 	= "Subscription changed to $newSku from $oldSku";
				return $message;
			}
		}

		/*
		*	Change shipping address for physical recurring subscription
		*/
		function changeAddress($receipt, $address){
			$access_url = "orders/{$receipt}/changeAddress/?";

			foreach ($address as $key => $value) {
				$access_url .= "{$key}={$value}&";
			}
			$access_url = rtrim($access_url, '&');
			list($curl, $result) = $this->cURL($access_url, 1, 'POST');

			if((int)$curl['http_code'] !== 204){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				return $message;
			}else if((int)$curl['http_code'] === 204){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				$message ['message'] 	= "Address changed";
				return $message;
			}
		}

		#############################################################
		####################### ANALYTICS API #######################
		#############################################################

		/*
		*	GET  /1.3/analytics/status
		*/
		function analyticsStatus(){
			$access_url = "analytics/status";
			list($curl, $result) = $this->cURL($access_url);

			/*if((int)$curl['http_code'] !== 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				return $message;
			}else if((int)$curl['http_code'] === 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				$message ['result'] 	= $result;
				return $message;
			}*/
			print_r($curl);
			echo "<br/>";
			return $result;
		}

		/*
		*	GET  /1.3/analytics/{role}/subscription/details/compthirty
		*	Returns a list of subscriptions completing in the next 30 days.
		*/
		function getCompNext30(){
			$access_url = "analytics/status";
			list($curl, $result) = $this->cURL($access_url);

			/*if((int)$curl['http_code'] !== 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				return $message;
			}else if((int)$curl['http_code'] === 200){
				$message ['code'] 		= (int)$curl['http_code'];
				$message ['code_msg'] 	= $this->get_http_codes_msg((int)$curl['http_code']);
				$message ['result'] 	= $result;
				return $message;
			}*/
			print_r($curl);
			echo "<br/>";
			return $result;
		}

		function getOrders($access_url){
			$page 		= 1;
			$curl 		= array();
			$data 		= array();
			$orders 	= array();
			$access_url = $access_url;

			while(empty($curl) || (int)$curl['http_code'] === 206){
				(!empty($curl)) ? $page++ : ''; //used for if $curl['http_code'] === 206 is true
				unset($data);
				list($curl, $data) = $this->cURL($access_url, $page);

				if(!empty($data)){
					if($this->isAssoc($data['orderData'])){
						foreach ($data['orderData'] as $key => $value) {
							#if(strtolower($value['site']) === $this->account)
								array_push ($orders, $value);
						}
					}else{
						#if(strtolower($data['orderData']['site']) === $this->account)
							array_push($orders, $data['orderData']);
					}
				}
			}
			return array($curl, $orders);
		}


		#########################################################
		####################### PRODUCT API #####################
		#########################################################

		function createProduct($param = array()){
			# have to add code, check @ https://api.clickbank.com/rest/1.3/products
		}

		function getProducts($params = array('sku' => 'list', 'type' => '')){ // type = STANDARD | RECURRING
			if($params['sku'] === 'list'){
				$url = "products/list/?site={$this->account}";
				$url .= (!empty($params['type'])) ? "&type={$params['type']}" : '';
			}else{
				$url = "products/{$params['sku']}/?site={$this->account}";
			}
			
			list($curl, $result) = $this->cURL($url);
			
			if((int)$curl['http_code'] === 200){
				return $result;
			}else{
				$json['code'] = $curl['http_code'];
				$json['code_msg'] = $this->get_http_codes_msg($json['code']);
				return $json;
			}
			
		}

		function deleteProducts($sku){ 
			$url = "products/{$sku}/?site={$this->account}";
			
			list($curl, $result) = $this->cURL($url, 1, "DELETE");
			
			if((int)$curl['http_code'] === 200){
				return $result;
			}else{
				$json['code'] = $curl['http_code'];
				$json['code_msg'] = $this->get_http_codes_msg($json['code']);
				return $json;
			}
			
		}

		#######################################################
		####################### IMAGE API #####################
		#######################################################

		//Have to test
		function getImages($type = '', $approvedOnly = FALSE){ //$type = PRODUCT | BANNER | BANNER_BG
			$url = "images/list/?site={$this->account}&approvedOnly=$approvedOnly";
			if(!empty($type)){
				if(in_array($type, array('PRODUCT', 'BANNER', 'BANNER_BG'))){
					$url .= "&type={$type}";
					list($curl, $result) = $this->cURL($url);

					print_r($curl);
					return $result;
				}else{
					$json['status'] 	= 0;
					$json['message'] 	= "Wrong type passed. You have to pass either PRODUCT, BANNER or BANNER_BG";
					return $json;
				}				
			}
		}

		//function to check array key is numaric or not
		function isAssoc($arr){
			$array_key = array_keys($arr);
			if(is_numeric($array_key[0])){
				return true;
			}else{
				return false;
			}
		}

		function get_http_codes_msg($code){
			$http_codes = array(200 => 'OK',
                        		201 => 'Created',
                        		202 => 'Accepted',
		                        203 => 'Partial Information',
		                        204 => 'No Response',
		                        301 => 'Access URL Moved',
		                        302 => 'Found',
		                        303 => 'Method',
		                        304 => 'Not Modified',
		                        400 => 'Bad Request (Client error)',
		                        401 => 'Unauthorized (Invalid authentication)',
		                        402 => 'Payment Required (Premium video content)',
		                        403 => 'Forbidden',
		                        404 => 'Not Found',
		                        405 => 'Method not allowed',
		                        406 => 'Not Acceptable',
		                        500 => 'Internal Server Error or Unavailable',
		                        501 => 'Not implemented',
		                        502 => 'Service temporarily overloaded',
		                        503 => 'Gateway timeout',
		                        504 => 'Internal Server Error or Unavailable',
		                        505 => 'Internal Server Error or Unavailable'
						);
			return $http_codes[$code];
		}
	}

<?php
class jvzoo {
	public $key;
	public $log;

	public function __construct(){
		//$this->key = '2A097B79';
		$this->key = 'nLIvRUKWBcpDIUw7yBNO';
		$this->log = new KLogger(APPPATH."logs/custom", KLogger::INFO);
	}
	function Jvzoo(){
		//$this->key = 'enter_key';
		//$this->log = new KLogger(APPPATH."logs/custom", KLogger::INFO);
	}
	function initialize($post, $log) {
		//$log = new KLogger(APPPATH."logs/custom", KLogger::INFO);
		
		$key = $this->key;

		$jvzooproducts = array();
		$post_vars = $this->extract_cb_postvars($post);

		if ($this->ipn_verified($key, $post)) {
					//$this->log->logInfo(__FUNCTION__ .' tentro');
			// Is this necessary??
			if ($this->is_v2($post_vars)) {
				$response['lastname'] = $post_vars['ccustlastname'];
				$response['firstname'] = $post_vars['ccustfirstname'];
			}


			// the passed sku...
			$passedparams = parse_str($post_vars['cvendthru']);

			// check if product ID (cproditem) is in cbproducts
			// if so, return the level for that product ID
			// if not, use $passedparams['sku']
			$response['product_id'] = $post_vars['cproditem'];


			$response['username'] = $post_vars['ccustemail'];
			$response['email'] = $post_vars['ccustemail'];
			$response['sctxnid'] = $post_vars['ctransreceipt'];
			$response['ctransaction'] = $post_vars['ctransaction'];

			// switch ($post_vars['ctransaction']) {
			// 	case 'SALE':
			// 	case 'TEST_SALE':
			// 		// we only save upsell info on sale in INS
			// 		$receipt = empty($post_vars['cupsellreceipt']) ? $post_vars['ctransreceipt'] : $post_vars['cupsellreceipt'];

			// 		break;
			// 	case 'BILL': // we do nothing because registration is handled by the regular thank you url...
			// 	case 'UNCANCEL-REBILL':
			// 		$txn = $post['sctxnid'];
			// 		$items = explode("-", $txn);
			// 		$response['sctxnid'] = $items[0]; 
			// 		break;

			// 	case 'RFND':
			// 	case 'CGBK':
			// 	case 'INSF':
			// 	case 'CANCEL-REBILL':
			// 	case 'CANCEL-TEST-REBILL':
			// 		$that->ShoppingCartDeactivate();
			// 		break;
			// }
		} else {
			$this->log->logInfo(__FUNCTION__ .'Wrong Attempt to validate Jvzoo APN');
			//throw new Exception("Error Validating Key", 1);
			return false;
			
		}
		return $response;

	}

	function extract_cb_postvars($post) {

		$fields_v4 = array(
			'cprodtitle', 'ctranspaymentmethod', 'cfuturepayments', 'ccustzip', 'ccustshippingzip', 'ccustemail', 'crebillfrequency', 'crebillstatus', 'ctransaffiliate', 'cupsellreceipt', 'corderamount', 'ccustcounty', 'ccurrency', 'ccustfirstname', 'crebillamnt', 'ctransaction', 'ccuststate', 'corderlanguage', 'caccountamount', 'ctid', 'ccustshippingcountry', 'cnextpaymentdate', 'cverify', 'cprocessedpayments', 'cnoticeversion', 'cprodtype', 'ccustcc', 'ccustshippingstate', 'ctransreceipt', 'ccustfullname', 'cbf', 'cbfid', 'cshippingamount', 'cvendthru', 'ctransvendor', 'ctransrole', 'ctaxamount', 'cbfpath', 'ccustaddr2', 'ccustaddr1', 'ccustcity', 'ccustlastname', 'ctranstime', 'cproditem'
		);
		$fields_v2 = array(
			'ccustfullname', 'ccustfirstname', 'ccustlastname', 'ccuststate', 'ccustzip', 'ccustcc', 'ccustaddr1', 'ccustaddr2', 'ccustcity', 'ccustcounty', 'ccustshippingstate', 'ccustshippingzip', 'ccustshippingcountry', 'ccustemail', 'cproditem', 'cprodtitle', 'cprodtype', 'ctransaction', 'ctransaffiliate', 'caccountamount', 'corderamount', 'ctranspaymentmethod', 'ccurrency', 'ctranspublisher', 'ctransreceipt', 'ctransrole', 'cupsellreceipt', 'crebillamnt', 'cprocessedpayments', 'cfuturepayments', 'cnextpaymentdate', 'crebillstatus', 'ctid', 'cvendthru', 'cverify', 'ctranstime'
		);
		sort($fields_v2);
		sort($fields_v4);

		$fields_v1 = array(
			'ccustname', 'ccustemail', 'ccustcc', 'ccuststate', 'ctransreceipt', 'cproditem', 'ctransaction', 'ctransaffiliate', 'ctranspublisher', 'cprodtype', 'cprodtitle', 'ctranspaymentmethod', 'ctransamount', 'caffitid', 'cvendthru', 'cverify'
		);
		//support physical medias
		if (strpos($cprodtype, "PHYSICAL") !== false) {
			array_push($fields_v1, 'ccustaddr1', 'ccustaddrd', 'ccustcity', 'ccustcounty', 'ccustzip');
		}
		$version_fields = array(
			1 => $fields_v1,
			2 => $fields_v2,
			4 => $fields_v4,
		);

		$f = $this->get_fields_for_version($version_fields, $post);


		$jvzoo_req = array();
		foreach ($f as $k) {
			#ignore missing fields
			if (isset($post[$k])) {
				$jvzoo_req[$k] = $post[$k];
			}
		}

		return $jvzoo_req;
	}

	function ipn_verified($secret_key, $post_vars) {
	    $secretKey = $secret_key;
	    $pop = "";
	    $ipnFields = array();
	    foreach ($post_vars AS $key => $value) {
	        if ($key == "cverify") {
	            continue;
	        }
	        $ipnFields[] = $key;
	    }
	    sort($ipnFields);
	    foreach ($ipnFields as $field) {
	        $pop = $pop . $post_vars[$field] . "|";
	    }
	    $pop = $pop . $secretKey;
	    if ('UTF-8' != mb_detect_encoding($pop))
	    {
	        $pop = mb_convert_encoding($pop, "UTF-8");
	    }
	    $calcedVerify = sha1($pop);
	    $calcedVerify = strtoupper(substr($calcedVerify,0,8));
	    $this->log->logInfo(__FUNCTION__ .$calcedVerify ." ".$post_vars["cverify"]);
	    return $calcedVerify == $post_vars["cverify"];		
	}


	function is_v2($post_vars = array()) {
		return isset($post_vars['ccustfullname']);
	}

	function get_fields_for_version($fields, $post) {
		if ($post['cnoticeversion'] == '4.0') {
			return $fields[4];
		}

		if (isset($post['ccustfullname'])) {
			return $fields[2];
		}
		return $fields[1];
	}
}


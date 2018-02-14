<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ebay {
	public $settings;

	public function __construct($settings) {
		$this->settings = $settings;
	}

	public function search($settings){

		$results = $this->request($settings);
		$output = $this->normalize($results);
		return $output;
	}

    public function request($settings)
    {   
		$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
		$version = '1.0.0';  // API version supported by your application
		$query = $settings['keyword'];  // You may want to supply your own query
		$safequery = urlencode($query);  // Make the query URL-friendly
		$appid = 'CesarQui-0dcb-4b69-8756-99284105681c';
	    $globalid = $this->settings['country'];
		$campaignid = $this->settings['campaign_id'];

	        if($settings['page']){
	           $page = $settings['page'];
	        } else {
	            $page = 1;
	        }

		// Create a PHP array of the item filters you want to use in your request
		$filterarray = array();
		// Build the indexed item filter URL snippet

		$no_of_product = 10;
		// Construct the findItemsByKeywords HTTP GET call 
		$apicall = "$endpoint?";
		$apicall .= "OPERATION-NAME=findItemsByKeywords";
		$apicall .= "&SERVICE-VERSION=$version";
		$apicall .= "&SECURITY-APPNAME=$appid";
		$apicall .= "&GLOBAL-ID=$globalid";
		$apicall .= "&keywords=$safequery";
		$apicall .= "&paginationInput.entriesPerPage=$no_of_product";
        $apicall .= "&paginationInput.pageNumber=$page";
        $apicall .= "&affiliate.trackingId=$campaignid";  // => eBay Partner Network Campaign ID
        $apicall .= "&affiliate.networkId=9"; // => eBay Partner Network	        
		
		// Load the call and capture the document returned by eBay API
		$resp = simplexml_load_file($apicall); 
		//ddd($resp);        
        //var_dump($resp);die('respuesta');
        return $resp;
 
    }

	public function normalize($results){	

		switch ($this->settings['country']) {
			case 'EBAY-US':
				$currency = '$'; 
				break;
			case 'EBAY-GB':
				$currency = '£';  
				break;	
			case 'EBAY-ENCA':
				$currency = '$';  
				break;	
			case 'EBAY-FRCA':
				$currency = '$';  
				break;
			case 'EBAY-AU':
				$currency = '$';  
				break;															
			default:
				$currency = '€';
				break;
		}		
		$object = new stdClass();
		$settings = $this->settings;
		if($results->ack == 'Failure'){
			d('fallo');
			return null;
		}		
		$item_list = $results->searchResult->item;
		$total_pages = (int) $results->paginationOutput->totalPages;
		if($total_pages >= 10){
			$total_pages = 10;
		}
		$_items = array();
		//var_dump($item_list);
		foreach ($item_list  as  $item) {
			$object = new stdClass();
			$_plus = (string) $item->title.". ".$currency.(string) $item->sellingStatus->currentPrice;
			$title = strlen((string) $item->title);
			$plus = strlen($_plus);
			if($title > 80) {
				$description = substr((string) $item->title, 0, 80);
				$description = $description.". ".$currency.(string) $item->sellingStatus->currentPrice;
			} else {
				$description = $_plus;
			}

			$object->description = $description;
			unset($description);
			$object->url = (string) $item->viewItemURL;
			// if(empty($item->galleryPlusPictureURL)){
			// 	continue;
			// }
			$object->media_url = (string) $item->galleryPlusPictureURL;
			//d($object->media_url, 'j');
			$object->media_url = empty($item->galleryPlusPictureURL)?(string) $item->galleryURL :(string) $item->galleryPlusPictureURL;
			if(empty($object->media_url)){
				continue;
			}
			$object->source_id = (string) $item->itemId;
			$object->source = 'ebay';
			$object->total_pages =  $total_pages;
			$_items[] = $object;
		}
		return $_items;
	}
	protected function id_filter($id, $dbposts){
		foreach($dbposts as $_dbposts){
			$args = $_dbposts->to_array();	
			if($id == $args['source_id']){
				return false;
			}
		}
		return true;
	}	

    public function ebay_filter($keyword, $ebay, $dbpost){ 
        $product = [];
        $results = $ebay->search(array('keyword' => $keyword));  
        $hook = 1;
        $page=1;
        $nextPage= false;

        while ($hook == 1) {
            if($nextPage == true){
                $page++;
                $results = $ebay->search(array('keyword' => $keyword, 'page' => $page));  
                $nextPage == false;             
            }

            foreach ($results as $item) {
                $item_id = $item->source_id;     
                if($this->id_filter($item_id, $dbpost)){
                    $product = $item;
                    $hook= 0;               
                    break;
                } else {
                    $nextPage =true;
                }
            }   
            if(empty($product)){
                $nextPage =true;
            }
            //avoid endeless loop
            if($page > 15){
                //$this->log->logInfo('Amazon Search Failed');
                break;
            }
        }

        return $product;
    }     
}
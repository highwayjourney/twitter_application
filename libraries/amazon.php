<?php
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Search;

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Amazon {
	public $conf;
	public $settings;

	public function __construct($settings) {
		$this->settings = $settings;
		$this->conf = new GenericConfiguration();	
		$client = new \GuzzleHttp\Client();
		$request = new \ApaiIO\Request\GuzzleRequest($client);		
		try {
			$this->conf
		    ->setCountry($settings['country'])
		    ->setAccessKey($settings['public_key'])
		    ->setSecretKey($settings['private_key'])
			->setAssociateTag($settings['associate_tag'])
		   	->setRequest($request);
		} catch (\Exception $e) {
             ddd($e->getMessage());
        }
	}
	public function search($settings){
        $apaiIO = new ApaiIO($this->conf);
        $search = new Search();
		$search->setCategory('All');
		if(!empty($settings['page'])){
			$search->setPage($settings['page']);
		}
        $search->setKeywords($settings['keyword']);
        $search->setResponseGroup(array('Large'));
        $response_final = simplexml_load_string($apaiIO->runOperation($search));
        return $this->normalize($response_final);
	}

	public function normalize($response){	
		$object = new stdClass();
		$settings = $this->settings;
		switch ($settings['country']) {
			case 'com':
				$currency = '$'; 
				break;
			case 'co.uk':
				$currency = '£';  
				break;	
			case 'cn':
				$currency = '$';  
				break;							
			default:
				$currency = '€';
				break;
		}
		$total_pages = (int) $response->Items->TotalPages;
		if($total_pages >= 15){
			$total_pages = 15;
		}
		$_items = array();

		foreach ($response->Items->Item as  $item) {
			//d($item);
			$object = new stdClass();
			$title_lenght = strlen($item->ItemAttributes->Title);
			$feature_lenght = strlen($item->ItemAttributes->Feature[1]);
			$price_lenght = strlen((string) $item->OfferSummary->LowestNewPrice->FormattedPrice);
			$plus = $title_lenght + $feature_lenght + $price_lenght;
			if($plus < 80){
				$description = $item->ItemAttributes->Title.". ".$item->ItemAttributes->Feature[1];
			} elseif($title_lenght > 80) {
				$description = substr($item->ItemAttributes->Title, 0, 80);
			} else {
				$description = $item->ItemAttributes->Title;
			}
			$description.= ". ".(string) $item->OfferSummary->LowestNewPrice->FormattedPrice;
			$object->description = $description;
			unset($description);
			$object->url = (string) $item->DetailPageURL;
			//var_dump($item); die();
			$object->media_url = !empty($item->LargeImage->URL)?(string) $item->LargeImage->URL :(string) $item->MediumImage->URL;
			if(empty($object->media_url)){
				continue;
			}			
			$object->source_id = (string) $item->ASIN;
			$object->source = 'amazon';
			$object->total_pages =  $total_pages;
			//$object->price = (string) $item->OfferSummary->LowestNewPrice->FormattedPrice;
			//$object->price.= $currency;
			$_items[] = $object;
		}
		//$_items['next'] = (string) $response->Items->MoreSearchResultsUrl;
		//$_items['total_pages'] = $total_pages;
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

    public function amazon_filter($keyword, $amazon, $dbpost){ 
        $product = [];
        $results = $amazon->search(array('keyword' => $keyword));  
        $hook = 1;
        $page=1;
        $nextPage= false;

        while ($hook == 1) {
            if($nextPage == true){
                $page++;
                $results = $amazon->search(array('keyword' => $keyword, 'page' => $page));  
                $nextPage == false;             
            }

            foreach ($results as $item) {
                $item_id = $item->source_id;     
                if($this->id_filter($item_id, $dbpost)){
                    $product = $item;
                    $hook= 0;               
                    break;
                } else {
                    //$pageToken = $item->nextPageToken;
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
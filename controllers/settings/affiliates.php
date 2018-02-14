<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Affiliates extends MY_Controller {

	private $amazon_countries = array(
                    'com'=>'USA' ,'ca' => 'Canada', 'co.uk' => 'United Kingdow', 
                    'de' =>'Deutschland', 'jp' =>'Japan', 'es' => 'Espa�a', 
                    'it' =>'Italy', 'fr' => 'France', 'cn' => 'China'
            );
	private $ebay_countries = array(                        
                        "EBAY-AT" =>"eBay Austria",
                        "EBAY-AU" => "eBay Australia",
                        "EBAY-CH" => "eBay Switzerland",
                        "EBAY-DE" => "eBay Germany",
                        "EBAY-ENCA" => "eBay Canada (English)",       
                        "EBAY-ES" =>"eBay Spain",            
                        "EBAY-FR" =>"eBay France",
                        "EBAY-FRBE" =>"eBay Belgium (French)",
                        "EBAY-FRCA" => "eBay Canada (French)",
                        "EBAY-GB" => "eBay UK",
                        "EBAY-HK" => "eBay Hong Kong",
                        "EBAY-IE" => "eBay Ireland",
                        "EBAY-IN" => "eBay India",
                        "EBAY-IT" => "eBay Italy",
                        "EBAY-MOTOR" => "eBay Motors", 
                        "EBAY-MY" => "eBay Malaysia",
                        "EBAY-NL" => "eBay Netherlands",
                        "EBAY-NLBE" => "eBay Belgium (Dutch)",
                        "EBAY-PH" => "eBay Philippines",
                        "EBAY-PL" => "eBay Poland",
                        "EBAY-SG" => "eBay Singapore",
                        "EBAY-US" => "eBay United States"
                     );  

    public function __construct() {
        parent::__construct();    
        $this->lang->load('affiliates_settings', $this->language);        
    }
    public function index()
    {
    	$this->form_validation->set_rules('amazon[associate_tag]', 'Associate Tag', 'required|xss_clean');
        $this->form_validation->set_rules('amazon[public_key]', 'Public Key', 'required|xss_clean');
    	$this->form_validation->set_rules('amazon[private_key]', 'Private Key', 'required|xss_clean');
        $this->form_validation->set_rules('amazon[country]', 'Amazon Country', 'required|xss_clean');
    	//$this->form_validation->set_rules('ebay[campaign_id]', 'Campaign ID', 'required|xss_clean');
        $this->form_validation->set_rules('ebay[country]', 'Ebay Country', 'required|xss_clean');

    	$this->template->set('amazon_country', null);
    	$this->template->set('ebay_country', null);

        if ($this->input->post()) {
            $config = $this->config->config;
            if (!$config['change_settings']) {
                $this->addFlash(lang('demo_error'));  
            } else {
	            if ($this->form_validation->run() === true) {
	            	//ddd($this->input->post());
	            	$affiliate_model = new affiliates_model();
	                $update = $affiliate_model->todb($this->c_user->id, $this->profile->id, $this->input->post());
	                if ($update) {
	                    $this->addFlash(lang('affiliate_settings_updated'), 'success');
	                    redirect('settings/affiliates');
	                } else {
	                    $this->addFlash($this->ion_auth->errors());
	                }
	            } else {
	                if (validation_errors()) {
	                    $this->addFlash(validation_errors());
	                }
	            }
	        }        	
        }  else{
			$affiliate_model = new affiliates_model();
        	$data = $affiliate_model->get_affiliate_info($this->c_user->id, $this->profile->id);
        	//$data = unserialize($_affiliate_model->data);
            //ddd($data);
	    	foreach ($data as $key => $value) {
	    		foreach ($value as $_key => $_value) {
	    		 	$this->template->set($key.'_'.$_key, $_value);
	    		 } 
	    	} 
        }                              

    	$this->template->set('amazon_countries', $this->amazon_countries);
    	$this->template->set('ebay_countries', $this->ebay_countries);
        $this->template->render();
    }      
}
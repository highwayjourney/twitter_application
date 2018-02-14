<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Asset extends MY_Controller
{
	private $plan;
	private $templates;

    public function __construct(){
        parent::__construct();
        $aac = $this->getAAC();
        if($aac->isGrantedPlan('pro_package')){
        	$this->plan = "PRO";
        	$this->templates[] = "PRO";
        }
        // if($aac->isGrantedPlan('lite_package')){
        // 	$this->plan = "LITE";
        // }
        if($aac->isGrantedPlan('max_package')){
        	$this->plan = "MAX";
        	$this->templates[] = "MAX"; 
        }
        // if($aac->isGrantedPlan('elite_package')){
        // 	$this->plan = "ELITE";
        // }        
        // if($aac->isGrantedPlan('black_package')){
        // 	$this->plan = "BLACK";
        // }
        if($aac->isGrantedPlan('monthly_package')){
        	$this->plan = "VIP";
        	$this->templates[] = "VIP";
        }
        if($aac->isGrantedPlan('yearly_package')){
        	$this->plan = "VIP";
        	$this->templates[] = "VIP";
        }                                        
    }

    public function user_designs($file_name){
    	if($this->template->is_ajax()){
	        if (preg_match("/^\d{1,10}$/", $file_name))
	        { 
	           	$file = dirname($_SERVER['SCRIPT_FILENAME']).'/assets/user_designs/'.$this->c_user->id.'/'.$file_name.'.json';
	            // Now check if file exists
	            if (file_exists($file))
	            {
	               // Serve the file
	               header('Content-Type: application/json');
	               readfile($file);
	           }
	        }  else {
	        	$this->error_404();
	        }     
	    }
    }
    public function redirect(){
    	redirect('social/campaign');
    }
    public function templates(){
    	if($this->template->is_ajax()){
    		$file_name = $this->input->post()['path'];
    		$serve = false;
	        if (preg_match("/\.json$/", $file_name))
	        { 
	        	foreach ($this->templates as $permission) {
	        		$file = dirname($_SERVER['SCRIPT_FILENAME']).'/assets/plans/'.$permission.'/'.$file_name;
		            if (file_exists($file))
		            {
		            	$serve = true;
		            	break;
		           }	           	        		
	        	}
	           if($serve){
	               header('Content-Type: application/json');
	               readfile($file);
	               exit();
	           }		        	
	        	$this->error_404();
			} else {
		        $this->error_404();
		    } 
	    }
    }
}
<?php

class Campaign_decommision_cron extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run() {
    	$numer_of_days = 15;
    	$secs = $numer_of_days*24*3600;
        $date = new DateTime('UTC');
        $now = $date->getTimestamp(); 
        $diff = $now - $secs;
        $campaign = new Social_campaign();
        $campaigns = $campaign->order_by('id', 'desc')
        					  ->where('updated !=', 0)
        					  ->where('updated <', $diff)
        					  ->where('created <', $diff)
        					  //->count(); 
        					   ->get(); 
        //ddd($campaigns->all_to_array());
        //ddd($campaigns); die();
        $i = 0;
        foreach($campaigns as $campaign) {
	        if($campaign->type != 'trivia' && !in_array('linkedin', unserialize($campaign->post_to_socials))){
	            if(!empty($campaign->user_id) && !empty($campaign->id)){
	                $files = glob(PUBPATH.'/uploads/'.$campaign->user_id.'/'.$campaign->id.'*');
	                foreach ($files as $file) {
	                	$i++;
	                	if(file_exists($file)){
	                		unlink($file);
	                	}	                    
	                }
	            }
	        }          
        }   
    }    
}
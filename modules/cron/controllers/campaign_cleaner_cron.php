<?php

class Campaign_cleaner_cron extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run() {
        $date = new DateTime('UTC');
        $number_of_hours = 12;
        $now = $date->getTimestamp(); 
        $diff = $now - $number_of_hours*3600;
        $campaign = new Social_campaign();
        $campaigns = $campaign
                              ->order_by('id', 'desc')
                              ->where('data_url', true)
                              ->where('updated <', $diff)
                              ->where('user_id', 2)
                              ->get(); 
        //ddd($campaigns->all_to_array());
        //ddd($campaigns);
        $i = 0;
        foreach($campaigns as $campaign) {
            $campaign->data_url = null;
            $campaign->save();
            $args = array('campaign_id' => $campaign->id);
            $i++;
            $this->jobQueue->addJob('tasks/campaign_builder_task/deleteItems',  $args, array(
                'thread' => self::SCHEDULED_QUEUE_THREAD
            ));
        }   
        log_message('TASK_DEBUG', __FUNCTION__ . ' > Cleaning Campaigns @12 hours cron '.$i);     
    }
    public function decomission() {
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
      log_message('TASK_DEBUG', __FUNCTION__ . ' > Decommisioning old campaigns items '.$i);  
    }
}
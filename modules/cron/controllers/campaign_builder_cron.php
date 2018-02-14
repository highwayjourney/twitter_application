<?php

class Campaign_builder_cron extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run() {
        $date = new DateTime('UTC');
        $now = $date->getTimestamp(); 
        $campaign = new Social_campaign();
        $campaignn = new Social_campaign();

        $running_campaigns = $campaignn->where(array('enable' => 'working', 'type' => 'animated'))->count();
        if($running_campaigns > 15){
            continue;
        } 
        $campaigns = $campaign->where('enable', 'pending')->get(); 
        //ddd($campaigns->all_to_array());
        foreach($campaigns as $campaign) {
            $flag = true;
            $user = new User($campaign->user_id);
            if (!$user->exists()) {
                $items = $campaign->social_campaigns_item->get();
                foreach ($items as $item) {
                    $item->delete();
                }                  
                $campaign->delete();
                $flag = false;
            }
            if($flag){
    	        $campaign->enable = 'working';
    	        $campaign->save();
                $args = array('campaign_id' => $campaign->id);

                $this->jobQueue->addJob('tasks/campaign_builder_task/executeCampaign',  $args, array(
                    'thread' => self::SCHEDULED_QUEUE_THREAD
                ));
            }
        }        
    }
}
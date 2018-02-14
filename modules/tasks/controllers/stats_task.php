<?php

class Stats_task extends CLI_controller {


    public function update($args) {
        $user_id = (int)$args['user_id'];
        $profile_id = (int)$args['profile_id'];
        $sent_post = new Social_sent_post();
        $result = $sent_post->getStatsbyGroup($user_id, $profile_id);   

        foreach ($result as $social => $data) {
            $_access_token = Access_token::getByTypeAndUserIdAndProfileId($social, $user_id, $profile_id);
            $access_token = $_access_token->to_array()['id'];
            foreach ($data as $_data) {
                foreach ($_data as $key => $value) {
                    $name = strtoupper($social."_".$key."_"."ANALYTICS_TYPE"); 
                    
                    // $now = new DateTime('UTC');
                    // $now->modify('-1 day');
                    // $yesterday = $now->format('Y-m-d');                   
                    // $day_before = social_analytics::getAnalytics($access_token, constant('Social_analytics::'.$name), $yesterday);
                    // if(!empty($day_before)){
                    //     $value = $value-$day_before->value;
                    // }
                    
                    Social_analytics::updateAnalytics(
                        $access_token,
                        constant('Social_analytics::'.$name),
                        $value,
                        null,
                        true
                    );
                    unset($day_before);                    
                }
            }
        }      
    }
}
<?php

class Twitter_cron extends CLI_controller {

    /**
     * Daily social statistic collect
     * Add new access token to Queue
     *
     * @access public
     * @return void
     */
    public function run() {
        $types = array('twitter');
        $tokens = Access_token::inst()
            ->where_in('type', $types)
            //->where('user_id', 3)
            ->order_by("id", "desc")      
            ->get();

        $aac = $this->getAAC();

        $now = new \DateTime('UTC');
        $tokens = $tokens->all_to_array();

        foreach($tokens as $_token) {
            $now->modify('2 seconds');
            $user = new User($_token['user_id']);
            if (!$user->exists()) {                
                continue;
            }
            $aac->setUser($user);
          
            if (!$aac->isGrantedPlan('social_activity')) {
                continue;
            }
            $profiles = Access_token::inst($_token['id'])->social_group->get()->all_to_array();

            foreach ($profiles as $profile) {
                $social_group = new social_group($profile['id']);
                $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
                $args['profile_id'] = $profile['id'];
                if(!empty($args['profile_id']) && !empty($args['token1']) && !empty($args['token2']) && !empty($args['user_id'])) {                


                    $this->jobQueue->addJob('tasks/twitter_task/searchUsers',  $args, array(
                        'thread' => self::SOCIAL_QUEUE_THREAD_SEARCH,
                        'execute_after' => $now
                    ));

                    $this->jobQueue->addJob('tasks/twitter_task/updateFollowers',  $args, array(
                        'thread' => self::SOCIAL_QUEUE_THREAD_UPDATE,
                        'execute_after' => $now
                    ));
                       
                    // if ($user->ifUserHasConfigValue('auto_send_welcome_message', $access_token_id)) {                
                        $this->jobQueue->addJob('tasks/twitter_task/sendWelcomeMessage',  $args, array(
                            'thread' => self::SOCIAL_QUEUE_THREAD_WELCOME,
                            'execute_after' => $now
                        ));
                    // }                
                    // if ($user->ifUserHasConfigValue('auto_follow', $access_token_id)) {                
                        $this->jobQueue->addJob('tasks/twitter_task/followNewFollowers',  $args, array(
                            'thread' => self::SOCIAL_QUEUE_THREAD_FOLLOW,
                            'execute_after' => $now
                        ));
                    // }             
                    // if ($user->ifUserHasConfigValue('auto_unfollow', $access_token_id)) {                
                        $this->jobQueue->addJob('tasks/twitter_task/unfollowUnsubscribedUsers',  $args, array(
                            'thread' => self::SOCIAL_QUEUE_THREAD_UNFOLLOW,
                            'execute_after' => $now
                        ));
                    // }
                    $_now = clone($now);
                    $_now->modify('300 seconds');
                    $this->jobQueue->addJob('tasks/twitter_task/getRetweets',  $args, array(
                        'thread' => self::SOCIAL_THREAD,
                        'execute_after' => $_now 
                    ));
                    $this->jobQueue->addJob('tasks/twitter_task/getMentions',  $args, array(
                        'thread' => self::SOCIAL_THREAD,
                        'execute_after' => $_now
                    ));                     
                    $this->jobQueue->addJob('tasks/twitter_task/getLists',  $args, array(
                        'thread' => self::SOCIAL_THREAD,
                        'execute_after' => $_now
                    ));   
                    $_now->modify('60 minutes');
                    $this->jobQueue->addJob('tasks/twitter_task/getFavourites',  $args, array(
                        'thread' => self::SOCIAL_THREAD,
                        'execute_after' => $_now
                    ));                                                                     
                }
            }
        }
    }

}
<?php

class Campaign_cron extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run() {
        try {
            //enabled Users
            $users = new user();
            $users = $users
                    ->where('active >', 0)
                    //->where('id', 3)
                    ->get();
            $now = new DateTime('UTC');
            $current_time = new DateTime();
            //log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'ACTION');
            foreach ($users as $user) {     
                $group = $user->group->get()->to_array();
                if($group['name'] != 'members'  || !User_timezone::get_user_timezone($user->id)){
                    //log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'User ID: '.$user->id.' is not active');
                    continue;
                }                
                $user_timezone = new DateTimeZone(User_timezone::get_user_timezone($user->id));
                $target_user_time = new DateTime('00:00:00', $user_timezone);
         
                $time_difference =   abs($current_time->getTimestamp() - $target_user_time->getTimestamp());
                $args = array('user_id' => $user->id);
                log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'User ID: '.$user->id.' time difference is: '.$time_difference);
                if($time_difference < 1805){
                    log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'CRON ADDED ');
                    $this->jobQueue->addJob('tasks/campaign_task/run',  $args, array(
                        'thread' => self::CAMPAIGN_THREAD_QUEUE,
                        'execute_after' => $now
                    ));                                       
                }
            }
        } catch(Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
        }
    }    
}
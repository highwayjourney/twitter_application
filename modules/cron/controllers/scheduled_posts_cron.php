<?php

class Scheduled_posts_cron extends CLI_controller {

    /* function __construct(){
        $this->load->library('activemq');
    } */

    /**
     * Daily social statistic collect
     * Add new access token to Queue
     *
     * @access public
     * @return void
     */
    public function run() {
        $date_now = new DateTime('UTC');
        $now = $date_now->getTimestamp();        
        $posts = Social_post::inst()
            ->where('posting_type', 'schedule')
            ->where('schedule_date <=', $now)
            ->where('disabled', null)
            ->get();

        //log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'scheduled posts count - '.$posts->result_count());

        $acc = $this->getAAC();

        /** @var Social_post $_post */
        foreach($posts as $_post) {
            $_post->disabled = true;
            $_post->save();
            $user = new User($_post->user_id);
            // if (!$user->exists()) {
            //     continue;
            // }
            $acc->setUser($user);

            if (!$acc->isGrantedPlan('scheduled_posts')) {
                continue;
            }

            $args = $_post->to_array();
            if($args['schedule_date'] <= $now){
                $this->jobQueue->addJob('tasks/scheduled_posts_task/check_for_sending',  $args, array(
                    'thread' => self::SCHEDULED_QUEUE_THREAD
                ));
            }
        }
    }

}
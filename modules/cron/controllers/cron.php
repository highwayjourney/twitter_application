<?php

class Cron extends CLI_controller {

    public function daily() {
        //Daily Stats
        // $this->jobQueue->addJob('cron/stats_cron/run', array(), array(
        //     'thread' => self::STATS_THREAD
        // ));
        //Recurrent Posts
        $this->jobQueue->addJob('cron/social_posts_cron/run', array(), array(
            'thread' => self::POST_CRON_THREAD
        ));      
        // //Some Twitter Tasks
        $this->jobQueue->addJob('cron/twitter_cron/run', array(), array(
            'thread' => self::SOCIAL_THREAD
        ));
        // //clearing of queue of jobs
        // $this->jobQueue->addUniqueJob('cron/job_queue_cron/clear');

    }

    public function minutely() {
        //log_message('TASK_DEBUG', __FUNCTION__ . 'Minutely');
        //Scheduled posts run
        $job_queue = new job_queue();
        $job_queue->dbMantain();   
        //if(!$job_queue->checkJob(self::SCHEDULED_QUEUE_THREAD)){
        $this->jobQueue->addJob('cron/scheduled_posts_cron/run', array(), array(
            'thread' => self::SCHEDULED_THREAD
        )); 
        //}                
    }

    public function tenminutely() {
        $this->jobQueue->addJob('tasks/twitter_task/sendRetweet', array(), array(
            'thread' => self::SOCIAL_THREAD
        ));
        $this->jobQueue->addJob('tasks/twitter_task/sendFavourite', array(), array(
            'thread' => self::SOCIAL_THREAD
        ));
        $this->jobQueue->addJob('tasks/twitter_task/sendMention', array(), array(
            'thread' => self::SOCIAL_THREAD
        ));                
    }

    public function hourly() {
        //Check if its midnight user time and schedule campaigns
        $this->jobQueue->addJob('cron/campaign_cron/run', array(), array(
            'thread' => self::CAMPAIGN_THREAD
        )); 
    }

    public function fourhourly() {

    }

    public function halfdaily() {

    }
}

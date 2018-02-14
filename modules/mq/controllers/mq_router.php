<?php
/**
 * User: Dred
 * Date: 26.02.13
 * Time: 16:45
 */

class mq_router extends CLI_controller{

    public function index(){

        $this->jobQueue->run(0);
        // echo "Stop MQ Router..." . date('Y-m-d H:i:s') . " started at " .  $start . " \n";
        // log_message('MQ_ROUTER', "Stop MQ Router..." . date('Y-m-d H:i:s') . " started at " .  $start);
    }

    public function convert(){

        $this->jobQueue->run(1);
        // echo "Stop MQ Converter..." . date('Y-m-d H:i:s') . " started at " .  $start . " \n";
        // log_message('MQ_ROUTER', "Stop MQ Converter..." . date('Y-m-d H:i:s') . " started at " .  $start);
    }

    public function scheduled() {
        $this->jobQueue->run(self::SCHEDULED_THREAD);
    }

    public function scheduled_queue() {
        $this->jobQueue->run(self::SCHEDULED_QUEUE_THREAD);      
    }
      
    public function mentions() {
        $this->jobQueue->run(self::MENTIONS_THREAD);
    }

    public function social() {
        $this->jobQueue->run(self::SOCIAL_THREAD);
    }

    public function social_queue_search() {
        $this->jobQueue->run(self::SOCIAL_QUEUE_THREAD_SEARCH);
    }

    public function social_queue_update() {
        $this->jobQueue->run(self::SOCIAL_QUEUE_THREAD_UPDATE);
    }

    public function social_queue_welcome() {
        $this->jobQueue->run(self::SOCIAL_QUEUE_THREAD_WELCOME);
    }

    public function social_queue_follow() {
        $this->jobQueue->run(self::SOCIAL_QUEUE_THREAD_FOLLOW);
    }

    public function social_queue_unfollow() {
        $this->jobQueue->run(self::SOCIAL_QUEUE_THREAD_UNFOLLOW);
    }                 

    public function campaign_scheduled() {
        $this->jobQueue->run(self::CAMPAIGN_THREAD);
    }

    public function campaign_queue() {
        $this->jobQueue->run(self::CAMPAIGN_THREAD_QUEUE);
    }

    public function post_cron() {
        $this->jobQueue->run(self::POST_CRON_THREAD);
    }

    public function stats() {
        $this->jobQueue->run(self::STATS_THREAD);
    }    
}
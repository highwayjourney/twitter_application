<?php
/**
 * User: dev
 * Date: 16.01.14
 * Time: 15:07
 */

require_once __DIR__.'/Base_Controller.php';

class CLI_controller extends Base_Controller
{
    /**
     * @var \Core\Service\Job\MysqlQueueManager
     */
    protected $jobQueue;

    const SCHEDULED_THREAD = 1;
    const SCHEDULED_QUEUE_THREAD = 2;
    const MENTIONS_THREAD = 3;
    const SOCIAL_THREAD = 4;
    const SOCIAL_QUEUE_THREAD_SEARCH = 5;
    const SOCIAL_QUEUE_THREAD_UPDATE = 6;
    const SOCIAL_QUEUE_THREAD_WELCOME = 7;
    const SOCIAL_QUEUE_THREAD_FOLLOW = 8;
    const SOCIAL_QUEUE_THREAD_UNFOLLOW = 9;
    const CAMPAIGN_THREAD = 10;
    const CAMPAIGN_THREAD_QUEUE = 11;
    const POST_CRON_THREAD = 12; 
    const STATS_THREAD = 13;    

    public function __construct(){
        // command line only

        if(!$this->input->is_cli_request()){
            exit;
        }

        parent::__construct();

        $this->jobQueue = $this->get('core.job.queue.manager');
    }

}
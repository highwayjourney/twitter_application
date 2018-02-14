<?php

class Stats_cron extends CLI_controller {


    public function run() {

        try{
            $user = new User();
            $users = $user->where('active >', 0)->get();

            foreach($users as $u){
                $groups = $u->social_group->get();
                $now = new \DateTime('UTC');
                foreach ($groups as $group) {
                    $now->modify('1 minutes');
                    $access_token = new Access_token;
                    $active_socials = $access_token->get_user_socials($u->id, $group->id);
                    if(empty($active_socials)){
                        continue;
                    }
                    $args['user_id'] = $u->id;
                    $args['profile_id'] = $group->id;
                    $this->jobQueue->addJob('tasks/stats_task/update',  $args, array(
                        'thread' => self::SCHEDULED_QUEUE_THREAD,
                        'execute_after' => $now
                    ));
                }
            }
            
        } catch (Exception $e) {
            
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
           
        }

    }



}
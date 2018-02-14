<?php

class Scheduled_posts_task extends CLI_controller {

    /**
     * Collect info from socials
     * (get twitter followers / facebook likes count)
     *
     * @access public
     * @param $post
     */
    public function check_for_sending( $post ){
        //set_time_limit(60);
        try {
            $date_now = new DateTime('UTC');
            $now = $date_now->getTimestamp();
            $post['schedule_date'] = (int)$post['schedule_date'];
            $social_post = Social_post::inst((int)$post['id']);

            if( $post['schedule_date'] <= $now ) {
                //$social_post->disabled = $social_post->disabled + 1;
                log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'ID - '.$post['id'].'; sch_date - '.$post['schedule_date'].'; now - '.$now);
                //$social_post->save();

                $this->load->library('Socializer/Socializer');
                $attachment = $social_post->media;
                if(!is_array($post['post_to_socials'])) {
                    $post['post_to_socials'] = unserialize($post['post_to_socials']);
                }
                if(!is_array($post['post_to_groups'])) {
                    $post['post_to_groups'] = unserialize($post['post_to_groups']);
                }
                //log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Entra 2'. microtime());
                if($attachment->id) {
                    //Need This for CRON
                    $image = PUBPATH.'/uploads/'.$post['user_id'].'/'.basename($attachment->path);
                    if (!file_exists($image)){
                        //throw new Exception("Image Doesn't exists", 1);   
                        $attachment->delete();
                        Social_post::inst()->_send_video_to_socials($post, $post['user_id']);
                        log_message('TASK_SUCCESS', __FUNCTION__ . ' > IMAGE NOT FOUND' . 'ID - '.$post['id']. ' USER_ID - '.$post['user_id']);
                    }
                    $post['image_name'] = basename($attachment->path);
                    if($attachment->type == 'video') {
                        Social_post::inst()->_send_video_to_socials($post, $post['user_id']);
                    } else {
                        //log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Entra 4'. serialize($post));
                        Social_post::inst()->_send_to_social($post, $post['user_id']);
                        //log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Entra 5'. microtime());
                    }
                } else {
                    
                    Social_post::inst()->_send_to_social($post, $post['user_id']);
                }
                $social_post->delete();
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'ID - '.$post['id']. ' USER_ID - '.$post['user_id']);
            }
        } catch (Exception $e) {
            $social_post->delete();
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'ID - '.$post['id'] . ' USER_ID - '.$post['user_id'] .' - '.$e->getMessage());
        }
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Smart_post extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }
    public function index()
    {    
        $http_post = $this->input->post();

        $campaign = Campaigns::inst();
        $campaigns = $campaign->where('user_id',$this->c_user->id)
                      ->where('profile_id',$this->profile->id)
                      ->where('type', 'affiliate')                    
                      ->get();        
        $this->template->set('campaigns', $campaigns);             
        
        $date_now = new DateTime('UTC');
        $now = $date_now->getTimestamp();        
        $posts = Social_post::inst()
            ->where('posting_type', 'schedule')
            //->where('schedule_date >', $now)
            ->where('disabled', null)
            ->where('campaign_id',$http_post['campaign'])
            ->where('user_id', $this->c_user->id)
            ->where('profile_id', $this->profile->id)
            ->where('type', 'affiliate')
            ->get(10);

        $p_posts = array();
        foreach ($posts as $post) {
            $_posts[] = $post;
            $posto = unserialize($post->campaign_data);
            //d($posto);
            $posto['id'] = $post->id;
            $p_posts[] = $posto;
        }  
        //die();          
        JsSettings::instance()->add(
            array(
                'serialPosts' => $p_posts,
            )
        );
        date_default_timezone_set(User_timezone::get_user_timezone($this->c_user->id));
        $this->template->set('posts', $_posts);
        $this->template->set('campana', $http_post['campaign']);
        
        CssJs::getInst()->c_js('twitter/smart_post', 'smart_post');
        CssJs::getInst()->add_css('post_picker.css');
        $this->template->render();
    }   
    public function delete(){
        if($this->template->is_ajax() ) {
            $post = $this->input->post();
            $_post = new Social_post($post['id']);
            try {
                $_post->delete();
            } catch (Exception $e) {
                echo json_encode(array(
                    'success' => false,
                    'message' => $e->getMessage()
                ));                
            }
            echo json_encode(array(
                'success' => true,
                'message' => 'Post Successfully Deleted'
            ));
        }
    }    

    public function postNow(){
        if( $this->template->is_ajax() ) {
            if(User_timezone::is_user_set_timezone($this->c_user->id)){
                $post = $this->input->post();
                $image = $post['image_name'];
                $_post = new Social_post($post['id']);
                $post = $_post->to_array();
                $post['post_to_socials'] = unserialize($post['post_to_socials']);
                $post['post_to_groups'] = unserialize($post['post_to_groups']);
                $post['posting_type']  = "immediate";
                $post['image_name']  = $image;
                $this->load->library('Socializer/Socializer');
                $attachment = $_post->media;
                try {
                    $at = new Access_token;
                    if(empty($at->get_array_by_type('twitter', $this->c_user->id, $this->profile->id))){
                        throw new Exception("You must add your Twitter Account first", 1);
                    }
                    if($attachment->type == 'video') {
                        Social_post::inst()->_send_video_to_socials($post, $this->c_user->id);
                    } else {
                        Social_post::inst()->_send_to_social($post, $this->c_user->id);
                    }                    
                } catch (Exception $e) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => $e->getMessage()
                    ));
                    exit();                      
                }             
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Message Sucessfully Posted'
                ));
                $_post->delete();
                exit();                              
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'You need to set your Timezone'
                ));
                exit();                
            }  
        }        
    }     
}   
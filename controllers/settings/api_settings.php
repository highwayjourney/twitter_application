<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api_settings extends MY_Controller {

    public function __construct() {
        parent::__construct();
        ///$this->lang->load('api_settings', $this->language);
    }

    public function index() {
        //ddd($this->c_user->id, $_POST);
        if ( ! empty($_POST)) {
            foreach ($_POST["twitter"] as $post_key => $value) {
     
               $rows = Twitter_api_key::inst()
                    ->where(array(
                            'user_id' => $this->c_user->id,
                            'key' => $post_key,
                        ))->get();

                if( $rows->result_count() > 0 ){
                    Twitter_api_key::inst()
                    ->where(array(
                            'user_id' => $this->c_user->id,
                            'key' => $post_key,
                        ))->update('value', $value ? $value : NULL);
                } else {
                    $twitter_api = Twitter_api_key::inst();
                    $twitter_api->user_id =  $this->c_user->id;
                    $twitter_api->key =  $post_key;
                    $twitter_api->value =  $value;
                    if($post_key == "consumer_key"){
                        $twitter_api->name =  "Consumer Key";
                    } else {
                        $twitter_api->name =  "Consumer Secret";
                    }
                    $twitter_api->save();                   
                }
            }
            $this->addFlash(lang('twitter_api_update_success'), 'success');
        }

        $api_keys = Twitter_api_key::inst()->value($this->c_user->id);
        $this->template->set('twitter', $api_keys);
        //die('d');
        $this->template->render();
    }
    
}
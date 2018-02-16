<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Engage extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('twitter_tools', $this->language);
        $this->lang->load('user_search_keywords', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('socialmedia', $this->language)
        ]);
    }

    /**
     * Used to show Social Media Settings Page
     * site_url/settings/socialmedia
     * Show Social-connect buttons
     *
     * @access public
     * @return void
     */
    public function index() {
       
        $access_token = new Access_token();
        $access_token = $access_token->getByTypeAndUserIdAndProfileId('twitter',$this->c_user->id,$this->profile->id);
        
        $this->load->library('Socializer/socializer');
        $twitter = Socializer::factory('Twitter', $this->c_user->id, $access_token->to_array());

        //update followers if there are no followers
        // $followers = $this->c_user->twitter_follower->where('access_token_id', $access_token->id)->order_by('id','random')->get(1)->all_to_array('follower_id');
        $now = new \DateTime('UTC');
        // if(empty($followers)){
        //     $social_group = new social_group($this->profile->id);
        //     $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
        //     $args['profile_id'] = $this->profile->id;
        //     if(!empty($args['profile_id']) && !empty($args['token1']) && !empty($args['token2']) && !empty($args['user_id'])) {                


        //         $this->jobQueue->addJob('tasks/twitter_task/searchUsers',  $args, array(
        //             'thread' => 5,
        //             'execute_after' => $now
        //         ));

        //         // $this->jobQueue->addJob('tasks/twitter_task/updateFollowers',  $args, array(
        //         //     'thread' => self::SOCIAL_QUEUE_THREAD_UPDATE,
        //         //     'execute_after' => $now
        //         // ));  
        //     }          
        // }

        //get suggested lists from DB
        $lists = $this->profile->suggested_list->get()->to_array();        
        $lists = unserialize($lists['data']);

        if(empty($lists)){
            //Get Lists info
            $followers = $this->c_user->twitter_follower->where('access_token_id', $access_token->id)->order_by('id','random')->get()->all_to_array('follower_id');
            if(!empty($followers)){
                try {
                    $lists = $twitter->getLists($followers, 5);
                } catch (Exception $e) {
                    $lists = [];
                }
                if(!empty($lists)){
                    $suggested_list = $this->profile->suggested_list;
                    $suggested_list->to_db($lists, $this->c_user->id, $this->profile->id);
                }
            }
        }

        //ddd($this->c_user->getUserSearchKeywords($this->profile->id)->all_to_array());
        //get selected Lists
        $current_lists = $this->profile->current_list->get()->all_to_array();

        //get next people following
        // $will_follow = $this->c_user->twitter_follower
        //     ->where('need_follow', true)
        //     ->where('access_token_id', $access_token->id)
        //     ->where('start_follow_time >', $now->getTimestamp())
        //     ->order_by('start_follow_time', 'asc')  
        //     ->get(10)->all_to_array('follower_id');     
        $will_follow = $this->c_user->twitter_follower
            ->where('need_follow', true);

        // echo count($will_follow);
        // var_dump($will_follow);
        // exit();


        //get next retweets
        $will_retweet = $this->c_user->twitter_retweet
            ->where('need_retweet', true)
            ->where('access_token_id', $access_token->id)
            ->where('start_retweet_time >', $now->getTimestamp())    
            ->order_by('start_retweet_time', 'asc')
            ->get(10)->all_to_array('tweet_id');   

        //get next favs
        $will_fav = $this->c_user->twitter_favourite
            ->where('need_favourite', true)
            ->where('access_token_id', $access_token->id) 
            ->where('start_favourite_time >', $now->getTimestamp())   
            ->order_by('start_favourite_time', 'asc') 
            ->get(10)->all_to_array('tweet_id');   

        //get next mentions
        $will_mention = $this->c_user->twitter_mention
            ->where('need_mention', true)
            ->where('access_token_id', $access_token->id) 
            ->where('start_mention_time >', $now->getTimestamp())               
            ->order_by('start_mention_time', 'asc') 
            ->get(10)->all_to_array(array('message', 'user_image', 'id')); 
        //ddd($will_mention);
        if(empty($access_token->id)){
            $this->addFlash('Please add a Twitter account first', 'error');
            redirect('settings/socialmedia');
        }
        if (!$this->profile->has_account($access_token->id)) {
            redirect('settings/socialmedia');
        }

        $tw_lan = new Twitter_language();
        $available_lang = $tw_lan->getAll();

        $available_configs = Available_config::getByTypeAsArray($access_token->type, []);


        $woid = $this->c_user->ifUserHasConfigValue('twitter_locale', $access_token->id);
        if(empty($woid)){
            $woid = 1; //Worldwide
        }
        //Get Twitter Trends
        try{
            $_full_trends = $twitter->trends($woid);
            $trends = array_slice($_full_trends, 0,7);
            foreach ($_full_trends as $value) {
                $full_trends[] = $value->name;
            }            
        } catch (Exception $e) {
            $_full_trends = array();
            $this->addFlash('We can\'t connect to your Twitter Account, please try to add it again');
        }
        $engage_settings = array(
                                 'smart_engage' => $this->c_user->ifUserHasConfigValue('smart_engage', $access_token->id),
                                 'auto_follow' => $this->c_user->ifUserHasConfigValue('auto_follow', $access_token->id),
                                 'auto_retweet' => $this->c_user->ifUserHasConfigValue('auto_retweet', $access_token->id),
                                 'auto_favourite' => $this->c_user->ifUserHasConfigValue('auto_favourite', $access_token->id),
                                 'smart_mention' => $this->c_user->ifUserHasConfigValue('smart_mention', $access_token->id)
                                );

        $keywords = User_search_keyword::inst()->get_user_keywords($this->c_user->id, $this->profile->id);

        $new_keywords = array();
        $errors = array();
        $saved_ids = array(0);  // '0' to prevent datamapper error caused by empty array
        $delete = true;

        if ($post = $this->input->post()) {

            unset($post['submit']);
            //d($post);
            $grouped = Arr::collect($post);
            //ddd($grouped);
            foreach ($grouped as $id => $data) {
                if (strpos($id, 'new_') === 0) {
                    $keyword = User_search_keyword::inst()->fill_from_array($data, $this->c_user->id, $this->profile->id);
                    $new_keywords[$id] = $keyword;
                } else {
                    $keyword = User_search_keyword::inst()->fill_from_array($data, $this->c_user->id, $this->profile->id, $id);
                    if ($keyword->id !== $id) {
                        $new_keywords[$id] = $keyword;
                    }
                }
                if ($keyword->save()) {
                    $saved_ids[] = $keyword->id;
                } else {
                    $errors[$id] = $keyword->error->string;
                }
            }

            if (empty($errors)) {
                if ($delete) {
                    User_search_keyword::inst()->set_deleted($this->c_user->id, $this->profile->id, $saved_ids);
                }
                $this->addFlash(lang('keywords_saved_success'), 'success');
                redirect('connect/engage');
            } else {
                $this->addFlash(implode('<br>', Arr::map('strip_tags', $errors)));
            }
        }

        $outp_keywords = array();
        foreach ($keywords as $keyword) {
            $outp_keywords[$keyword->id] = $keyword;
        }
        //get retweet quotes from config
        $quotes = $this->c_user->ifUserHasConfigValue('retweet_quote', $access_token->id);
        $mention_quotes = $this->c_user->ifUserHasConfigValue('mention_quote', $access_token->id);
        $website = $this->c_user->ifUserHasConfigValue('mention_website', $access_token->id);
        //ddd(unserialize($mention_quotes), unserialize($quotes ));
        //ddd($this->c_user->ifUserHasConfigValue('retweet_quote', $access_token->id));
        //ddd(unserialize(base64_decode($mention_quotes)));

        $this->template->set('mention_quotes', unserialize(base64_decode($mention_quotes)));
        $this->template->set('mentions', $will_mention);
        $this->template->set('website', $website);
        $this->template->set('quotes', unserialize(base64_decode($quotes)));
        $this->template->set('will_follow', $will_follow);
        $this->template->set('classes', array("btn btn-xs btn-info", "btn btn-xs btn-success", "btn btn-xs btn-danger", "btn btn-xs btn-primary", "btn btn-xs tw-bg text-white", "btn btn-xs linkedin-bg text-white", "btn btn-xs pink-bg text-white"));
        $this->template->set('trends', $trends);
        $this->template->set('current_lists', $current_lists);
        $this->template->set('lists', $lists);
        $this->template->set('available_lang', $available_lang);
        $this->template->set('keywords', $outp_keywords);
        $this->template->set('engage_settings', $engage_settings);
        //$this->template->set('not_display_configs_values', $not_display_configs_values);


        //Fecth data for retweets and Favourites
        $twitter = $this->initializeTwitter();
        foreach ($will_retweet as $value) {
            $_will_retweet[] = $value['tweet_id'];
        }
        unset($will_retweet);
        $retweet_data = $twitter->get_single_tweet(implode(",", $_will_retweet));

        foreach ($will_fav as $value) {
            $_will_fav[] = $value['tweet_id'];
        }
        unset($will_fav);
        $fav_data = $twitter->get_single_tweet(implode(",", $_will_fav));
        //ddd($retweet_data, $fav_data);
        CssJs::getInst()
            ->add_js(array(
                'masonry-docs.min.js',
                'masonry.pkgd.min.js'
            ));
        CssJs::getInst()
            ->add_css(array(
                'connect/index.css'
            ));            
        CssJs::getInst()
            ->c_js('settings/user_search_keywords', 'index');
        CssJs::getInst()
            ->c_js('connect/engage','index');

        JsSettings::instance()->add(array(
            'lists' => $lists,
            'full_trends' => $full_trends,
            'retweet' => $retweet_data,
            'favourite' => $fav_data
        ));            
        //ddd($retweet_data, $fav_data);
        if(empty($retweet_data)){
            $this->template->set('retweet_data', true);
        }
        if(empty($fav_data)){
            $this->template->set('favourite_data', true);
        }
        $this->template->set('token', $access_token);
        $this->template->render();
    }

    public function add_list(){
        $post = $this->input->post();
        if($post){
            $current_list = new Current_list;
            //var_dump($post['lists'], $post['list']);
            $id = $current_list->add_new($post['lists'][$post['id']], $this->c_user->id, $this->profile->id);
            if($id){
                echo json_encode(array('success' => true, 'message' => 'List added', 'id' => $id));
            } else {
                echo json_encode(array('success' => false, 'message' => 'List already added'));
            }
        }
        exit();
    }

    public function delete_list(){
        $post = $this->input->post();
        if($post){
            $current_list = new Current_list($post['id']);
            if($current_list->delete()){
                echo json_encode(array('success' => true, 'message' => 'List deleted'));
            } else {
                echo json_encode(array('success' => false, 'false' => 'Error'));
            }
        }
        exit();
    }

    private function update_lang(){
        $this->load->library('Socializer/socializer');
        $twitter = Socializer::factory('Twitter', $this->c_user->id, $access_token->to_array());
        $tw_lan = Twitter_language::inst();
        $tw_lan->update($twitter->get_languages());        
    }

    public function remove_suggested(){
        $post = $this->input->post();
        try {
            if($post){
                unset($post['lists'][$post['id']]);
                $suggested_list = new  Suggested_list;
                $suggested_list->to_db($post['lists'], $this->c_user->id, $this->profile->id);
                echo json_encode(array('success' => true, 'message' => 'Suggestion removed'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();        
    }
    public function get_followers_data(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if(!empty($post['follower_ids'])){
                $follower_ids = $post['follower_ids'];
                foreach ($follower_ids as $value) {
                    $followers_data[] = $twitter->get_user_full_info(null, $value);
                }
                echo json_encode(array('success' => true, 'data' => $followers_data));
            } else {
                echo json_encode(array('success' => true, 'data' => false));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }
    public function get_tweet_data(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $follower_ids = $post['tweet_id'];
                $followers_data = $twitter->get_single_tweet(implode(",", $follower_ids));
                echo json_encode(array('success' => true, 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    } 
    public function save_quote()
    {
        $post = $this->input->post();
        try {            
            if($post){
                $access_token = new Access_token();
                $access_token = $access_token->getByTypeAndUserIdAndProfileId('twitter',$this->c_user->id,$this->profile->id);
                $userConfig = $this->c_user->setConfig('retweet_quote', !empty($post['quote']) ? base64_encode(serialize($post['quote'])) : '', $access_token->id);
                if (!$userConfig) {
                    $error_message = preg_replace('|<p>|', '', $userConfig->error->string);
                    $error_message = preg_replace('|</p>|', '<br>', $error_message);
                    throw new Exception($error_message, 1);                    
                }       
                echo json_encode(array('success' => true, 'message' => 'Success'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit(); 
    }
    public function save_mention_quote()
    {
        $post = $this->input->post();
        try {            
            if($post){
                $access_token = new Access_token();
                $access_token = $access_token->getByTypeAndUserIdAndProfileId('twitter',$this->c_user->id,$this->profile->id);
                //var_dump($post['quote']); die();
                $userConfig = $this->c_user->setConfig('mention_quote', !empty($post['quote']) ? base64_encode(serialize($post['quote'])) : '', $access_token->id);
                if (!$userConfig) {
                    $error_message = preg_replace('|<p>|', '', $userConfig->error->string);
                    $error_message = preg_replace('|</p>|', '<br>', $error_message);
                    throw new Exception($error_message, 1);                    
                }      
                $userConfig = $this->c_user->setConfig('mention_website', !empty($post['website']) ? $post['website'] : '', $access_token->id);
                if (!$userConfig) {
                    $error_message = preg_replace('|<p>|', '', $userConfig->error->string);
                    $error_message = preg_replace('|</p>|', '<br>', $error_message);
                    throw new Exception($error_message, 1);                    
                }                   
                echo json_encode(array('success' => true, 'message' => 'Success'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit(); 
    }
    public function save_mention_website()
    {
        $post = $this->input->post();
        try {            
            if($post){
                $access_token = new Access_token();
                $access_token = $access_token->getByTypeAndUserIdAndProfileId('twitter',$this->c_user->id,$this->profile->id);
                $userConfig = $this->c_user->setConfig('mention_website', !empty($post['website']) ? $post['website'] : '', $access_token->id);
                if (!$userConfig) {
                    $error_message = preg_replace('|<p>|', '', $userConfig->error->string);
                    $error_message = preg_replace('|</p>|', '<br>', $error_message);
                    throw new Exception($error_message, 1);                    
                }       
                echo json_encode(array('success' => true, 'message' => 'Success'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit(); 
    }  
    public function mention(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $id = $post['tweet_id'];
                $tweet = new twitter_mention($id);            
                $tweet->need_mention = 0;
                $tweet->save();             
                $followers_data = $twitter->tweet($tweet->message);
                echo json_encode(array('success' => true, 'message' => 'Success', 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }  
    public function delete_mention(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $id = $post['id'];
                $tweet = new twitter_mention($id);             
                $tweet->need_mention = 0;
                $tweet->save(); 
                echo json_encode(array('success' => true, 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }                
    public function retweet(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $follower_ids = $post['tweet_id'];
                $tweet = $this->c_user->twitter_retweet->where('tweet_id', $follower_ids)->get();             
                $tweet->need_retweet = 0;
                $tweet->save();                
                $followers_data = $twitter->retweet($follower_ids);
                echo json_encode(array('success' => true, 'message' => 'Success', 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    } 
    public function favourite(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $follower_ids = $post['tweet_id'];
                $tweet = $this->c_user->twitter_favourite->where('tweet_id', $follower_ids)->get();             
                $tweet->need_favourite = 0;
                $tweet->save();                
                $followers_data = $twitter->favorite($follower_ids);
                echo json_encode(array('success' => true, 'message' => 'Success', 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }        
    public function delete_retweet(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $follower_ids = $post['tweet_id'];
                $tweet = $this->c_user->twitter_retweet->where('tweet_id', $follower_ids)->where('user_id', $this->c_user->id)->get();             
                $tweet->need_retweet = 0;
                $tweet->save();                
                //$followers_data = $twitter->retweet($follower_ids);
                echo json_encode(array('success' => true, 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }    
    public function delete_favourite(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $follower_ids = $post['tweet_id'];
                $tweet = $this->c_user->twitter_favourite->where('tweet_id', $follower_ids)->where('user_id', $this->c_user->id)->get();             
                $tweet->need_favourite = 0;
                $tweet->save();                
                //$followers_data = $twitter->retweet($follower_ids);
                echo json_encode(array('success' => true, 'data' => $followers_data));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }              
    public function follow(){
        $post = $this->input->post();
        try {
            $twitter = $this->initializeTwitter();            
            if($post){
                $follower_id = $post['user_id'];
                $follower = $this->c_user->twitter_follower->where('follower_id', $follower_id)->get();             
                $follower->setNeedFollow(0);
                $data = new DateTime('UTC');
                $follower->setUnfollowTime($data->getTimestamp());
                $follower->setStillFollow(0);
                $follower->save();
                $twitter->follow($follower_id);                
                echo json_encode(array('success' => true, 'message' => 'Success'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }  
    public function noFollow(){
        $post = $this->input->post();
        try {           
            if($post){
                $follower_id = $post['user_id'];
                $follower = $this->c_user->twitter_follower->where('follower_id', $follower_id)->get();             
                $result = $follower->delete();    
                if($result->errors[0]){
                    throw new Exception($result->errors[0]->message, 1);
                }            
                echo json_encode(array('success' => true, 'message' => 'Success'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();         
    }        
    public function toogle_current_list(){
        $post = $this->input->post();
        try {
            if($post){
                $current_list = Current_list::inst($post['id']);
                $current_list->toogle_show();
                echo json_encode(array('success' => true, 'message' => 'Updated'));
            }            
        } catch (Exception $e) {
            echo json_encode(array('success' => false, 'false' => $e->getMessage()));
        }
        exit();        
    }
    private function initializeTwitter(){
            $access_token = new Access_token();
            $access_token = $access_token->getByTypeAndUserIdAndProfileId('twitter',$this->c_user->id,$this->profile->id);            
            $this->load->library('Socializer/socializer');
            $twitter = Socializer::factory('Twitter', $this->c_user->id, $access_token->to_array());  
            return $twitter;      
    }

}
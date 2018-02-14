<?php

class Campaign_cron extends MY_Controller {

    /**
     * @access public
     * @return void
     */

    public function go(){
        $this->load->library('Socializer/Socializer');
        $group = new Social_group($this->profile->id);
        $token = $group->access_token->where('type', 'youtube')->get(); 
        $youtube = Socializer::factory('Google', $user_id, $token);
        //d($token->data);
        $results = $youtube->search_videos('ps4', null, $token);
        d($results);

        // $token = Access_token::inst()->get_youtube_token($this->c_user->id);
        // ddd($token->to_array());
    }
    public function run($args) {      
        try {
            $args = array('user_id' => 3);
            $user_id = $args['user_id'];
            $user_timezone = User_timezone::get_user_timezone($user_id);
            $user_timezone = new DateTimeZone($user_timezone);
            $date = new DateTime();
            $date->setTimezone($user_timezone);
            $target_user_time = new DateTime('00:00:00', $user_timezone);

            $time_difference =   abs($date->getTimestamp() - $target_user_time->getTimestamp());
            
            // if($time_difference > 3600){
            //     throw new Exception("Triying to Run Campaign +1 hour midnight. USER_ID: ".$user_id.' ; '.$target_user_time->timezone, 1);
            // }
            $max_post_number = 10;
            $random = array('-1 minutes','-2 minutes','-3 minutes','-4 minutes','-5 minutes', '-5 minutes', '+1 minutes','+2 minutes','+3 minutes','+4 minutes','+5 minutes');
            $data = array();
            $user = new User($user_id);
            $group = $user->group->get()->to_array();

            if($group['name'] == 'members' && $user->active == 1 && User_timezone::get_user_timezone($user->id)){
                //profiles
                $profiles = array();
                foreach ($user->social_group->get() as $profile) {
                    $group = new Social_group($profile->id);
                    $token = $group->access_token->get()->all_to_array(); 
                    if(empty($token)){
                        continue;
                    }                         
                    $campaign = new campaigns();
                    $campaigns = $campaign->get_user_campaigns($user->id, $profile->id);
                    $node = array();
                    foreach ($campaigns as  $campaign) {
                        $node[$campaign->id] = $campaign->priority;
                    }
                    $post_interval = 1440/$max_post_number;
                    $_midnight = clone $date;

                    if(!empty($node)){
                        for ($i=0; $i < $max_post_number; $i++) { 
                            $mod = $random[array_rand($random)];
                            $time = $_midnight->modify('+ '.$post_interval.' minutes');
                            $time = $_midnight->modify($mod);
                            $_campaign = $this->generateWeighedRandomValue($node);
                            $social_post = $this->getPost(new Campaigns($_campaign));
                            $social_post['schedule_date'] = $time->getTimestamp();
                            $social_post['user_id'] = $user->id;
                            //$social_post->url = ;
                            $social_post['profile_id'] = $profile->id;
                            $social_post['campaign_id'] = $_campaign;
                            $social_post['type'] = 'campaign';
                            $social_post['post_to_groups'] = serialize($social_post['post_to_groups']); 
                            $social_post['post_to_socials'] = serialize($social_post['post_to_socials']);
                            //d('got a post', $_campaign);
                            $this->addPost($social_post);
                        }
                    }
                }
            }
        } catch(Exception $e) {
            log_message('CAMPAIGN_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
        }
    }
    public function addPost($post_data){
        $social_post = new Social_post();
        foreach ($post_data as $key => $value) {
            $social_post->$key = $value;
        }
        $social_post->posting_type = 'schedule';
        $social_post->category_id = 0;
        //d($post_data);
        if(!empty($post_data['image_name'])){
            $media = new Media();
            $media->path = PUBPATH.'/uploads/'.$post_data['user_id'].'/'.$post_data['image_name'];
            //d($media->path);
            $media->type = 'image';
            $media->user_id  = $post_data['user_id'];
            $media->save();
            $social_post->save($media, 'media');
        }
        $social_post->save();      
    }

    public function generateWeighedRandomValue($nodes) {
        $weights = array_values($nodes);
        $values = array_keys($nodes);
        $count = count($values);
        $i = 0;
        $n = 0;
        $num = mt_rand(0, array_sum($weights));
        while($i < $count) {
            $n += $weights[$i];
            if($n >= $num) {
                break;
               }
            $i++;
           }
        return $values[$i];
    }

    public function getSocialPost($source, $user_id, $profile_id, $token, $keyword, $dbPosts, $campaign){
        $this->load->library('Socializer/Socializer');
        $social = Socializer::factory(ucfirst($source), $user_id, $token);
        $_source = $source.'_filter';
        $post = $social->$_source($keyword, $social, $dbPosts);
        $source_id = null;
        if($source == 'facebook'){
            $source_id = $post->data[0]->source_id; 
        } else {
            $source_id = $post->source_id;
        }        
        if(!empty($source_id)){
            $_post = $this->buildPost($campaign, $post, $source);
            $campaignPosts = new Campaign_posts;
            $campaignPosts->add_new_post($user_id, $source_id, $profile_id, $source);
            return $_post;
        }   
        return false;      
    }

    public function getPost($campaign){
        $campaign = $campaign->to_array();
        $sources = json_decode($campaign['sources']);
        $keywords = json_decode($campaign['keywords']);
        d($sources, $keywords);
        $counter = 0;

        $this->load->library('giphy/giphy');
        $this->load->library('Socializer/Socializer');

        $group = new Social_group($campaign['profile_id']);
        $token = $group->access_token->get()->all_to_array(); 
        if(empty($token)){
            throw new Exception("No tokens found", 1);
        }        
        foreach ($token as $_token) {
           $tokens[$_token['type']] = $_token;
        }
        $post = null;
        $campaignPosts = new Campaign_posts;                 
        $socials = array('facebook', 'youtube'); 
        $affiliates = array('amazon', 'ebay');       
        while (empty($post)) {
            if($counter > 10){
                break; 
            }            
            $counter++;
            $source = $sources[array_rand($sources)];
            $keyword = $keywords[array_rand($keywords)]; 
            
            d($keyword, $source);

            $dbPosts = $campaignPosts->get_user_posts($campaign['user_id'], $source, $campaign['profile_id']);
            
            if(in_array($source, $socials)){
                $post = $this->getSocialPost($source, $campaign['user_id'], $campaign['profile_id'], $tokens[$source], $keyword, $dbPosts, $campaign);
            }
            if(in_array($source, $affiliates)){
                $post = $this->getAffiliatePost($source, $campaign['user_id'], $campaign['profile_id'], $tokens[$source], $keyword, $dbPosts, $campaign);
            }
            if($source == 'giphy'){
                $post = $this->getGiphyPost($keyword, $campaign['user_id'], $campaign['profile_id'], $dbPosts, $campaign);
            }
            
            unset($dbPosts);
        }
        return $post;
        // $affiliate_model = new affiliates_model();
        // $common_settings = $affiliate_model->get_affiliate_info($campaign['user_id']);
    }

    public function getGiphyPost($keyword, $user_id, $profile_id, $dbPosts, $campaign){
        $giphy = new Giphy();
        $post = $giphy->getFilteredPost($keyword, $dbPosts);
        $source_id = null;
        $source_id = $post->id;       
        if(!empty($source_id)){
            $_post = $this->buildPost($campaign, $post, 'giphy');
            $campaignPosts = new Campaign_posts;
            $campaignPosts->add_new_post($user_id, $source_id, $profile_id, 'giphy');
            return $_post;
        }   
        return false;        
    }
    public function buildPost($campaign, $data, $source){

        if($source == "youtube" || $source == "giphy"){
            $post['post_to_socials'] = array("twitter");
            $post['post_to_groups'] = array($campaign['profile_id']);
            if($source == 'giphy'){
                $post['description'] =  $data->bitly_gif_url." via @giphy";
                $post['campaign_data'] = serialize(array('embed_url' => $data->embed_url, 'source_id' => $data->id, 'source' => $source));
            }
            if($source == 'youtube'){
                $post['description'] = "https://www.youtube.com/watch?v=".$data->source_id;
                $post['campaign_data'] = serialize(array('source_id' => $data->source_id, 'source' => $source));
            }
            //d($post);
            return $post;            
        }
        if($source == "facebook" || $source == "instagram"){
            //we are only auto posting on twitter
            $post['post_to_socials'] = array("twitter");
            $post['post_to_groups'] = array($campaign['profile_id']);
            $post['description'] = substr($data->data[0]->description, 0, 100);

            $nameImage = time() . '.png';
            $post['campaign_data'] = serialize(array('source_id' => $data->data[0]->source_id, 'source' => $source, 'media_url' =>$data->data[0]->media_url, 'image_name' => $nameImage));
            $url = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/uploads/' . $campaign['user_id'] . '/';
            if(!is_dir($url)) {
                mkdir($url, 0755, TRUE);
                chown($url, "autosoci");
                chgrp($url, "autosoci");
            }
            //Save Image
            file_put_contents ($url.$nameImage,file_get_contents($data->data[0]->media_url));              
            //$post['image_name'] = $nameImage;
            //d($post);
            return $post;            
        }

        if($data->source == "amazon" || $data->source == "ebay"){
            $post['post_to_socials'] = array("twitter");
            $post['post_to_groups'] = array($campaign['profile_id']);
            $post['description'] = substr($data->description, 0, 80);
            
            $nameImage = time() . '.png';
            $post['campaign_data'] = serialize(array('source_id' => $data->source_id, 'source' => $source, 'media_url' =>$data->media_url, 'image_name' => $nameImage));
            $url = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/uploads/' . $campaign['user_id'] . '/';
            if(!is_dir($url)) {
                mkdir($url, 0755, TRUE);
                chown($url, "autosoci");
                chgrp($url, "autosoci");
            }
            //Save Image
            file_put_contents ($url.$nameImage,file_get_contents($data->media_url)); 
            $image = new Imagick(); 
            $image->readImage($url.$nameImage);
            $draw = new ImagickDraw(); 
            $width = $image->getimagewidth(); 
            $draw->setFillColor('#ffffff'); 
            //$draw->setFont('/home/sociamplify/sociamplify.com/app/public/uploads/font/fredoka.ttf'); 
            $draw->setFontSize($width/16); 
            $draw->setTextUnderColor('#a8a3a3'); 
            $draw->setGravity(Imagick::GRAVITY_SOUTHEAST);                        
            $image->annotateImage($draw,0,$width/16,0,"Publisher may get a commission");
            $image->writeImage();            

            $post['image_name'] = $nameImage;
            $bitly_config = Api_key::build_config('bitly');
            if (isset($bitly_config['username'], $bitly_config['apikey'])) {
                $args = array(
                    'login' => $bitly_config['username'],
                    'apiKey' => $bitly_config['apikey'],
                );
                $post['url'] = $this->customShorten($data->url);
            }                       
            return $post;            
        }                    
    }
    public function customShorten($url){
        $timestamp = time();
        $signature = md5( $timestamp . '64658502f8' ); 
        $api_url =  'http://tracklix.com/yourls-api.php';

        // Init the CURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
        curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
                'action'   => 'shorturl',
                'timestamp' => $timestamp,
                'signature' => $signature,
                'url' => $url,
                'format' => 'json'

            ));

        // Fetch and return content
        $data = curl_exec($ch);
        curl_close($ch);

        // Do something with the result. Here, we echo the long URL
        $data = json_decode( $data );
        if(!empty($data->shorturl)){
            return $data->shorturl; 
        } else {
            throw new Exception($data->message, 1);
            return false;
        }
    }  
}
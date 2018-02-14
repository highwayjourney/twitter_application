<?php

class Campaign_task extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run($args=null) {      
        try {
            //$args = array('user_id' => 3);
            //d('empezando', microtime());
            $this->load->library('giphy/giphy');
            $this->load->library('Socializer/Socializer');            
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

            log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Starting Campaign Posts Task');
            
            if($group['name'] == 'members' && $user->active > 0 && User_timezone::get_user_timezone($user->id)){
                //profiles
                $profiles = array();
                foreach ($user->social_group->get() as $profile) {
                    $group = new Social_group($profile->id);
                    $token = $group->access_token->get()->all_to_array(); 
                    if(empty($token)){
                        continue;
                    }                        
                    $campaign = new campaigns();
                    $social_campaigns = $campaign->get_user_campaigns($user->id, $profile->id, 'social');
                    $campaign = new campaigns();
                    $affiliate_campaigns = $campaign->get_user_campaigns($user->id, $profile->id, 'affiliate');
                    //d($social_campaigns->all_to_array(), $affiliate_campaigns->all_to_array());
                    $node = array();
                    $affiliate_node = null;
                    $social_node = null;
                    foreach ($social_campaigns as  $campaign) {
                        $social_node[$campaign->id] = $campaign->priority;
                    }
                    foreach ($affiliate_campaigns as  $campaign) {
                        $affiliate_node[$campaign->id] = $campaign->priority;
                    }                    

                    if(!empty($affiliate_node) && !empty($social_node)){
                        $max_post_number = 10;
                    }
                    $node[] = $affiliate_node;
                    $node[] = $social_node;
                    $node = array_filter($node);
                    //echo 'dd';
                    //d($social_node , $affiliate_node);

                    $post_interval = 1440/$max_post_number;
                    $_midnight = clone $date;

                    if(!empty($social_node) || !empty($affiliate_node)){
                        //d($node);
                        for ($i=0; $i < $max_post_number; $i++) { 
                            //d('Empezando Post #'.$i, microtime());
                            $_node = $node[array_rand($node)];
                            $_campaign = $this->generateWeighedRandomValue($_node);
                            //d($_campaign);
                            $campania = new Campaigns($_campaign);

                            $mod = $random[array_rand($random)];
                            $time = $_midnight->modify('+ '.$post_interval.' minutes');
                            $time = $_midnight->modify($mod);                            
                            $social_post = $this->getPost($campania);
                            if(empty($social_post)){
                                //d('devolvio post vacio#'.$i, microtime());
                                continue;
                            }
                            //d('Devovlvio Post #'.$i, microtime());
                            $social_post['schedule_date'] = $time->getTimestamp();
                            $social_post['user_id'] = $user->id;
                            //$social_post->url = ;
                            $social_post['profile_id'] = $profile->id;
                            $social_post['campaign_id'] = $_campaign;
                            $social_post['type'] = $social_post['type'];
                            $social_post['post_to_groups'] = serialize($social_post['post_to_groups']); 
                            $social_post['post_to_socials'] = serialize($social_post['post_to_socials']);
                            //d('got a post', $_campaign);
                            $this->addPost($social_post);
                            unset($social_post);
                        }
                    }
                }
                //d('termino', microtime());
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Campaign Posts Created');
            }
        } catch(Exception $e) {
            //d($e);
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
        unset($post_data, $social_post);     
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
        // $this->load->library('Socializer/Socializer');
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
            unset($source, $user_id, $profile_id, $token, $keyword, $dbPosts, $campaign, $campaignPosts, $post);
            return $_post;
        }   
        unset($source, $user_id, $profile_id, $token, $keyword, $dbPosts, $campaign, $campaignPosts, $post);
        return false;      
    }

    public function getPost($campaign){
        $campaign = $campaign->to_array();
        $sources = json_decode($campaign['sources']);
        $keywords = json_decode($campaign['keywords']);
        $counter = 0;

        $affiliate_model = new affiliates_model();
        $common_settings = $affiliate_model->get_affiliate_info($campaign['user_id'], $campaign['profile_id']);        
        //ddd($common_settings);
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
            if($counter > 5){
                break; 
            }            
            $counter++;
            //$source = 'amazon';
            $source = $sources[array_rand($sources)];
            $keyword = $keywords[array_rand($keywords)]; 
            
            //d($keyword, $source);
            //d('buscando en: '.$source.' intento#'.$counter, microtime());
            $dbPosts = $campaignPosts->get_user_posts($campaign['user_id'], $source, $campaign['profile_id']);
            
            if(in_array($source, $socials)){
                if(!empty($tokens[$source])){
                    $post = $this->getSocialPost($source, $campaign['user_id'], $campaign['profile_id'], $tokens[$source], $keyword, $dbPosts, $campaign);
                }
            }
            if(in_array($source, $affiliates)){
                $post = $this->getAffiliatePost($source, $campaign['user_id'], $campaign['profile_id'], $common_settings, $keyword, $dbPosts, $campaign);
            }
            if($source == 'giphy'){
                $post = $this->getGiphyPost($keyword, $campaign['user_id'], $campaign['profile_id'], $dbPosts, $campaign);
            }
            
            unset($dbPosts);
        }
        unset($campaignPosts, $token, $group, $affiliate_model);
        //d('se obtuvo un post buscando en: '.$source.' intento#'.$counter, microtime());
        return $post;
 
    }
    public function getAffiliatePost($source, $user_id, $profile_id ,$tokens, $keyword, $dbPosts, $campaign){
        $_post = false;
        //d($tokens);
        //d('entrando en '.$source, microtime());
        if($source == 'amazon' && !empty($tokens['amazon'])){
            //ddd($tokens['amazon']);
            $amazon = $this->load->library('amazon', $tokens['amazon']);  
            $post = $amazon->amazon_filter($keyword, $amazon, $dbPosts);
            if(!empty($post)){
                $_post = $this->buildPost($campaign, $post, 'amazon');
                $campaignPosts = new Campaign_posts;
                $campaignPosts->add_new_post($user_id, $post->source_id, $profile_id, 'amazon');
            }                        
        }                             
        if($source == 'ebay' && !empty($tokens['ebay'])){
            $ebay = $this->load->library('ebay', $tokens['ebay']);
            $post = $ebay->ebay_filter($keyword, $ebay, $dbPosts);
            if(!empty($post)){
                $_post = $this->buildPost($campaign, $post, 'ebay');
                $campaignPosts = new Campaign_posts;
                $campaignPosts->add_new_post($user_id, $post->source_id, $profile_id, 'ebay');
            } 
            //d('Saliendo de '.$source, microtime());
        }
        unset($source, $campaign, $profile_id ,$tokens, $keyword, $campaignPost, $post, $ebay, $amazon);
        return $_post;
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
            unset($keyword, $user_id, $profile_id, $dbPosts, $campaign, $post, $giphy);
            return $_post;
        }   
        unset($keyword, $user_id, $profile_id, $dbPosts, $campaign, $post, $giphy);
        return false;        
    }
    public function buildPost($campaign, $data, $source){    
        if($source == "youtube" || $source == "giphy"){
            $post['post_to_socials'] = array("twitter");
            $post['post_to_groups'] = array($campaign['profile_id']);
            $post['type'] = 'social';
            if(!empty($campaign['url'])){
                $urls = json_decode($campaign['url']);
                $url = $urls[array_rand($urls)]; 
                $post['url'] = $this->customShorten($url);
            }
            if($source == 'giphy'){
                if(empty($post['url'])){
                    $post['description'] =  $data->bitly_gif_url." via @giphy";
                } else {
                    $post['description'] = $post['url'];
                    $post['url'] = $data->bitly_gif_url;
                }
                
                $post['campaign_data'] = serialize(array('embed_url' => $data->embed_url, 'source_id' => $data->id, 'source' => $source));
            }
            if($source == 'youtube'){
                if(empty($post['url'])){
                    $post['description'] = "https://www.youtube.com/watch?v=".$data->source_id;
                } else {
                    $post['description'] = $post['url'];
                    $post['url'] = "https://www.youtube.com/watch?v=".$data->source_id;
                }                
                
                $post['campaign_data'] = serialize(array('source_id' => $data->source_id, 'source' => $source));
            }            
            return $post;            
        }
        if($source == "facebook" || $source == "instagram"){
            //we are only auto posting on twitter
            $post['post_to_socials'] = array("twitter");
            $post['post_to_groups'] = array($campaign['profile_id']);
            $post['description'] = substr($data->data[0]->description, 0, 100);
            $post['type'] = 'social';
            if(!empty($campaign['url'])){
                $urls = json_decode($campaign['url']);
                $url = $urls[array_rand($urls)]; 
                $post['url'] = $this->customShorten($url);
            }            

            try {
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
                $post['image_name'] = $nameImage;                
            } catch (Exception $e) {
                unset($post['image_name']);
                return $post;
            } 
            return $post;            
        }
        if($data->source == "amazon" || $data->source == "ebay"){
            $post['post_to_socials'] = array("twitter");
            $post['post_to_groups'] = array($campaign['profile_id']);
            $post['description'] = substr($data->description, 0, 80);
            $post['url'] = $this->customShorten($data->url);
            $post['type'] = 'affiliate';
            $nameImage = time() . '.png';
            $post['campaign_data'] = serialize(array('source_id' => $data->source_id, 'source' => $source, 'media_url' =>$data->media_url, 'image_name' => $nameImage));
            $url = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/uploads/' . $campaign['user_id'] . '/';
            if(!is_dir($url)) {
                mkdir($url, 0755, TRUE);
                chown($url, "autosoci");
                chgrp($url, "autosoci");
            }
            try {
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
            } catch (Exception $e) {
                unset($post['image_name']);
                return $post; 
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
            d($url);
            //throw new Exception($data->message, 1);
            return null;
        }
    }      
}
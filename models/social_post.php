<?php

/**
 * Class Social_post
 *
 * @property integer    $id
 * @property string     $description
 * @property string     $posting_type
 * @property integer    $user_id
 * @property string     $url
 * @property integer    $schedule_date
 * @property integer    $category_id
 * @property string     $timezone
 * @property string     $title
 * @property string     $post_to_groups
 * @property string     $post_to_socials
 * @property integer    $profile_id
 * @property integer    $post_cron_id
 */
class Social_post extends DataMapper {

    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $error_prefix = '<span class="message-error">';
    var $error_suffix = '</span>';
    
    public static $socials = array(
        'twitter',
        'facebook',
        'youtube',
        // 'pinterest',
        //'instagram'
//        'google'
    );
    
    var $validation = array(
        // 'description' => array(
        //     'label' => 'Description',
        //     'rules' => array('required', 'trim'),
        // ),
        'posting_type' => array(
            'label' => 'Posting type',
            'rules' => array('required')
        )
    );

    var $table = 'social_posts';

    var $has_one = array(
        'social_posts_category' => array(
            'model' => 'social_posts_category',
            'join_other_as' => 'category',
        ),
        'social_post_cron' => array(
            'model' => 'social_post_cron',
            'join_other_as' => 'post_cron',
        )
    );

    var $has_many = array(
        'media' => array(
            'class' => 'media',
            'join_self_as' => 'post',
            'join_other_as' => 'media',
            'join_table' => 'posts_media'
        ),
    );


    function __construct($id = NULL) {
        parent::__construct($id);
       
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public static function getActiveSocials($profile_id) {
        $_socials = self::$socials;
        foreach($_socials as $key => $_social) {
            if(!Social_group::hasSocialAccountByType($profile_id, $_social)) {
                unset($_socials[$key]);
            }
        }
        return $_socials;
    }

    /**
     * Validate a form
     * Check Description / URL / post type and social networks fields
     *
     * @access public
     * @param $feeds
     * @return array
     */
    public static function validate_post($feeds) {

        $errors = array();
        
        //$category_slug = $feeds['attachment_type'];
        $category_slug = null;
        $feeds['title'] = 'from '.clear_domain(site_url());
        $post = new self;
        $post->from_array($feeds, array('description', 'url', 'posting_type', 'category_id'));
        if(!$post->posting_type) {
            $post->posting_type = 'immediate';
        }
        $post->validate();
        if ( ! $post->valid) {
            foreach($post->error->all as $err_key => $err_value) {
                $errors[ $err_key ] = $err_value;
            }
        }

        if(mb_strlen($feeds['description']) < 1){
            $errors['description'] = "This can't be empty";
        }

        // if( isset($feeds['url']) ) {
        //     if (!empty($feeds['url'])) {
        //         if (filter_var($feeds['url'], FILTER_VALIDATE_URL) === false ||
        //             strstr($feeds['url'], '.') === false
        //         ) {
        //             $errors['url'] = lang('email_error');
        //         }
        //     }
        // }

        if(!isset($feeds['post_to_socials'])) {
            $errors[ 'post_to_groups[]' ] = lang('socials_error');
        }

        if(isset($feeds['posting_type'])) {
            //validate Schedule date
            if($feeds['posting_type'] != 'immediate') {
                $scheduled_errors = self::_validate_scheduled_data( $feeds );
                $errors = array_merge($errors, $scheduled_errors);
            }
        }

        if( $category_slug == 'photos' ) {
            if( empty($feeds['image_name']) ) {
                $errors['image_name'] = lang('image_error');
            }
        }

        if(!empty($feeds['post_to_socials']) && in_array('twitter', $feeds['post_to_socials'])){
            $twitter_limit = array(
                1 => 140,
                2 => 117,
                3 => 94
            );
            $input_category = 1;

            $file = (!empty($feeds['image_name']) || isset($feeds['file_name']) );
            $link = !empty($feeds['url']);

            if($file && $link){
                $input_category = 3;
            }elseif($file || $link){
                $input_category = 2;
            }
            if(mb_strlen($feeds['description']) > $twitter_limit[$input_category]){
                $errors['description'] = lang('twitter_error');
            }

        }
        if(!empty($feeds['post_to_socials']) && in_array('linkedin', $feeds['post_to_socials'])){
           if(mb_strlen($feeds['description']) > 400){
                $errors['description'] = lang('linkedin_error');
           }
         }
        if( $category_slug == 'videos' ) {
            $video_errors = self::_validate_video( $feeds );
            $errors = array_merge($errors, $video_errors);
        } else {
            if(empty($feeds['url']) && isset($feeds['post_to_socials']) && in_array('linkedin', $feeds['post_to_socials'])){
                $errors['url'] = lang('url_error');
            }
        }
        return $errors;
    }

    private function _validate_video( $feeds ) {

        $errors = array();
        if( empty($feeds['image_name']) ) {
            $errors['image_name'] = lang('video_error');
        }
        
        if(isset($feeds['title'])) {
            if(empty($feeds['title'])) {
                $errors['title'] = lang('video_title_error');
            }
        } else {
            
            $errors['title'] = lang('video_title_error');
        }

        $youtube_token = Access_token::inst()->get_youtube_token($feeds['user_id']);
        foreach($feeds['post_to_groups'] as $group_id) {
            if(Social_group::hasSocialAccountByType($group_id, 'twitter')
                || Social_group::hasSocialAccountByType($group_id, 'linkedin')) {
                if( !$youtube_token->exists() ) {
                    $errors['post_to_groups[]'] = lang('youtube_error');
                    break;
                }
            }
        }

        return $errors;
    }

    private function _validate_scheduled_data ( $feeds ) {
        $errors = array();

        if(isset($feeds['schedule_date'])) {
            if(self::_get_schedule_date($feeds) < strtotime('now')) {
                // $errors['schedule_date'] = '<span class="message-error">Time cant be less current date. </span>';
            }
        } else {
            $errors['schedule_date'] = lang('schedule_error');
        }

        return $errors;
    }

    public static function checkPostByDescription($description) {
        $post = new self;
        $post->where(['description' => $description])->get(1);
        return $post->exists();
    }


    /**
     * Insert post data to database
     *
     * @access public
     * @param $feeds
     * @param $user_id
     */
    public static function add_new_post($feeds, $user_id, $profile_id) {
        $post = isset($feeds['post_id']) ? new self((int)$feeds['post_id']) : new self;
        $post->from_array($feeds, array('description', 'posting_type'));
        $post->url = isset($feeds['url']) ? $feeds['url'] : '';

        $post->post_to_groups = serialize($feeds['post_to_groups']);
        $post->post_to_socials = serialize($feeds['post_to_socials']);
        $post->user_id = $user_id;
        $post->profile_id = $profile_id;
        $post->category_id = isset($feeds['category_id']) ? (int)$feeds['category_id'] : 0;

        if($feeds['posting_type'] != 'schedule') {
            if(isset($feeds['post_id'])) {
                $post->delete();
            }
            self::_send_to_social($feeds, $user_id);
        } else {
            $post->schedule_date = self::_get_schedule_date($feeds);
            $post->timezone = $feeds['timezone'];
        }

        if(isset($feeds['image_name'])) {
            if(!empty($feeds['image_name'])) {
                self::_save_attachment($post, $feeds, $user_id);
            }
        }

        $post->save();
    }

    public function getScheduledDate($format) {
        $date = new DateTime('UTC');
        $date->setTimestamp($this->schedule_date);
        $date->setTimezone(new DateTimeZone($this->timezone));
        return $date->format($format);
    }

    public static function post_video($feeds, $user_id, $profile_id) {

        if($feeds['posting_type'] != 'schedule') {
            if(isset($feeds['post_id'])) {
                $post = Social_post::inst((int)$feeds['post_id']);
                $post->delete();
            }
            self::_send_video_to_socials( $feeds, $user_id );
        } else {
            $post = isset($feeds['post_id']) ? new self((int)$feeds['post_id']) : new self;
            $post->from_array($feeds, array('description', 'posting_type'));

            $post->post_to_groups = serialize($feeds['post_to_groups']);
            $post->post_to_socials = serialize($feeds['post_to_socials']);

            $post->user_id = $user_id;
            $post->profile_id = $profile_id;
            $post->title = 'from '.clear_domain(site_url());;
            $post->category_id = isset($feeds['category_id']) ? (int)$feeds['category_id'] : 0;
            $post->schedule_date = self::_get_schedule_date($feeds);
            $post->timezone = $feeds['timezone'];
            self::_save_attachment($post, $feeds, $user_id);
            $post->save();
        }
    }

    public function get_user_scheduled_posts( $user_id, $profile_id, $page = 1, $offset, $category ) {
        $where = array(
            'user_id' => $user_id,
            'profile_id' => $profile_id,
            'schedule_date !=' => 'null',
            'type' => '',
            'posting_type' => 'schedule'
        );

        if($category != 'all') {
            $where['category_id'] = $category; //filter by category
        }

        $posts = $this->where($where)
            ->order_by('schedule_date', 'ASC')
            ->get_paged($page, $offset);
        return $posts;
    }

    /**
     * Used to Send data to social
     * Send message and bit.ly-formed URL
     *
     * @access private
     * @param $post
     * @param $user_id
     * @throws Exception
     */
    public function _send_to_social( $post, $user_id ) {
        $post['url'] = isset($post['url']) ? $post['url'] : '';
        $inTwitter = in_array('twitter', $post['post_to_socials']);
        $inFacebook = in_array('facebook', $post['post_to_socials']);
        $inLinkedin = in_array('linkedin', $post['post_to_socials']);
        $inPinterest = in_array('pinterest', $post['post_to_socials']);
        $inInstagram = in_array('instagram', $post['post_to_socials']);

        $session_errors = array();

        foreach ($post['post_to_groups'] as $group_id) {
            $group = new Social_group($group_id);
            foreach ($group->access_token->get()->all_to_array() as $access_token) {

                if($access_token['type'] == 'facebook' && $inFacebook) {
                    // if(isset($post['image_name'])) {
                    //     if(empty($post['image_name'])) {
                    //         unset($post['image_name']);
                    //     }
                    // }
                    // if(isset($post['url'])) {
                    //     if(empty($post['url'])) {
                    //         unset($post['url']);
                    //     }
                    // }

                    $result = self::_send_to_facebook($post, $user_id, $access_token);
                    // var_dump($result, $result->result['post_id']);
                    if (!empty($result->errors)) {
                        $session_errors[] = 'Facebook: '.$result->errors->message;
                    } else {  
                        if(!empty($result->result['post_id'])){
                            $id = $result->result['post_id'];
                        }  else {
                            $id = $result->result['id'];
                        }   
                        Social_sent_post::addPost(
                            $id,
                            $access_token['id']
                        );                                                          
                        Social_analytics::updateAnalytics(
                            $access_token['id'],
                            Social_analytics::FACEBOOK_ANALYTICS_TYPE,
                            1
                        ); 
                    }                   
                }
                //echo $access_token['type'];
                if($access_token['type'] == 'pinterest' && $inPinterest) {
                    /* @var Socializer_Linkedin $linkedin */
                    $pinterest = Socializer::factory('pinterest', $user_id, $access_token);
                    $pinterest_len = strlen($post['description']);
                    if($pinterest_len > $pinterest::MAX_DESCRIPTION_LENGTH){
                        $post['description'] = substr($post['description'], 0, $pinterest::MAX_DESCRIPTION_LENGTH) ;
                    }
                    $selected_board = Pinterest_board::inst()->get_selected_board($user_id, $access_token['id']);
                    //var_dump($post, $selected_board->to_array()); die();
                    $result = $pinterest->createPost($post, $selected_board->to_array());
                    //var_dump($result->__get('id'), 'pinterest');
                    if (!empty($result->errors)) {
                        //throw new Exception('Pinterest: '.$result->errors->message);
                        $session_errors[] = 'Pinterest: '.$result->errors;
                    } else {
                        Social_sent_post::addPost(
                            $result->__get('id'),
                            $access_token['id']
                        );                        
                        Social_analytics::updateAnalytics(
                            $access_token['id'],
                            Social_analytics::PINTEREST_ANALYTICS_TYPE,
                            1
                        ); 
                    }                   
                }

                if($access_token['type'] == 'instagram' && $inInstagram) {
                    /* @var Socializer_Linkedin $linkedin */
                    $instagram = Socializer::factory('instagram', $user_id, $access_token);
                    $instagram_len = strlen($post['description']);
                    if($instagram_len > $instagram::MAX_DESCRIPTION_LENGTH){
                        $post['description'] = substr($post['description'], 0, $instagram::MAX_DESCRIPTION_LENGTH) ;
                    }
                    $result = $instagram->createPost($post);
                    if (!empty($result->errors)) {
                        //throw new Exception('Instagram: '.$result->errors->message);
                        $session_errors[] = 'Instagram: '.$result->errors->message;
                    } else {
                        Social_analytics::updateAnalytics(
                            $access_token['id'],
                            Social_analytics::IGPOSTS_ANALYTICS_TYPE,
                            1
                        );  
                    }                   
                }
                if($access_token['type'] == 'twitter' && $inTwitter) {
                    /* @var Socializer_Twitter $twitter */
                    $twitter = Socializer::factory('Twitter', $user_id, $access_token );
                    $tweet_len = strlen($post['url']) + strlen($post['description']);
                    if($tweet_len > $twitter::MAX_TWEET_LENGTH) {
                        $message = substr($post['description'], 0, $twitter::MAX_TWEET_LENGTH - strlen($post['url']) - 1) . ' ' . $post['url'];
                    } else {
                        $message = $post['description'] . ' ' . $post['url'];
                    }
            
                    if (empty($post['image_name']) || $post['type'] == "trivia" ) {
                        $result = $twitter->tweet($message, null);
                    } else {
                        if(preg_match("/\.gif$/", $post['image_name'])){
                            $mime = 'image/gif';
                        } elseif(preg_match("/\.mp4$/", $post['image_name'])){
                            $mime = 'video/mp4';
                        } else {
                            $mime = 'image/png';
                        }                          
                        $result = $twitter->tweet_with_image($message, $post['image_name'], $mime);
                    }
                    if (!empty($result->errors)) {
                        //throw new Exception('Twitter: '.$result->errors[0]->message);
                        $session_errors[] = $result->errors[0]->message;
                    } else {
                        Social_sent_post::addPost(
                            $result->id,
                            $access_token['id']
                        );                        
                        Social_analytics::updateAnalytics(
                            $access_token['id'],
                            Social_analytics::TWITTER_ANALYTICS_TYPE,
                            1
                        );
                    }
                }
                if($access_token['type'] == 'linkedin' && $inLinkedin) {
                    /* @var Socializer_Linkedin $linkedin */
                    $linkedin = Socializer::factory('linkedin', $user_id, $access_token );
                    $linkedint_len = strlen($post['description']);
                    if($linkedint_len > $linkedin::MAX_DESCRIPTION_LENGTH){
                        $post['description'] = substr($post['description'], 0, $linkedin::MAX_DESCRIPTION_LENGTH) ;
                    }
                    if($post['type'] == "trivia"){
                        $buffer = $post['image_name'];
                        $post['image_name'] = NULL;
                    }                      
                    //Social_post::_resize_image(__DIR__.'/../../public/uploads/'.$user_id.'/'.$post['image_name'], true);
                    $response = $linkedin->createPost($post);
                    //var_dump($linkedin->xmlToArray($response['linkedin']) , $linkedin->xmlToArray($response['linkedin'])['update']['children']['update-key']['content']);
                    if($post['type'] == "trivia"){
                        $post['image_name'] = $buffer;
                    }                      

                    if(!$response['success']) {
                        $error = $linkedin->xmlToArray($response['linkedin']);
                        //throw new Exception('Linkedin: '.$error['error']['children']['message']['content']);
                        $session_errors[] = 'Linkedin: '.$error['error']['children']['message']['content'];
                    } else {
                        $content = $linkedin->xmlToArray($response['linkedin'])['update']['children']['update-key']['content'];
                        $content = explode("-", $content);
                        Social_sent_post::addPost(
                            $content[2],
                            $access_token['id']
                        );                         
                        Social_analytics::updateAnalytics(
                            $access_token['id'],
                            Social_analytics::LINKEDIN_ANALYTICS_TYPE,
                            1
                        );                         
                    }     
                }                
            }
        } 
        if(isset($post['image_name']) && (!isset($post['post_cron_id']) || !$post['post_cron_id'])) {
        //if(isset($post['image_name']) && !preg_match("/\.gif$/", $post['image_name']) && !$inLinkedin && !$inTwitter && (!isset($post['post_cron_id']) || !$post['post_cron_id'])) {
            self::drop_attachment(__DIR__.'/../../public/uploads/'.$user_id.'/'.$post['image_name']);
        }
        if(count($session_errors) > 0){
             $message = implode(", ",$session_errors);
            //$message .= ". Posting to Twitter Failed.";
            if(isset($post['image_name'])) {
                self::drop_attachment(__DIR__.'/../../public/uploads/'.$user_id.'/'.$post['image_name']);
            }            
            throw new Exception($message, 1);
        }        
    }

    public function _send_video_to_socials( $post, $user_id ) {
        $inTwitter = in_array('twitter', $post['post_to_socials']);
        $inFacebook = in_array('facebook', $post['post_to_socials']);
        $inLinkedin = in_array('linkedin', $post['post_to_socials']);
        foreach ($post['post_to_groups'] as $group_id) {
            $group = new Social_group($group_id);
            $video = '';
            foreach ($group->access_token->get()->all_to_array() as $access_token) {
                if($access_token['type'] == 'twitter' && $inTwitter) {
                    self::_send_to_social($post, $user_id);
                } elseif($access_token['type'] == 'linkedin' && $inLinkedin) {
                    if (!$video) {
                        /* @var Socializer_Google $twitter */
                        $youtube_uploader = Socializer::factory('Google', $user_id);
                        $video = $youtube_uploader->post_video(
                            $post['title'],
                            $post['description'],
                            $post['image_name']
                        );
                    }
                    /* @var Socializer_Linkedin $linkedin */
                    $linkedin = Socializer::factory('Linkedin', $user_id, $access_token);
                    $data = array(
                        'title' => $post['title'],
                        'description' => $post['description'],
                        'url' => ' http://www.youtube.com/watch?v='. $video['id'],
                    );
                    $linkedin->createPost($data);
                } elseif($access_token['type'] == 'facebook' && $inFacebook) {
                    /* @var Socializer_Facebook $facebook */
                    $facebook = Socializer::factory('Facebook', $user_id, $access_token);
                    $facebook->post_with_video(
                        $post['title'],
                        $post['description'],
                        $post['image_name']
                    );
                }
            }
        }
        if(isset($post['image_name'])) {
            self::drop_attachment(__DIR__.'/../../public/uploads/'.$user_id.'/'.$post['image_name']);
        } 
    }

    private static function _resize_image($filename, $linkedin = false){
        // Get new dimensions

        list($width, $height) = getimagesize($filename);
        if(!$linkedin){
            $ratio = 4000000/ filesize($filename);
            $multipler = sqrt($ratio);
        } else {
            if($width > 360){
                $multipler = 360/$width;
            } else {
                $multipler = 1;
            }
        }
        $new_width = $width * $multipler;
        $new_height = $height * $multipler;    

        // Resample
        $image_p = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefrompng($filename);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
        imagepng($image_p, $filename,0); 
        imagedestroy($image_p);  
    } 

    /**
     * Send post to Facebook
     *
     * @param $post
     * @param $user_id
     * @param $access_token
     */
    private function _send_to_facebook($post, $user_id, $access_token) {
        $facebook = Socializer::factory('Facebook', $user_id, $access_token);
        try{    
            if(isset($post['url'])) {
                if(isset($post['image_name'])) {
                    if(preg_match("/\.gif$/", $post['image_name'])){
                        $description = $post['description']." ".$post['url'];
                    } else{
                        $description = $post['description'];    
                    }                    
                    $url_description = empty($post['url_text'])?null:$post['url_text'];
                    $result = $facebook->post_with_picture($description, $post['image_name'], $post['url'], $url_description);
                } else {
                    $result = $facebook->post($post['description'], $post['url']);
                }
            } else {
                if(isset($post['image_name'])) {
                    $result = $facebook->post_with_picture($post['description'], $post['image_name'], null, null);
                } else {
                    $result = $facebook->post($post['description'], null);
                }
            }
        // } catch (Exception $e){
        //     //throw new Exception('Facebook: '.$e->getMessage());
        //     return (object) array("errors" => true, "message" =>  $e->getMessage());
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $error_code = $e->getSubErrorCode();
            if(in_array($error_code, $facebook->authentication_errors)){
                //pause all campaigns
                User_notification::setNotification($user_id, User_notification::LOGIN_FACEBOOK, true);
                //need to create modal for this
            }
            //need to catch error code for double posting and spam

            return (object) array("errors" => true, "message" =>  $e->getMessage());
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            //throw new Exception($e->getMessage(), 1);
            //throw new Exception(serialize($e->getResponseData()), 1);
            return (object) array("errors" => true, "message" =>  $e->getMessage());
        } 
        return (object) array("errors" => false, "result" =>  $result);         
    }

    private function _save_attachment($post, $feeds, $user_id) {
        $category_slug = $feeds['attachment_type'];
        $media = new Media();
        $media->path = __DIR__.'/../../public/uploads/'.$user_id.'/'.$feeds['image_name'];
        if($category_slug == 'videos') {
            $media->type = 'video';
        } else {
            $media->type = 'image';
        }
        $media->user_id  = $user_id;
        $media->save();
        $post->save($media, 'media');
    }

    /**
     * Create UTC-formed date from time passed by user
     *
     * @access private
     * @param $post
     * @return int
     */
    private function _get_schedule_date($post) {
        $date = new DateTime($post['schedule_date'].' '.$post['timezone']);
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->getTimestamp();
    }


    public function drop_attachment($path) {
        unlink($path);
    }


    /**
     * Delete scheduled post from list
     *
     * @access public
     *
     * @param $post_id
     * @param $user_id
     *
     * @return bool
     */
    public function delete_scheduled( $post_id, $user_id ) {
        $post = $this->where(array('id' => $post_id, 'user_id' => $user_id, 'posting_type' => 'schedule'))
            ->get();
        if( $post->result_count() > 0 ) {
           return $post->delete();
        } else {
            return true;
        }
    }

    /**
     * Check if post has media
     *
     * @return Media||null
     */
    public function isMediaPost()
    {
        $media = $this->media->get();

        return ($media->id) ? $media : null;
    }

    /**
     * @return array
     */
    public function getPostToGroups() {
        return unserialize($this->post_to_groups);
    }
}

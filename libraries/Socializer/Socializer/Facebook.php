<?php defined('BASEPATH') or die('No direct script access.');

require_once dirname(dirname(__FILE__)).'/vendors/facebook-sdk/src/facebook.php';

class Socializer_Facebook
{
    private $_facebook;
    private $_ci;
    private $_config;
    private $_user_id;
    private $_profile_id;
    private $_fanpage_id;
    public $authentication_errors;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;

    /**
     * Construct function
     * Sets the codeigniter instance variable and loads the lang file
     * @param integer|null $user_id
     * @param array|null $token
     */
    function __construct($user_id, $token) {
        $this->authentication_errors = array(458, 459, 460, 463, 464, 467);
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');
        $this->_config = Api_key::build_config('facebook', $this->_ci->config->item('facebook'));
        $this->_user_id = $user_id;
        $this->_facebook = new Facebook($this->_config);
        $this->_new_facebook = new \Facebook\Facebook([
          'app_id' => $this->_config['appId'],
          'app_secret' => $this->_config['secret'],
          'default_graph_version' => 'v2.8',
        ]);
        //ddd($this->_new_facebook->getDefaultGraphVersion());
        //ddd($this->_new_facebook);
        //ddd($this->_new_facebook, $this->_facebook);
        $this->_token = $token;

        if(isset($this->_token['id'])) {
            $this->_facebook->destroySession();
            $this->_facebook->setAccessToken($this->_token['token1']);
            $page = Facebook_Fanpage::inst()->get_selected_page($this->_user_id, $this->_token['id']);
            if($page->id) {
                $this->_fanpage_id = $page->fanpage_id;
                $this->_profile_id = $page->profile_id;
            }
            $this->_new_facebook->setDefaultAccessToken($this->_token['token1']);
        }

    }

    /**
     * Get username from profile link
     *
     * @param $link
     * @return mixed
     */
    public function getUserFromLink($link)
    {
        $parts = explode('/', $link);

        return  $parts[count($parts)-1];
    }

    /**
     * Get posts by user id
     *
     * @param $userId
     * @return mixed
     */
    public function getUserPosts($userId)
    {
        $result = $this->_new_facebook->get('/'.$userId.'/posts');
        return  $result->getDecodedBody();
    }

    /**
     * Called in methods where fan page id is used
     * @throws Exception
     */
    protected function check_fanpage() {
        if ( ! $this->_fanpage_id) {
            $message = 'Facebook fan page not selected.';
            if( ! $this->_ci->input->is_cli_request()) {
                $message .= '<a class="configure-fblink" href="' 
                    . site_url('settings/socialmedia/edit_account/'.$this->_token['id']) . '">Do it now</a>.';
            }
            throw new Exception($message, Socializer::FBERRCODE);
        }
    }

    /**
     * Used to get facebook access token
     * Use Facebook SDK API library
     * After - return redirect url
     *
     * @access public
     *
     * @param $profile_id
     *
     * @return string
     */
    public function add_new_account($profile_id) {
        $helper = $this->_new_facebook->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();
        if($accessToken) {

            $profile = $this->get_profile($accessToken->getValue());
            $picture = $this->get_profile_picture($profile['id']);
            $access_token = new Access_token();
            $tokens = array(
                'token' => $accessToken->getValue(),
                'secret_token' => null,
                'image' => $picture,
                'name' => $profile['name'],
                'username' => $profile['id']
            );
            $token = $access_token->add_token($tokens, 'facebook', $this->_user_id, $profile_id);

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));
            $redirect_url = site_url('settings/socialmedia');
            //$redirect_url = site_url('settings/socialmedia/edit_account/'.$token->id);
        } else {
            $redirect_url = site_url('settings/socialmedia');
        }
        return $redirect_url;
    }
    public function getRedirectUrl(){
        $permissions = array();
        $callback = $this->_config['redirect_uri'];
        $helper = $this->_new_facebook->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($callback, $permissions);        
        return $loginUrl;
    }

    /**
     * Used to get facebook access token
     * Use Facebook SDK API library
     * After - return redirect url
     *
     * @access public
     *
     * @return string
     */
    public function sign_up() {
        $permissions = array('manage_pages', 'user_videos', 'user_likes', 'publish_actions', 'publish_pages');
        $helper = $this->_new_facebook->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($this->_config['redirect_uri'], $permissions);  

        $accessToken = $helper->getAccessToken();
        if($accessToken) {
            $profile = $this->get_profile();
            return $profile;
        } else {
            redirect($login_url);
        }
    }

    /**
     * Used to get profile for user
     *
     * @access public
     * @return mixed
     */
    public function get_profile($access_token =null) {
        if(empty($access_token)){
            $this->_new_facebook->setDefaultAccessToken($this->_token['token1']);
        } else {
            $this->_new_facebook->setDefaultAccessToken($access_token);
        } 
        $result = $this->_new_facebook->get('/me');
        return $result->getDecodedBody();        

    }

    /**
     * Used to get array of user facebook fanpages
     * Array like : $item['id'] -- fanpage id in facebook
     * AND $item['name'] -- fanpage name at facebook
     *
     * @access public
     * @return array
     */
    public function get_user_pages() {
        $fb_pages = $this->_new_facebook->get('/me/accounts');
        $fb_pages = $fb_pages->getDecodedBody();
        $pages_data = array();
        $pages_counter = 0;
        foreach ($fb_pages['data'] as $_page) {
            $pages_data[$pages_counter]['name'] = $_page['name'];
            $pages_data[$pages_counter]['id'] = $_page['id'];
            $pages_counter++;
        }
        return $pages_data;
    }

    /**
     * Used to get page feed
     * Using Facebook graph API (FB SDK)
     *
     * @access public
     * @param $url
     * @return mixed
     */
    public function get_page_posts( $url = null ) {
        $this->check_fanpage();
        $this->_ci->load->config('facebook_settings');
        $limit = $this->_ci->config->item('facebook_posts_limit');
        if ($this->_fanpage_id) {
            $request_string = $url == null ? '/'.$this->_fanpage_id.'?fields=feed.limit('.$limit.')' : $url;
            $result = $this->_new_facebook->get($request_string);
            $page_feed =  $result->getDecodedBody();
        } else {
            $page_feed = null;
        }
        return $page_feed;
    }

    /**
     * Get fanapge id
     *
     * return string
     */
    public function getFanpageId()
    {
        return $this->_fanpage_id;
    }

    /**
     * Used to get all data for feed post
     * ( I use this to get ALL comments for post
     * Because get_page_posts return only 2 comments for post )
     *
     * @access public
     * @param $post_id
     * @return mixed
     */
    public function get_post_feed( $post_id ) {
        $result = $this->_new_facebook->get('/'.$post_id.'/comments');
        return $result->getDecodedBody();
    }

    /**
     * Get avatar to show it in comments-section
     * ( you can use profile ID or send 'me' to $id param )
     *
     * @param string $id 
     * @return string
     */
    public function get_profile_picture($id = null) {
        
        $result = '';
        if (!$id) {
            try {
                $profile = $this->get_profile();
            } catch (Exception $e) {
            }
            if (!empty($profile['id'])) {
                $id = $profile['id'];
            }
            
        }
        
        if ($id) {
            $result = 'http://graph.facebook.com/'.$id.'/picture?type=square';
        }
        
        return $result;
    }

    /**
     * Used to add a new comment for post
     *
     * @access public
     */
    public function comment( $post_id, $message ) {
        $comment = $this->_new_facebook->post('/'.$post_id.'/comments', array('message' => $message));
        $comment = $comment->getDecodedBody();
        $id = $this->_new_facebook->get('/'.$comment['id']);
        return $id->getDecodedBody();
    }

    /**
     * Used to add new post to facebook fanpage
     *
     * @access public
     * @param $message
     * @param $link
     * @return mixed
     */
    public function post($message, $link) {
        $this->_new_facebook->setDefaultAccessToken($this->_token['token1']);
        $this->check_fanpage();

        $token_request = $this->_new_facebook->get('/'.$this->_fanpage_id.'?fields=access_token');
        $token_request = $token_request->getDecodedBody();
        $token = $token_request['access_token'];
        //$this->_new_facebook->setAccessToken($token);

        $data = array(
            'message' => $message,
        );
        if( $link != null ) {
            $data['link'] = $link;
        }
        $post = $this->_new_facebook->post('/'.$this->_fanpage_id.'/feed', $data, $token);
        return $post->getDecodedBody();
    }

    /**
     * !!!!!!!!!!
     * @param       $query
     * @param array $params
     *
     * @return mixed
     */
    public function search_posts($query, $params = array()) {

        $params['q'] = $query;
        $params['type'] = 'posts';

        $search_posts = $this->_new_facebook->post('/search', $params);
        return $search_posts;
    }

    public function get($id){
        try {
            $this->_new_facebook->setDefaultAccessToken($this->_token['token1']);
            $result = $this->_new_facebook->get('/'.$id.'?fields=likes.limit(0).summary(true),comments.limit(0).summary(true)');
            $result = $result->getDecodedBody();
            $_response = new stdclass;
            $_response->like_count = $result['likes']['summary']['total_count'];
            $_response->comment_count = $result['comments']['summary']['total_count'];
            return $_response;           
        } catch (Exception $e) {
           return array(); 
        }
    }

    /**
     * Used to add a new post to Facebook page
     * With attachment (Picture)
     *
     * @access public
     * @param $message
     * @param $image_name
     * @param $link
     * @return mixed
     */
    public function post_with_picture($message, $image_name, $link = null, $url_description = null) {
        $this->check_fanpage();
        //$this->_facebook->setFileUploadSupport(true);
        $image_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$image_name;
        $token_request = $this->_new_facebook->get('/'.$this->_fanpage_id.'?fields=access_token');
        $token_request = $token_request->getDecodedBody();
        $token = $token_request['access_token'];

         $gif = false;
        if (preg_match("/\.gif$/", $image_name))
        { 
            $gif = true;
        }

        //$link = null;

        $data = array(
            'message' => $message,
        );

        $endpoint = 'me';

        if(!empty($gif)) {
            $endpoint = 'feed';
            $data = [
                'link' => 'http://app.socimattic.com/public/uploads/'.$this->_user_id.'/'.$image_name,
                'message' => $message
            ];
        }

        if($image_name != null  && empty($link) && empty($gif)) {
            $endpoint = 'me/photos';
            $data = [
                'source' => $this->_new_facebook->fileToUpload($image_path),
                'message' => $message
            ];
        }

        if($image_name != null  && !empty($link) && empty($gif)) {
            $endpoint = $this->_fanpage_id.'/feed';
            $data = [
                'thumbnail' => $this->_new_facebook->fileToUpload($image_path),
                'link' => $link,
                'message' => $message,
                'description' => $url_description
            ];
        }

        $post = $this->_new_facebook->post('/'.$endpoint, $data, $token);
        return $post->getDecodedBody();
    }

    public function post_with_video( $name, $description, $video_name ) {
        $this->check_fanpage();
        $video_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$video_name;

        $token_request = $this->_new_facebook->get('/'.$this->_fanpage_id.'?fields=access_token');
        $token = $token_request['access_token'];

        $data = [
            'source' => $this->_new_facebook->videoToUpload($video_path),
            'description' => $description
            //'title' => $name
        ];

        $uploaded  = $this->_new_facebook->post('/'.$this->_fanpage_id.'/videos', $data, $token);
        return $uploaded;
    }

    /**
     * Used to like something on facebook page
     *
     * @access public
     */
    public function like( $post_id ) {
        $like = $this->_new_facebook->post('/'.$post_id.'/likes', array());
        return $like->getDecodedBody();        
    }

    /**
     * Used to dislike something on facebook page
     *
     * @access public
     */
    public function dislike( $post_id ) {
        $like = $this->_new_facebook->delete('/'.$post_id.'/likes');
        return $like->getDecodedBody();        
    }

    /**
     * Used to delete some comment
     *
     * @access public
     * @param $comment_id
     * @return mixed
     */
    public function remove_comment( $comment_id ) {
        $like = $this->_new_facebook->delete('/'.$comment_id);
        return $like->getDecodedBody();  
    }

    /**
     * Get Page likes count
     * Using for Social Reports
     *
     * @access public
     * @return int
     */
    public function get_page_likes_count() {
        $this->check_fanpage();
        $data = $this->_facebook->api('/'.$this->_fanpage_id);
        return isset($data['likes']) ? $data['likes'] : 0;
    }

    /**
     * Check for 'is liked by me' for comments
     * Need to get more data from Facebook graph API
     *
     * @param $post
     * @return bool
     */
    public function is_liked_comment ( $post ) {
        $likes = $this->_facebook->api('/'.$post['id'].'/likes');
        return $this->is_liked_by_me(array('likes' => $likes));
    }

    /**
     * Used to check -- is user already like this post
     *
     * @access public
     * @param $post
     * @return bool
     */
    public function is_liked_by_me( $post ) {
        $this->check_fanpage();
        if(!isset($post['likes'])) {
            return false;
        }
        if(!isset($post['likes']['data'])) {
            return false;
        }
        $likes = $post['likes']['data'];
        foreach($likes as $_like) {
            if($_like['id'] == $this->_profile_id || $_like['id'] == $this->_fanpage_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Facebook comment time beautifier
     * Return comment create-time in format like : '12 hours ago'
     * or '12 of march on Facebook' or '10 minutes ago'
     *
     * @access public
     * @param $comment_time
     * @return string
     */
    public function convert_facebook_time( $comment_time ) {
        $diff = time() - strtotime($comment_time);
        $date = strtotime($comment_time);
        if($diff > 3600){
            if($diff < 86400){
                $diff = $diff/3600;
                return round($diff,0).' hours ago on Facebook';
            } else {
                return date('d F',$date).' on Facebook';
            }
        } else{
            $diff = $diff/60;
            return round($diff,0).' minutes ago on Facebook';
        }
    }
    
    /**
     * Transform message of Facebook API errors
     *
     * @param string $message
     * @return string   
     */
    public function facebookErrorTransformer($message)
    {
        $patternCode = '/^\(\#[0-9]*\)/';
        preg_match($patternCode, $message, $match);
        if (!empty($match[0])) {
            if ($match[0] == '(#210)') {
                $message = 'Wall of the user is closed';    
            } else {
                $message = str_replace($match[0], '', $message);
            }
        }
        
        return $message;
    }

    /**
     * Get friends count of user
     *
     * @param $userId
     *
     * @return int
     * @throws
     */
    public function getUserFriendsCount($userId)
    {
        if (empty($userId)) {
            throw InvalidArgumentException('FB user id can not be empty!');
        }

        $params = array(
            'method' => 'fql.query',
            'query' => "SELECT friend_count FROM user WHERE uid = ".$userId,
        );
        $data = $this->_facebook->api($params);

        return isset($data[0]['friend_count']) ? (int)$data[0]['friend_count'] : 0;
    }




    /**
     * Search fbpost by given keywords
     *
     * Returns fbpost that meet criteria: language, keyword,
     *  and containing media entities
     *
     * @param string $keyword to search
     * @param (object) $twitter instance of twitter search
     * @return object  post
     */
    function facebook_filter($keyword, $facebook, $db_posts){
        //log_message('TASK_DEBUG', __FUNCTION__ . ' > Facebook filter Started');
        $hook=1;
        $flag=0;
        $flag1=0;
        $nextPost =false;
        $nextPage =true;    
        $post =[];

        $page= $facebook->get_page_by_keyword(urlencode($keyword), '&limit=1');
        //log_message('TASK_DEBUG', __FUNCTION__ . ' > Results '.var_dump($page) );
        if(!empty($page)){
            //log_message('TASK_DEBUG', __FUNCTION__ . ' > Entered on first page');
            $post = json_decode($facebook->get_custom_page_posts('', $page["data"][0]["id"], '&limit=1'));
            //d($post);
            if(!empty($post)){
                $nextPage = false;
            }
            while($hook == 1){
                //if we have +5 post from one page lets move to the next
                if($flag1 > 6 || empty($post->data)){
                    $nextPage = true;
                    $nextPost = false;
                    $flag1= 0;
                }
                //get next page
                if($nextPage == true){
                    //log_message('TASK_DEBUG', __FUNCTION__ . ' > Moving to next page'. $page["paging"]["cursors"]["after"]);
                    $page = $facebook->get_page_by_keyword(urlencode($keyword),'&after='.$page["paging"]["cursors"]["after"]);
                    $nextPage = false;
                    $nextPost = false;
                }
                //get next post
                if($nextPost == true){
                    $post = json_decode($facebook->get_custom_page_posts($post->paging->next, ''));
                    $nextPost =false;
                    $nextPage =false;
                } else {
                    //get post
                    if(!empty($page["data"][0]["id"])){
                        $post = json_decode($facebook->get_custom_page_posts('', $page["data"][0]["id"], '&limit=1'));
                    } else {
                        $nextPage = true;
                    }                 
                }           

                // if there are post break loop
                if(!empty($post)){
                    //d('filter', $post->data[0]->source_id);
                    if($this->id_filter($post->data[0]->source_id, $db_posts)){
                        //unique post found
                        $hook = 0;
                    } else {
                        $nextPost =true;
                        $flag1++;
                    }
                } else {
                    $nextPage =true;
                }
                $flag++;
                // avoid endless loop
                if($flag > 15){
                    unset($post);
                    break;
                }
            }
            //d('returning post');
            return $post;
        }
    }  
    /**
     * Search for public Pages by Keyword
     *
     * Returns pages that meet criteria: language, keyword,
     *  and containing media entities
     *
     * @param string $keyword to search
     * @return object  post
     */      
    public function get_page_by_keyword($keyword, $since =''){

        $url= "/search?q=$keyword&type=page$since&fields=id%2Cname%2Cabout%2Clikes%2Ctalking_about_count%2Cpicture%7Burl%7D";        
        try{
            $page_feed = $this->_new_facebook->get($url);
            $page_feed = $page_feed->getDecodedBody(); 
            return $page_feed;            
        } catch (Exception $e){
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Error Getting Pages->' . $e->getMessage());
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Error Getting Pages->'. $url);
            return array();
        }

    }
 
    /**
     * Search for public posts by Keyword
     *
     * Returns pages that meet criteria: language, keyword,
     *  and containing media entities
     *
     * @param string $object_id
     * @return object  post
     */    
    public function get_custom_page_posts($since, $object_id = '', $limit='&limit=10'){
        //log_message('TASK_DEBUG', __FUNCTION__ . ' > OBJECT_ID '. $object_id. ' SINCE '.$since);
        if(empty($since)){
           $url= "/$object_id/posts?fields=message,name,full_picture,link,likes.summary(true),comments.summary(true),shares$limit";        
        } else {
            $since = str_replace('https://graph.facebook.com/v2.8/', '', $since);
            $url = $since;
        }
        try{
            $page_feed = $this->_new_facebook->get($url);
            $page_feed = $page_feed->getDecodedBody(); 
            $json = json_decode(json_encode($page_feed));
            $post = new stdClass;
            $object = new stdClass;
            foreach ($json->data as $value) {
                if (preg_match("/youtu.be/i", $value->link )){
                    continue;
                }
                $post = new stdClass;
                $post->source_id = $value->id;
                $post->source = 'facebook';

                $post->url = $value->url;

                $parsed= parse_url($value->full_picture);
                parse_str($parsed['query'], $query);
                //d($parsed, $query);
                $post->media_url =  $query['url']?$query['url']:$value->full_picture;
                $post->title = $value->name;
                $post->description = $value->message?$value->message:$value->title;
                if(empty($post->description)){
                    continue;
                }
                $custom = new stdClass;
                $custom->likes = $value->likes->summary->total_count;
                $custom->shares = $value->shares->count;
                $custom->comments =  $value->comments->summary->total_count;
                $post->custom = json_encode($custom);
            $posts[]  = $post;
            }
            $object->data = $posts;
            $object->paging  = $json->paging;        
            return json_encode($object);

        } catch (Exception $e){
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Error Getting Posts->' . $e->getMessage());
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Error Getting Posts->'. $url);
            return array();
        }   
    }
    protected function id_filter($id, $dbposts){
        foreach($dbposts as $_dbposts){
            $args = $_dbposts->to_array();  
            if($id == $args['source_id']){
                return false;
            }
        }
        return true;
    }       
}
<?php use Abraham\TwitterOAuth\TwitterOAuth;
defined('BASEPATH') or die('No direct script access.');

// require_once dirname(dirname(__FILE__)).'/vendors/twitteroauth/twitteroauth/twitteroauth.php';
// require_once dirname(dirname(__FILE__)).'/vendors/twitteroauth/twitteroauth/OAuth.php';
// require_once dirname(dirname(__FILE__)).'/vendors/twitteroauth/oauthdamnit.php';

class Socializer_Twitter
{

    const MAX_TWEET_LENGTH = 140;

    /**
     * CodeIgniter Core instance
     *
     * @var CI_Controller
     */
    private $_ci;

    /**
     * Twitter config from APPATH/config/social_credentials
     *
     * @var
     */
    private $_config;

    /**
     * Current user id
     *
     * @var
     */
    private $_user_id;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;

    private $_cache_dir;
    /**
     * @param integer|null $user_id
     * @param array|null $token
     */
    function __construct($user_id, $token) {
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');
        //global api setting
        //$this->_config = Api_key::build_config('twitter', $this->_ci->config->item('twitter'));
        $this->_config = Twitter_api_key::build_config($user_id, $this->_ci->config->item('twitter'));
        $this->_cache_dir = $this->_ci->config->item('twitter_cache_dir');
        $this->_user_id = $user_id;
        if (!$token) {
            $this->_token = Access_token::inst()->get_one_by_type('twitter', $this->_user_id)->to_array();
        } else {
            $this->_token = $token;
        }
    }

    /**
     * Used to set temporary credentials in Session
     * Get this credentials from Twitter API library
     *
     * @access public
     * @return string
     */
    public function set_temporary_credentials() {
        $connection = new TwitterOAuth($this->_config['consumer_key'], $this->_config['consumer_secret']);
        //$temporary_credentials = $connection->getRequestToken();
        $temporary_credentials = $connection->oauth('oauth/request_token', array('oauth_callback' => $this->_config['auth_callback']));
        
        //ddd($temporary_credentials);
        
        $this->_ci->session->set_userdata( 'oauth_token', $temporary_credentials['oauth_token'] );
        $this->_ci->session->set_userdata( 'oauth_token_secret', $temporary_credentials['oauth_token_secret'] );

        //$redirect_url = $connection->getAuthorizeURL($temporary_credentials);
        $redirect_url = $connection->url('oauth/authorize', array('oauth_token' => $temporary_credentials['oauth_token']));
        return $redirect_url;
    }

    /**
     * Used to add new record to Access Tokens Table
     *
     * @access public
     *
     * @param $oauth_verifier - $_REQUEST['code'] from controller
     * @param $profile_id
     *
     * @return string
     * @throws Exception
     */
    public function add_new_account($oauth_verifier, $profile_id) {

        $oauth_token = $this->_ci->session->userdata('oauth_token');
        $oauth_token_secret = $this->_ci->session->userdata('oauth_token_secret');

        $connection = new TwitterOAuth($this->_config['consumer_key'], $this->_config['consumer_secret'], $oauth_token, $oauth_token_secret);
        //$token_credentials = $connection->getAccessToken($oauth_verifier);
        $token_credentials = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier]);

        $tokens = array(
            'token' => $token_credentials['oauth_token'],
            'secret_token' => $token_credentials['oauth_token_secret']
        );

        try{

            if (empty($this->_user_id)) {
                throw new Exception("There in no active user to connect to twitter.");
            }
            $this->_token['token1'] = $token_credentials['oauth_token'];
            $this->_token['token2'] = $token_credentials['oauth_token_secret'];

            $connection = new TwitterOAuth($this->_config['consumer_key'], $this->_config['consumer_secret'], $this->_token['token1'] , $token_credentials['oauth_token_secret']);
            $exists = 0;
            if(!empty($token_credentials['screen_name'])){
                $access_token = new Access_token();
                $exists = $access_token->where('username', $token_credentials['screen_name'])->count();
            }
            if($exists > 0){
                throw new Exception("Twitter account already added", 1);
            }
            $tokens['username'] = $token_credentials['screen_name'];

            $socialFullInfo = $connection->get("account/verify_credentials");
            //ddd($socialFullInfo);
            if (empty($socialFullInfo->name)) {
                throw new Exception("Invalid twitter's user data. Please try to reconnect.");
            }
            $tokens['name'] = $socialFullInfo->name;
            $tokens['image'] = $socialFullInfo->profile_image_url_https;

            $access_token = new Access_token();
            $token = $access_token->add_token($tokens, 'twitter', $this->_user_id);

            if (!$token->exists()) {
                throw new Exception("Cant save twitter access data. Please try to reconnect.");
            }

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));

        } catch(Exception $e){
            throw $e;
        }

        $redirect_url = site_url('settings/socialmedia');
        return $redirect_url;
    }

    /**
     * Used to get user full info
     *
     * @access public
     * @param $username
     * @return mixed
     */
    public function get_user_full_info( $username = null, $id = null  ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );
        if(!empty($username)){
            $feed_params = array('screen_name' => $username);
        }  else {
            $feed_params = array('user_id' => $id);
        }    

        $user_feed = $connection->get('users/show', $feed_params);     
        return $user_feed; 
    }

    /**
     * Used to get user Tweets feed
     *
     * @access public
     * @param $limit - count of tweets to display
     * @param $page - number of tweets page
     * @return mixed
     */
    public function get_user_feed( $limit, $page ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $feed_params = array('count' => $limit, 'page' => $page);

        $user_feed = $connection->get('statuses/home_timeline', $feed_params);
        //$this->parseErrors($user_feed); 
        return $user_feed;
    }

    /**
     * Used to get mentions to user
     *
     * @access public
     * @param $limit - count of tweets to display
     * @param $page - number of tweets page
     * @return mixed
     */
    public function get_user_mentions( $limit, $page ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $feed_params = array('count' => $limit, 'page' => $page);

        $user_feed = $connection->get('statuses/mentions_timeline', $feed_params);   
        //$this->parseErrors($user_feed);   
        return $user_feed;          
    }

    /**
     * Used to get tweets created by user
     *
     * @access public
     * @param $limit - count of tweets to display
     * @param $page - number of tweets page
     * @return mixed
     */
    public function get_user_tweets( $limit, $page ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $feed_params = array('count' => $limit, 'page' => $page);

        $user_feed = $connection->get('statuses/user_timeline', $feed_params);   
        //$this->parseErrors($user_feed);  
        return $user_feed;   
    }

    public function get_single_tweet( $tweet_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $feed_params = array('id' => $tweet_id);

        $user_feed = $connection->get('statuses/lookup', $feed_params);  
        //$this->parseErrors($user_feed);    
        return $user_feed;   
    }

    /**
     * Used to get current user followers count
     *
     * @access public
     * @return int
     */
    public function get_followers_count() {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] ); 

        $profile_data = $connection->get('users/show', array('screen_name' => $this->_token['username']));

        return isset($profile_data->followers_count) ? $profile_data->followers_count : 0;
    }

    /**
     * Used to get current user followers
     *
     * @access public
     * @return mixed
     */
    public function get_followers() {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'], $this->_token['token1'], $this->_token['token2'] );
               
        $profile_data = $connection->get('followers/ids',
            array(
                'screen_name' => $this->_token['username']
            )
        );
        //$this->parseErrors($profile_data);             
        return $profile_data;
    }

    /**
     * Used to get current user friends
     *
     * @access public
     * @return mixed
     */
    public function get_friends() {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $feed_params = array('screen_name' => $this->_token['username']);

        $user_feed = $connection->get('friends/ids', $feed_params);     
        return $user_feed; 
    }

    // /**
    //  * Used to get tweets
    //  *
    //  * @access public
    //  * @param array $args
    //  *  *'username' => username in twitter
    //  *  *'user_id' => user_id in twitter
    //  *  'exclude_replies' => default: none
    //  *  'trim_user' => default: none
    //  *  'only_one' => set true if need only one tweet. Default: false
    //  *  'count' => count of tweets. Default: 5
    //  *  'criteriaAnd' => array(
    //  *       array(
    //  *          'param_name' => retweet_count, favorite_count, favorited, retweeted, created_at (timestamp)
    //  *          'comparison_sign' => >,<,=,>=,<=,!=, between. Default: =
    //  *          'value' => value to compare
    //  *       )
    //  *  ),
    //  * 'criteriaOr' => equal to criteriaAnd
    //  *
    //  * @see https://dev.twitter.com/rest/reference/get/statuses/user_timeline
    //  * @return array|string
    //  */
    public function get_tweets($args = array()) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );

        $argsForCall = array();
        if (isset($args['username'])) {
            $argsForCall['screen_name'] = $args['username'];
        } elseif (isset($args['user_id'])) {
            $argsForCall['user_id'] = $args['user_id'];
        } else {
            $argsForCall['screen_name'] = $this->_token['username'];
        }
        if (isset($args['trim_user'])) {
            $argsForCall['trim_user'] = $args['trim_user'];
        }
        if (isset($args['exclude_replies'])) {
            $argsForCall['exclude_replies'] = $args['exclude_replies'];
        }
        if (isset($args['count'])) {
            $argsForCall['count'] = $args['count'];
        } else {
            $argsForCall['count'] = 5;
        }
        $tweets = $connection->get('statuses/user_timeline', $argsForCall); 
        //$this->parseErrors($tweets); 
        if ($tweets->errors) {
            return 'Error: ' . $tweets->errors[0]->message . "\n\n";
        }
        $returnedData = array();
        foreach ($tweets as $tweet) {
            $isGood = true;
            foreach($args['criteriaAnd'] as $criteria) {
                if ($criteria['param_name'] != 'created_at') {
                    $param1 = $tweet->$criteria['param_name'];
                } else {
                    $date = new DateTime($tweet->$criteria['param_name']);
                    $param1 = $date->getTimestamp();
                }
                $isGood = $this->compare(
                    $param1,
                    $criteria['comparison_sign'],
                    $criteria['value']
                );
                if(!$isGood) {
                    break;
                }
            }
            if($isGood) {
                foreach ($args['criteriaOr'] as $criteria) {
                    if ($criteria['param_name'] != 'created_at') {
                        $param1 = $tweet->$criteria['param_name'];
                    } else {
                        $date = new DateTime($tweet->$criteria['param_name']);
                        $param1 = $date->getTimestamp();
                    }
                    $isGood = $this->compare(
                            $param1,
                            $criteria['comparison_sign'],
                            $criteria['value']
                    );
                }
                if($isGood) {
                    if(empty($args['id_only'])){
                        $returnedData[] = $tweet;
                    } else {
                        $returnedData = $tweet->id;
                    }
                    if(isset($args['only_one']) && $args['only_one'] == true) {
                        break;
                    }
                }
            }
        }
        return $returnedData;
    }

    // /**
    //  * @param $arg1
    //  * @param string $comparison_sign >,<,=,>=,<=,!=
    //  * @param $arg2
    //  * @return bool
    //  */
    private function compare($arg1, $comparison_sign, $arg2) {
        switch ($comparison_sign) {
            case '>':
                return $arg1 > $arg2;
                break;
            case '<':
                return $arg1 < $arg2;
                break;
            case '>=':
                return $arg1 >= $arg2;
                break;
            case '<=':
                return $arg1 <= $arg2;
                break;
            case '!=':
                return $arg1 != $arg2;
                break;
            case 'between':
                return  $arg2[0] <= $arg1 && $arg1 <= $arg2[1];
                break;
            default:
                return $arg1 == $arg2;
                break;
        }
    }


    // /**
    //  * Used to get user profile info
    //  *
    //  * @access public
    //  * @return mixed
    //  */
    private function get_user_info() {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );        

        $result = $connection->get('account/settings', $parameters);
        return $result;
    }

    /**
     * Used to create a new tweet
     *
     * @access public
     * @param $tweet_text
     * @param null|string $in_reply_to_status_id
     * @return mixed
     */
    public function tweet( $tweet_text, $in_reply_to_status_id = null ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );   
        if(empty($in_reply_to_status_id)){
            $parameters = array('status' => $tweet_text);
        } else {
            $parameters = array('status' => $tweet_text, 'in_reply_to_status_id' => $in_reply_to_status_id);
        }  

        $result = $connection->post('statuses/update', $parameters);
        //$this->parseErrors($result); 
        return $result;
    }

    // /**
    //  * Used to retweeted some tweet from user feed
    //  *
    //  * @access public
    //  * @param $tweet_id
    //  * @return mixed
    //  */
    public function retweet( $tweet_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );        
        $result = $connection->post('statuses/retweet/'.$tweet_id);
        //$this->parseErrors($result); 
        return $result;
    }

    // /**
    //  * Used to undo-retweet some tweet from user feed
    //  *
    //  * @access public
    //  * @param $tweet_id
    //  * @return mixed
    //  */
    public function undo_retweet( $tweet_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] ); 

        $retweet = $connection->get('statuses/retweet/'.$tweet_id, array('include_my_retweet' => 1));

        $my_retweet_id = $retweet[0]->id_str;
        return $connection->post('statuses/destroy/'.$my_retweet_id);
    }

    // /**
    //  * Used to unfollow someone who user follow
    //  *
    //  * @access public
    //  * @param $follower_id
    //  * @return mixed
    //  */
    public function unfollow( $follower_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );

        return $connection->post('friendships/destroy', array('user_id' => $follower_id));                    

    }

    // /**
    //  * Used to add some tweet from user feed to 'Favorites'
    //  *
    //  * @access public
    //  * @param $tweet_id
    //  * @return mixed
    //  */
    public function favorite( $tweet_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );        
        $favorite = $connection->post('favorites/create', array('id' => $tweet_id));
        //$this->parseErrors($favorite); 
        return $favorite;
    }

    // /**
    //  * Used to undo-Favorite tweet
    //  *
    //  * @access public
    //  * @param $tweet_id
    //  * @return mixed
    //  */
    public function undo_favorite( $tweet_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );        
        $favorite = $connection->post('favorites/destroy', array('id' => $tweet_id));
        return $favorite;
    }

    /**
     * Follow user
     *
     * @param array $args
     *  user_id - user id
     *  screen_name - user screen name
     *  text - message text
     *
     * @return mixed
     */
    public function direct_message($args)
    {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $direct_message = $connection->post('direct_messages/new', $args);
        //$this->parseErrors($direct_message); 
        return $direct_message;        
    }

    /**
     * Follow user
     *
     * @param $userId
     * @return mixed
     */
    public function follow($userId)
    {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );      
        $follow = $connection->post('friendships/create', array('user_id' => $userId, 'follow' => true));
        //$this->parseErrors($follow); 
        return $follow;
    }

    // /**
    //  * Used to remove tweet
    //  *
    //  * @access public
    //  * @param $tweet_id
    //  * @return mixed
    //  */
    public function remove_tweet( $tweet_id ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );        

        $removed = $connection->post('statuses/destroy'.$tweet_id);
        return $removed;
    }

    public function list_members($id){
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );        
        $parameter = array('list_id' => $id);
        $result = $connection->get('lists/members',$parameter);
        //$this->parseErrors($result); 
        return $result;                
    }

    public function getLists($ids, $number){
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );        
        $endpoint = 'lists/list';
        $result = array();

        for ($i=0; $i < 5; $i++) { 
            sleep(1);
            $id = $ids[$i]['follower_id'];
            $data = $connection->get('lists/list', array("user_id" => $id));
            $this->parseErrors($data);
            $data = $this->arrangeData($data);
            if(!empty($data)){
                if(is_array($data)){
                    foreach ($data as  $value) {
                        $result[] = $value;
                    }
                } else {
                    $result[] = $data;
                }
            }
            if(count($result) > $number+1){
                break;
            }
        }
        return $result;
    }

    private function arrangeData($data){
        foreach ($data as  $key => $value) {
            $data[$key]->show = true;
            unset($data[$key]->id_str);
            unset($data[$key]->uri);
            unset($data[$key]->slug);
            unset($data[$key]->full_name);
            unset($data[$key]->description);
            unset($data[$key]->created_at);
            unset($data[$key]->following);
            unset($data[$key]->user);            
            if($value->mode != 'public'){
                unset($data[$key]);
            }
            unset($data[$key]->mode);
        }         
        usort($data, function($a, $b)
        {
            return $a->subscriber_count - $b->subscriber_count;
        });
        $cuenta = count($data);
        if($cuenta > 3){
            $data = array_slice($data, 4-$cuenta, 4);
        }
        return $data;      
    }
    public function get($id){
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
        $this->_token['token1'], $this->_token['token2'] );        
        $result = $connection->get('statuses/show', array("id" => $id));
        if(!empty($result->errors[0])){
            if($result->errors[0]->code == 144){
                $sent = new social_sent_post();
                $sent->delete_by_source($id);
            }
        }
        $_response = new stdclass;
        $_response->retweet_count = $result->retweet_count;
        $_response->favourite_count = $result->favorite_count;
        return $_response;
    }


    /**
     * Used to upload image into Twitter
     * (attachment to text)
     *
     * @access public
     * @param $image_name
     * @param $status
     * @return mixed
     */
    public function tweet_with_image($status, $image_name, $type='image/png') {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );

        $path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$image_name;
        
        $result = $connection->upload('media/upload', array('media' => $path, 'media_type' => $type), true);

        if ( $connection->getLastHttpCode() != 201 ) {
            $result = new stdclass;
            $result->errors[0] = new stdclass;
            $result->errors[0]->message = "Failed to Upload Video to Twitter. Error Code: ".$connection->getLastHttpCode();
            return $result;
        }
        if ( !property_exists($result, 'media_id_string') ) {
            $result = new stdclass;
            $result->errors[0] = new stdclass;
            $result->errors[0]->message = "Failed to Upload Could not fetch Media";
            return $result;
        }
        $parameters = array('status' => $status, 'media_ids' => $result->media_id_string);
        $result = $connection->post('statuses/update', $parameters);

        if ( $connection->getLastHttpCode() != 200 ) {
            $result = new stdclass;
            $result->errors[0] = new stdclass;
            $result->errors[0]->message = "Failed. Error Code: ".$connection->getLastHttpCode();
            return $result;
        }
        //var_dump($result);
        return $result;
    }

    /**
     * !!!!!!!!!!
     * @param $query
     * @param array $params
     * @return mixed
     */
    public function search_tweets($query, $params = array()) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'], $this->_token['token1'], $this->_token['token2'] );
        $params['q'] = $query;
        $search_tweets = $connection->get('search/tweets', $params);
        //$this->parseErrors($search_tweets); 
        return $search_tweets;
    } 

    public function get_available_locations() {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'], $this->_token['token1'], $this->_token['token2'] );
        $data = $connection->get('trends/available');
        return $data;
    } 

    public function trends($country_id = 1, $number = 15 ) {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'], $this->_token['token1'], $this->_token['token2'] );
        $params['id'] = $country_id;
        $params['exclude'] = 'hashtags';
        $endpoint = 'trends/place';
        //asks whether it needs data from cache or no
        if($this->fetchFromCache($endpoint)){
            $data = $this->getCache($endpoint);
        } else {
            $data = $connection->get($endpoint, $params);
            //$data = array_slice($data[0]->trends, 0,$number);
            $this->is_error($data);
            $data = $data[0]->trends;
            $this->saveCache($endpoint, $data);            
        }
        return $data;
    }
    private function is_error($response){
        if(!empty($response->errors)){
            throw new Exception($response->errors[0]->message, 1);
        } 
    }
    private function getCache($endpoint, $id = null){
        $cache_file = $this->cacheName($endpoint, $id);
        return unserialize(file_get_contents( $cache_file ));       
    }     
    private function saveCache($endpoint, $data, $id = null){
        $cache_file = $this->cacheName($endpoint, $id);
        $cache_static = fopen( $cache_file, 'w' );
        fwrite( $cache_static, serialize($data) );
        fclose( $cache_static );        
    }
    private function fetchFromCache($endpoint, $id = null){
        switch ($endpoint) {
            case 'trends/place':
                $ttl = 300;
                break;
            case 'lists/list':
                $ttl = 3600;
                break;                
            default:
                $ttl = 600;
                break;
        }
        $cache_file = $this->cacheName($endpoint, $id);
        if (file_exists($cache_file)) {
            $modified = filemtime( $cache_file );
            $now = time();
            if ( !$modified || ( ( $now - $modified ) > $ttl ) ) {
                return false;
            }  else {
                return true;
            }          
        } else {
            return false;
        }
    }
    private function cacheName($endpoint, $id){

        $cache_dir = $this->_cache_dir;
        $endpoint = str_replace('/', '_', $endpoint);
        $user_name = $this->_token['username'];
        if(empty($id)){
            $cache_file = $cache_dir.'/'.$user_name.'_'.$endpoint;
        } else {
            $cache_file = $cache_dir.'/'.$endpoint.'_'.$id;
        }
        return $cache_file;        
    }

    /*
     * !!!!!!!!!!
     * @param $query
     * @param array $params
     * @return mixed
     */
    public function get_languages() {
        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'], $this->_token['token1'], $this->_token['token2'] );
        $language = $connection->get('help/languages');
        return $language;
    } 


    /**
     * @param $query
     * @param array $args
     * @return array
     */
    public function search_users($query, $args = array(), $get_tweet= false) {
        if (isset($args['min_followers'])) {
            $min_followers = (int)$args['min_followers'];
            unset($args['min_followers']);
        } else {
            $min_followers = 0;
        }
        if (isset($args['max_followers'])) {
            $max_followers = (int)$args['max_followers'];
            unset($args['max_followers']);
        } else {
            $max_followers = 0;
        }
        if (isset($args['age_of_account'])) {
            $age_of_account = (int)$args['age_of_account'];
            unset($args['age_of_account']);
        }
        if (isset($args['tweets_count'])) {
            $tweets_count = (int)$args['tweets_count'];
            unset($args['tweets_count']);
        }
        if(isset($args['no_previous_engage'])){
            $no_previous_engage = $args['no_previous_engage'];
            unset($args['no_previous_engage']);
        }
        if(!isset($args['lang'])) {
            $args['lang'] = 'en';
        }
        $return_array = array(
            'users' => array()
        );
        $now = new DateTime();
        $tweets = $this->search_tweets($query, $args);
        foreach($tweets->statuses as $tweet) {
            if ($min_followers > 0) {
                if(!$tweet->user->followers_count > $min_followers) {
                   continue;
                }
            }
            if($max_followers > 0) {
                if(!$tweet->user->followers_count < $max_followers) {
                    continue;
                }
            }
            if($no_previous_engage){
                if($tweet->retweeted){
                    continue;
                }
                if($tweet->favorited){
                    continue;
                } 
            }           
            if(isset($age_of_account)) {
                $created_account_date = new DateTime($tweet->user->created_at);
                $diff = $created_account_date->diff($now);
                if($diff->invert) {
                    continue;
                } else {
                    if(is_array($age_of_account)) {
                        if(count($age_of_account) < 2 ||
                            ($age_of_account[0] > $diff->m || $diff->m > $age_of_account[1])) {

                            continue;
                        }
                    } else {
                        if($age_of_account >= $diff->m) {
                            continue;
                        }
                    }
                }
            }
            if(isset($tweets_count)) {
                if(is_array($tweets_count)) {
                    if(count($tweets_count) < 2 ||
                        ($tweets_count[0] > $tweet->user->statuses_count ||
                            $tweet->user->statuses_count > $tweets_count[1])) {

                        continue;
                    }
                } else {
                    if($tweets_count >= $tweet->user->statuses_count) {
                        continue;
                    }
                }
            }
            if($get_tweet){
                $_tweets = $tweet->id_str;
                break;
            }
            $return_array['users'][] = $tweet->user->id;
        }
        $return_array['max_id'] = $this->getMaxIdFromNextResult(
            (isset($tweets->search_metadata->next_results))
                ? $tweets->search_metadata->next_results
                : null
        );
        if($get_tweet){
            return $_tweets;
        }        
        return $return_array;
    }

    /**
     * @param string $query
     * @param array $include words separated by comma
     * @param array $exclude words separated by comma
     * @param bool $exact
     * @return string
     */
    public function create_query($query, $include, $exclude, $exact) {
        if ($exact) {
            $result_query = '"'.$query.'"';
        } else {
            $result_query = $query;
        }
        foreach($include as $include_element) {
            $result_query .= ' +'.$include_element;
        }
        foreach($exclude as $exclude_element) {
            $result_query .= ' -'.$exclude_element;
        }
        return urlencode($result_query);
    }

    // /**
    //  * Get username from profile link
    //  *
    //  * @param $link
    //  * @return mixed
    //  */
    public function getUserFromLink($link)
    {
        $parts = explode('/', $link);

        return  $parts[count($parts)-1];
    }

    /**
     * @param null $nextResult
     * @return null
     */
    public function getMaxIdFromNextResult($nextResult = null) {
        if (!$nextResult) {
            return null;
        }
        $result = preg_match('|max_id=(\d+)&*.*$|', $nextResult, $matches);
        if ($result) {
            $max_id = $matches[1];
        } else {
            $max_id = null;
        }
        return $max_id;
    }

    function parseErrors($response){
        if(!empty($response->errors[0])){
            throw new Exception($response->errors[0]->message, 1);
        }
    }
    // /**
    //  * Authorize user using Access token from database & app-credentials
    //  * After - call Twitter API v1.1 (Send GET Request)
    //  * GET Request used to get some info from twitter
    //  *
    //  * @access private
    //  * @param $url
    //  * @param array $params
    //  * @return mixed
    //  */
    // private function _api_call( $url, $params = array() ) {

    //     $connection = new OAuthDamnit( $this->_config['consumer_key'], $this->_config['consumer_secret'],
    //         $this->_token['token1'], $this->_token['token2'] );

    //     $data_json = $connection->get($url, $params);
    //     $data = json_decode($data_json);
    //     return $data;
    // }

    // /**
    //  * Authorize user using Access token from database & app-credentials
    //  * After - call Twitter API v1.1 (Send POST Request)
    //  * POST Request used to create some action (create / destroy post, favorite / retweet and etc)
    //  *
    //  * @access private
    //  * @param $url
    //  * @param array $params
    //  * @return mixed
    //  */
    // private function _api_call_post( $url, $params = array() ) {

    //     $connection = new OAuthDamnit( $this->_config['consumer_key'], $this->_config['consumer_secret'],
    //         $this->_token['token1'], $this->_token['token2'] );

    //     $data_json = $connection->post($url, $params);
    //     $data = json_decode($data_json);
    //     return $data;
    // }

    // private function _api_call_get( $url, $params = array() ) {

    //     $connection = new OAuthDamnit( $this->_config['consumer_key'], $this->_config['consumer_secret'],
    //         $this->_token['token1'], $this->_token['token2'] );

    //     $data_json = $connection->get($url, $params);
    //     $data = json_decode($data_json);
    //     return $data;
    // }


}
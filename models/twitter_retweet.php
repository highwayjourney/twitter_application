<?php

/**
 * TwitterRetweet model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $tweet_id
 * @property integer $last_check
 * @property bool $need_retweet
 * @property integer $start_retweet_time
 * @property integer $end_retweet_time
 * @property integer $access_token_id
 *
 * @property-read  DataMapper $user
 */
class Twitter_retweet extends DataMapper
{

    var $has_one = array(
        'user',
        'access_token'
    );
    var $has_many = array();

    var $validation = array();

    var $updated_field = 'last_check';

    var $table = 'twitter_retweets';

    /**
     * Initialize User model
     *
     * @access public
     *
     * @param $id (int) - user id
     *
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user->get();
    }

    /**
     * @param $user_id
     * @return DataMapper
     */
    public function getByUserId($user_id) {
        return $this->where('user_id', $user_id)->get();
    }

    /**
     * @param int|string $user_id
     */
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    /**
     * @param int|string $retweet_id
     */
    public function setTweetId($tweet_id) {
        $this->tweet_id = $tweet_id;
    }


    /**
     * @param integer $start_follow_time
     */
    public function setStartRetweetTime($start_follow_time) {
        $this->start_follow_time = $start_follow_time;
    }

    /**
     * @param integer $end_follow_time
     */
    public function setEndRetweetTime($end_follow_time) {
        $this->end_follow_time = $end_follow_time;
    }

    /**
     * @param integer $access_token_id
     */
    public function setAccessTokenId($access_token_id) {
        $this->access_token_id = $access_token_id;
    }
}

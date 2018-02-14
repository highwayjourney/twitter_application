<?php
/**
 * TwitterMention model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $tweet_id
 * @property integer $last_check
 * @property bool $need_favorite
 * @property integer $start_favorite_time
 * @property integer $end_favorite_time
 * @property integer $access_token_id
 *
 * @property-read  DataMapper $user
 */
class Twitter_mention extends DataMapper
{

    var $has_one = array(
        'user',
        'access_token'
    );
    var $has_many = array();

    var $validation = array();

    var $updated_field = 'last_check';

    var $table = 'twitter_mentions';

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

    // /**
    //  * @param int|string $favorite_id
    //  */
    // public function setTweetId($favorite_id) {
    //     $this->tweet_id = $favorite_id;
    // }


    /**
     * @param integer $start_follow_time
     */
    public function setStartMentionTime($start_follow_time) {
        $this->start_mention_time = $start_follow_time;
    }

    /**
     * @param integer $end_follow_time
     */
    public function setEndMentionTime($end_follow_time) {
        $this->end_mention_time = $end_follow_time;
    }

    /**
     * @param integer $access_token_id
     */
    public function setAccessTokenId($access_token_id) {
        $this->access_token_id = $access_token_id;
    }
}

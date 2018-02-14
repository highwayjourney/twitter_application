<?php

class Twitter_task extends CLI_controller {

    /**
     * Update Twitter followers in DB.
     *
     * @access public
     * @param  array $args
     */
    public function updateFollowers($args) {
                // $social_group = new social_group(2);
                // $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
                // $args['profile_id'] = 2;        
        try{
            //d($args);
            $user_id = (int)$args['user_id'];
            $user = new User($user_id);
            $access_token_id = $args['id'];
            
            $twitter = $this->inicializeTwitterSocializer($user_id, $args);

            $user->twitter_follower
                ->where('still_follow', true)
                ->where('access_token_id', $access_token_id)
                ->update([
                    'still_follow' => false
                ]);

            $answer = $twitter->get_followers();

            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                if(empty($answer->ids)){
                    d('no followers');
                    log_message('TASK_SUCCESS', __FUNCTION__ . 'Nothing to do, user has no followers yet');
                    return;
                }
                $followersIds = $answer->ids;
                //d($followersIds);
                Twitter_follower::create()
                    ->where_in('follower_id', $followersIds)
                    ->where('user_id', $user_id)
                    ->where('access_token_id', $access_token_id)
                    ->update([
                        'unfollow_time' => null,
                        'still_follow' => true
                    ]);

                $exists_followers_ids = Twitter_follower::create()
                    ->where_in('follower_id', $followersIds)
                    ->where('user_id', $user_id)
                    ->where('access_token_id', $access_token_id)
                    ->get()->all_to_array('follower_id');
                $new_followers_ids = [];

                foreach($exists_followers_ids as $exists_followers_id) {
                    $_exists_followers_ids[] = $exists_followers_id['follower_id'];

                }

                foreach($followersIds as $followersId) {
                    if(!in_array($followersId, $_exists_followers_ids)) {
                        $new_followers_ids[] = $followersId;
                    }
                }                

                $first_time = false;
                if(empty($_exists_followers_ids)){
                    $new_followers_ids = $followersIds;
                    $first_time = true;
                }
                //d("New Followers count: " .count($new_followers_ids). "\n". $this->getDebugInfo($user, $access_token_id));
                log_message('TASK_DEBUG', __FUNCTION__ . 'New Followers count: '.count($new_followers_ids). "Messages". "\n". $this->getDebugInfo($user, $access_token_id));
                // if(count($new_followers_ids) > 10){
                //     log_message('TASK_DEBUG', __FUNCTION__ . 'Too many new followers to send, slicing to 10. '. "\n". $this->getDebugInfo($user, $access_token_id));
                //     shuffle($new_followers_ids);
                //     $new_followers_ids = array_slice($new_followers_ids, 0, 10);
                // }

                foreach ($new_followers_ids as $new_followers_id) {
                    $twitterFollower = new Twitter_follower();
                    $twitterFollower->setFollowerId($new_followers_id);
                    $twitterFollower->setUserId($user_id);
                    if ($user->ifUserHasConfigValue('auto_send_welcome_message', $access_token_id) && !$first_time) {
                        $twitterFollower->setNeedMessage(true);
                    }
                    if ($user->ifUserHasConfigValue('auto_follow', $access_token_id) && !$first_time) {
                        $twitterFollower->setNeedFollow(true);
                    }
                    $twitterFollower->setStillFollow(true);
                    $twitterFollower->setAccessTokenId($access_token_id);
                    $twitterFollower->save();
                }

                $unfollowers_query = [
                    'still_follow' => false,
                    'unfollow_time' => null,
                    'start_follow_time' => null,
                    'end_follow_time' => null,
                    'access_token_id' => $access_token_id
                ];

                $new_unfollowers_count = $user->twitter_follower
                    ->where($unfollowers_query)
                    ->count();

                $user->twitter_follower
                    ->where($unfollowers_query)
                    ->update([
                        'unfollow_time' => time()
                    ]);
                Social_analytics::updateAnalytics(
                    $access_token_id,
                    Social_analytics::NEW_UNFOLLOWERS_ANALYTICS_TYPE,
                    $new_unfollowers_count
                );
            }
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage());
        }
    }
    public function getRetweets($args){
                // $social_group = new social_group(7);
                // $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
                // $args['profile_id'] = 7;  
        try {

            $user_id = (int)$args['user_id'];
            $user = new User($user_id);
            $social_group = new social_group($args['profile_id']);
            $access_token_id = $args['id'];
            if (!$user->ifUserHasConfigValue('auto_retweet', $access_token_id)) {
                return;
            }

            //log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start retweet.'.$this->getDebugInfo($user, $access_token_id));
            $twitter = $this->inicializeTwitterSocializer($user_id, $args);
            $number_of_retweets = $user->getDateToRetweet($access_token_id);
            $date = new DateTime('UTC 00:00:00');
            $old_date = DateTime::createFromFormat('!Y-m-d', $number_of_retweets->date);
            if (!$number_of_retweets->id) {
                $number_of_retweets->date = $date->format('Y-m-d');
                $number_of_retweets->setUserId($user_id);
                $number_of_retweets->token_id = $access_token_id;
                $number_of_retweets->count = 0;
            } elseif ($old_date < $date) {
                $number_of_retweets = new Number_of_retweets_twitter();
                $number_of_retweets->date = $date->format('Y-m-d');
                $number_of_retweets->setUserId($user_id);
                $number_of_retweets->count = 0;
                $number_of_retweets->token_id = $access_token_id;
                $number_of_retweets->save();
            } elseif($old_date > $date) {
                //ddd('yuhaaaa');
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                    . 'Twitter Retweets already added.'."\n"
                );
                return;
            }
            unset($old_date);


            //Get tweets from all users by keywords
            $user_search_keywords = $user->getUserSearchKeywords($args['profile_id']);
            $i = 0;
            foreach($user_search_keywords as $user_search_keyword) {

                $other_field = $user_search_keyword->get_other_fields();
                $query = $twitter->create_query(
                    $user_search_keyword->keyword,
                    $other_field['include'],
                    $other_field['exclude'],
                    $user_search_keyword->exact
                );
                if($i > 50){
                    break;
                }            
                $i++;
                $queryArgs = [
                    'min_followers' => $user_search_keyword->min_followers,
                    'max_followers' => $user_search_keyword->max_followers,
                    'max_id' => $user_search_keyword->max_id,
                    'age_of_account' => $age_of_account,
                    'tweets_count' => $tweets_count,
                    'lang' => $other_field['lang'],
                    'no_previous_engage' => true
                ];
                //d($user_search_keyword->keyword);
                $tweets_from_keywords[] = $twitter->search_users($query, $queryArgs, true);
            }   

            //get tweets from lists
            $current_list = $social_group->current_list->order_by('id', 'random')->get(20)->all_to_array('list_id'); 

            if(!empty($current_list)){
                foreach ($current_list as  $value) {
                    $members[] = $twitter->list_members($value['list_id'])->users[0]->id;
                }        
            }
            $date = new DateTime('- 7 days UTC');

            $tweets_from_lists = [];
            foreach ($members as $member) {
                $tweets_from_lists[] = $twitter->get_tweets(array(
                    'user_id' => $member,
                    'exclude_replies' => true,
                    'trim_user' => true,
                    'only_one' => true,
                    'id_only' => true,
                    'count' => 1,
                    'criteriaAnd' => array(
                        array(
                            'param_name' => 'retweeted',
                            'comparison_sign' => '=',
                            'value' => false
                        ),
                        array(
                            'param_name' => 'created_at',
                            'comparison_sign' => '>',
                            'value' => $date->getTimestamp()
                        )
                    )
                ));
            }            
            $tweets_from_lists = array_filter($tweets_from_lists);
            $tweets_from_lists = array_unique($tweets_from_lists);
            $tweets_from_keywords = array_filter($tweets_from_keywords);
            $tweets_from_keywords = array_unique($tweets_from_keywords);

            //d($tweets_from_lists, $tweets_from_keywords);
            
            
            $tweets = array_merge($tweets_from_lists, $tweets_from_keywords);
            //d($tweets);
            $max_daily_auto_retweets = range(1,5);
            $max_daily_auto_retweets = $max_daily_auto_retweets[array_rand($max_daily_auto_retweets)];
            $count = 0;
            foreach ($tweets as $tweet) {
                $twitter_retweet = new Twitter_retweet();
                $current = $twitter_retweet
                    ->where('user_id', $user_id)
                    ->where('tweet_id', $tweet)
                    ->count();
                if($current > 0){
                    continue;
                }
                if ($max_daily_auto_retweets > $number_of_retweets->count) {
                    $date = DateTime::createFromFormat('!Y-m-d', $number_of_retweets->date);
                    $number_of_retweets->count += 1;
                    $number_of_retweets->save();
                    $count++;
                } else {
                    $date = DateTime::createFromFormat('!Y-m-d', $number_of_retweets->date);
                    $date->modify('+1 days');
                    $number_of_retweets = new Number_of_retweets_twitter();
                    $number_of_retweets->date = $date->format('Y-m-d');
                    $number_of_retweets->setUserId($user_id);
                    $number_of_retweets->token_id = $access_token_id;
                    $number_of_retweets->count = 1;
                    $number_of_retweets->save();
                }
                //ADD TO TWITTER_RETWEETS TABLE
                $twitter_retweet = new Twitter_retweet();
                //d($tweet);
                $twitter_retweet->setTweetId($tweet);
                $twitter_retweet->setUserId((string)$user_id);
                $twitter_retweet->setAccessTokenId($access_token_id);

                $start_date = clone $date;

                $start_date = $start_date->getTimestamp();
                $start_date = $start_date + mt_rand(0,82800);
                $start_date = new DateTime('@'.$start_date);

                $end_date = clone $start_date;
                $end_date = $end_date->modify('+1 days');

                $twitter_retweet->need_retweet = true;
                $twitter_retweet->start_retweet_time = $start_date->getTimestamp();
                $twitter_retweet->end_retweet_time = $end_date->getTimestamp();

                $twitter_retweet->save();
                if($count > 5){
                    break;
                }                
            }   
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage(). "\n". $this->getDebugInfo($user, $access_token_id));
        }                    
    }
    public function getMentions($args = null){
                // $social_group = new social_group(7);
                // $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
                // $args['profile_id'] = 7;          
        $max_daily_auto_mentions = 5;
        try {

            $user_id = (int)$args['user_id'];
            $user = new User($user_id);
            $access_token_id = $args['id'];
            if (!$user->ifUserHasConfigValue('smart_mention', $access_token_id)) {
                return;
            }  
            $number_of_mentions = $user->getDateToMention($access_token_id); //NEED TO ADD TO USERS, MODEL AND TABLE
            $date = new DateTime('UTC 00:00:00');
            $old_date = DateTime::createFromFormat('!Y-m-d', $number_of_mentions->date);
            if (!$number_of_mentions->id) {
                $number_of_mentions->date = $date->format('Y-m-d');
                $number_of_mentions->setUserId($user_id);
                $number_of_mentions->token_id = $access_token_id;
                $number_of_mentions->count = 0;
            } elseif ($old_date < $date) {
                $number_of_mentions = new number_of_mentions_twitter();
                $number_of_mentions->date = $date->format('Y-m-d');
                $number_of_mentions->setUserId($user_id);
                $number_of_mentions->count = 0;
                $number_of_mentions->token_id = $access_token_id;
                $number_of_mentions->save();
            } elseif($old_date > $date) {
                //ddd('yuhaaaa');
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                    . 'Twitter Mentions already added.'
                );
                return;
            }            
            $website = '';
            $limit = range(1,5);
            $limit = $limit[array_rand($limit)];    
            //$quotes = array("Hola Mundo", "Lorem Ipwsum", "Hello WOrld");
            $quotes = unserialize(base64_decode($user->ifUserHasConfigValue('mention_quote', $access_token_id)));
            $website = $user->ifUserHasConfigValue('mention_website', $access_token_id)?$user->ifUserHasConfigValue('mention_website', $access_token_id):"";
            if(!empty($quotes)) {    
                $twitter = $this->inicializeTwitterSocializer($user_id, $args);
                $_current_mentions = $user->twitter_mention->where('access_token_id', $access_token_id)->get();
                foreach ($_current_mentions as  $value) {
                    $current_mentions[] = $value->follower_id;
                }
                $will_mention = $user->twitter_follower->where_not_in('follower_id', $current_mentions)->where(array('access_token_id' => $access_token_id, 'need_follow' => true))->order_by('start_follow_time', 'desc')->get($limit);
                foreach ($will_mention as $user) {
                    $quote = trim($quotes[array_rand($quotes)]);
                    $tweet = $twitter->get_user_full_info(null, $user->follower_id);
                    if(empty($tweet->screen_name)){
                        continue;
                    }
                    $username = $tweet->screen_name; 
                    $userimage = $tweet->profile_image_url_https;
                    $follower_id = $tweet->id_str;
                    $start_date = clone $date;

                    $start_date = $start_date->getTimestamp();
                    $start_date = $start_date + mt_rand(0,82800);
                    $start_date = new DateTime('@'.$start_date);

                    $end_date = clone $start_date;
                    $end_date = $end_date->modify('+1 days');   

                    $message = $quote." @".$username. " ". $website;
                    if (strpos($quote, '{user}') !== false && strpos($quote, '{website}') !== false) {
                        $message = str_replace('{user}', "@".$username, $quote);
                        $message = str_replace('{website}', $website, $message);
                    } elseif (strpos($quote, '{user}') === false && strpos($quote, '{website}') !== false) {
                        $message = str_replace('{website}', $website, $quote);
                        $message.= " @".$username;
                    } elseif (strpos($quote, '{user}') !== false && strpos($quote, '{website}') === false) {
                        $message = str_replace('{user}', "@".$username, $quote);
                        //$message.= $website;
                    }                     
                    $mention = new twitter_mention();  
                    $mention->user_id = $user_id; 
                    $mention->follower_id = $follower_id;
                    $mention->need_mention = 1;   
                    $mention->message = $message;   
                    $mention->user_image = $userimage; 
                    $mention->start_mention_time = $start_date->getTimestamp();
                    $mention->end_mention_time = $start_date->getTimestamp();
                    $mention->access_token_id = $access_token_id;
                    $mention->save();

                    if ($max_daily_auto_mentions > $number_of_mentions->count) {
                        $date = DateTime::createFromFormat('!Y-m-d', $number_of_mentions->date);
                        $number_of_mentions->count += 1;
                        $number_of_mentions->save();
                    } else {
                        $date = DateTime::createFromFormat('!Y-m-d', $number_of_mentions->date);
                        $date->modify('+1 days');
                        $number_of_mentions = new Number_of_mentions_twitter();
                        $number_of_mentions->date = $date->format('Y-m-d');
                        $number_of_mentions->setUserId($user_id);
                        $number_of_mentions->token_id = $access_token_id;
                        $number_of_mentions->count = 1;
                        $number_of_mentions->save();
                    }                                          
                }
            }
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage());
        }
    }

    public function getFavourites($args){
        //$args = array('user_id' => $this->c_user->id, 'profile_id' => $this->profile->id, 'id' => 5);
        try {
            $user_id = (int)$args['user_id'];
            $user = new User($user_id);
            $social_group = new social_group($args['profile_id']);
            $access_token_id = $args['id'];
            if (!$user->ifUserHasConfigValue('auto_favourite', $access_token_id)) {
                return;
            }

            //log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start retweet.'.$this->getDebugInfo($user, $access_token_id));
            $twitter = $this->inicializeTwitterSocializer($user_id, $args);
            $number_of_favourites = $user->getDateToFavourite($access_token_id);
            $date = new DateTime('UTC 00:00:00');
            $old_date = DateTime::createFromFormat('!Y-m-d', $number_of_favourites->date);
            if (!$number_of_favourites->id) {
                $number_of_favourites->date = $date->format('Y-m-d');
                $number_of_favourites->setUserId($user_id);
                $number_of_favourites->token_id = $access_token_id;
                $number_of_favourites->count = 0;
            } elseif ($old_date < $date) {
                $number_of_favourites = new Number_of_favourites_twitter();
                $number_of_favourites->date = $date->format('Y-m-d');
                $number_of_favourites->setUserId($user_id);
                $number_of_favourites->count = 0;
                $number_of_favourites->token_id = $access_token_id;
                $number_of_favourites->save();
            } elseif($old_date > $date) {
                //ddd('yuhaaaa');
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                    . 'Twitter Retweets already added.'."\n"
                );
                return;
            }
            unset($old_date);


            //Get tweets from all users by keywords
            $user_search_keywords = $user->getUserSearchKeywords($args['profile_id']);
            $i = 0;
            foreach($user_search_keywords as $user_search_keyword) {

                $other_field = $user_search_keyword->get_other_fields();
                $query = $twitter->create_query(
                    $user_search_keyword->keyword,
                    $other_field['include'],
                    $other_field['exclude'],
                    $user_search_keyword->exact
                );
                if($i > 50){
                    break;
                }            
                $i++;
                $queryArgs = [
                    'min_followers' => $user_search_keyword->min_followers,
                    'max_followers' => $user_search_keyword->max_followers,
                    'max_id' => $user_search_keyword->max_id,
                    'age_of_account' => $age_of_account,
                    'tweets_count' => $tweets_count,
                    'lang' => $other_field['lang'],
                    'no_previous_engage' => true
                ];
                //d($user_search_keyword->keyword);
                $tweets_from_keywords[] = $twitter->search_users($query, $queryArgs, true);
            }   

            // shuffle($tweets_from_keywords);
            // $tweets_from_keywords = array_slice($tweets_from_keywords, 0, 4);
            //get tweets from lists
            $current_list = $social_group->current_list->order_by('id', 'random')->get(20)->all_to_array('list_id'); 

            if(!empty($current_list)){
                foreach ($current_list as  $value) {
                    $members[] = $twitter->list_members($value['list_id'])->users[0]->id;
                }        
            }
            $date = new DateTime('- 7 days UTC');

            $tweets_from_lists = [];
            foreach ($members as $member) {
                $tweets_from_lists[] = $twitter->get_tweets(array(
                    'user_id' => $member,
                    'exclude_replies' => true,
                    'trim_user' => true,
                    'only_one' => true,
                    'id_only' => true,
                    'count' => 1,
                    'criteriaAnd' => array(
                        array(
                            'param_name' => 'retweeted',
                            'comparison_sign' => '=',
                            'value' => false
                        ),
                        array(
                            'param_name' => 'created_at',
                            'comparison_sign' => '>',
                            'value' => $date->getTimestamp()
                        )
                    )
                ));
            }            
            $tweets_from_lists = array_filter($tweets_from_lists);
            $tweets_from_lists = array_unique($tweets_from_lists);
            $tweets_from_keywords = array_filter($tweets_from_keywords);
            $tweets_from_keywords = array_unique($tweets_from_keywords);

            //ddd($tweets_from_lists, $tweets_from_keywords);
            
            
            $tweets = array_merge($tweets_from_lists, $tweets_from_keywords);
            $max_daily_auto_retweets = range(1,5);
            $max_daily_auto_retweets = $max_daily_auto_retweets[array_rand($max_daily_auto_retweets)];
            //d($tweets, $max_daily_auto_retweets);
            $count = 0;
            foreach ($tweets as $tweet) {
                $twitter_retweet = new Twitter_favourite();
                $current = $twitter_retweet
                    ->where('user_id', $user_id)
                    ->where('tweet_id', $tweet)
                    ->count();
                if($current > 0){
                    continue;
                }
                if ($max_daily_auto_retweets > $number_of_favourites->count) {
                    $date = DateTime::createFromFormat('!Y-m-d', $number_of_favourites->date);
                    $number_of_favourites->count += 1;
                    $number_of_favourites->save();
                    $count++;
                } else {
                    $date = DateTime::createFromFormat('!Y-m-d', $number_of_favourites->date);
                    $date->modify('+1 days');
                    $number_of_favourites = new Number_of_favourites_twitter();
                    $number_of_favourites->date = $date->format('Y-m-d');
                    $number_of_favourites->setUserId($user_id);
                    $number_of_favourites->token_id = $access_token_id;
                    $number_of_favourites->count = 1;
                    $number_of_favourites->save();
                }
                //ADD TO TWITTER_RETWEETS TABLE
                $twitter_retweet = new Twitter_favourite();
                $twitter_retweet->setTweetId($tweet);
                $twitter_retweet->setUserId((string)$user_id);
                $twitter_retweet->setAccessTokenId($access_token_id);

                $start_date = clone $date;

                $start_date = $start_date->getTimestamp();
                $start_date = $start_date + mt_rand(0,82800);
                $start_date = new DateTime('@'.$start_date);

                $end_date = clone $start_date;
                $end_date = $end_date->modify('+1 days');

                $twitter_retweet->need_favourite = true;
                $twitter_retweet->start_favourite_time = $start_date->getTimestamp();
                $twitter_retweet->end_favourite_time = $end_date->getTimestamp();

                $twitter_retweet->save();
                if($count > 5){
                    break;
                }                  
            }
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage(). "\n". $this->getDebugInfo($user, $access_token_id));
        }                
    }
    /**
    *Get schedule retweets and do it
    * RUNS 10 minutely
    *
    */
    function sendRetweet(){
        $now = new DateTime();
        $now = $now->getTimestamp();
        $twitter_retweet = new Twitter_retweet();
        $tweets = $twitter_retweet
            ->where('need_retweet', true)
            //->where('user_id', 3)
            ->where('start_retweet_time >=', $now)
            ->where('end_retweet_time >=', $now)
            ->get();
        log_message('TASK_DEBUG', __FUNCTION__ . 'Sending '.count($tweets->all_to_array()).' Retweets' );
        //ddd('Sending '.count($tweets->all_to_array()).' Retweets');
        $retweets_count = 0;
        if(!empty($tweets->all_to_array())){
            $errors = false;
            $retweets_count = 0;
            foreach ($tweets as $tweet) {
                try{
                    $user = new user($tweet->user_id);
                    if (!$user->ifUserHasConfigValue('auto_retweet', $tweet->access_token_id)) {
                        continue;
                    }                
                    $quotes = unserialize(base64_decode($user->ifUserHasConfigValue('retweet_quote', $tweet->access_token_id)));
                    //ddd($quotes);
                    $quote = $quotes[array_rand($quotes)];

                    $this->load->library('Socializer/socializer');
                    $access_token = new access_token($tweet->access_token_id);
                    $twitter = Socializer::factory('Twitter', $tweet->user_id, $access_token->to_array());
                    if(empty($quote)){
                        $answer = $twitter->retweet($tweet->tweet_id);
                    } else {
                        $user_tweet = $twitter->get_single_tweet($tweet->tweet_id);
                        $screen_name = $user_tweet[0]->user->screen_name;
                        $answer = $twitter->tweet($quote." https://www.twitter.com/".$screen_name ."/status/".$tweet->tweet_id);
                    }
                    if (!empty($answer->errors)) {
                        $errors = true;
                        foreach($answer->errors as $err) {
                            log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message."\n");                         
                        }
                        $tweet->delete();
                    } else {
                        $retweets_count++;
                        $tweet->need_retweet = 0;
                        $tweet->save();                        
                    }
                } catch (Exception $e) {
                    log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage()." ".$this->getDebugInfo($user, $tweet->access_token_id));
                    $tweet->delete();
                }                 
            }
            Social_analytics::updateAnalytics(
                $tweet->access_token_id,
                Social_analytics::RETWEETS_ANALYTICS_TYPE,
                $retweets_count
            );
        }  
        log_message('TASK_DEBUG', __FUNCTION__ . 'Success Sending '.$retweets_count.' Retweets' );
        //d('Success Sending '.$retweets_count.' Retweets');       
    }
    function sendFavourite($args= null){
        $now = new DateTime();
        $now = $now->getTimestamp();

        $twitter_favourite = new twitter_favourite();
        $tweets = $twitter_favourite
            ->where('need_favourite', true)
            //->where('user_id', 3)
            ->where('start_favourite_time >=', $now)
            ->where('end_favourite_time >=', $now)
            ->get();
        //d($tweets->all_to_array(), $now);
        log_message('TASK_DEBUG', __FUNCTION__ . ' Sending '.count($tweets->all_to_array()).' Favourites' );
        //d('Sending '.count($tweets->all_to_array()).' Favourites');
        $retweets_count = 0;
        if(!empty($tweets->all_to_array())){
            $errors = false;
            $retweets_count = 0;
            foreach ($tweets as $tweet) {
                try{
                    $user = new user($tweet->user_id);
                    if (!$user->ifUserHasConfigValue('auto_favourite', $tweet->access_token_id)) {
                        continue;
                    }                

                    $this->load->library('Socializer/socializer');
                    $access_token = new access_token($tweet->access_token_id);
                    $twitter = Socializer::factory('Twitter', $tweet->user_id, $access_token->to_array());
                    $answer = $twitter->favorite($tweet->tweet_id);
                    //d($answer);
                    if (!empty($answer->errors)) {
                        $errors = true;
                        foreach($answer->errors as $err) {
                            log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message."\n");
                        }
                        if($err->code == 139){
                            $tweet->need_favourite = 0;
                            $retweets_count++;
                            $tweet->save(); 
                        } else {
                            $tweet->delete();
                        }
                    } else {
                        $retweets_count++;
                        $tweet->need_favourite = 0;
                        $tweet->save();                        
                    }
                } catch (Exception $e) {
                    log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage()." ".$this->getDebugInfo($user, $tweet->access_token_id));
                    $tweet->delete();
                }                
            }
            Social_analytics::updateAnalytics(
                $tweet->access_token_id,
                Social_analytics::FAVOURITES_ANALYTICS_TYPE,
                $retweets_count
            );
        }
        log_message('TASK_DEBUG', __FUNCTION__ . 'Success Sending '.$retweets_count.' Favourites' );                      
    }
    function sendMention($args= null){        
        $now = new DateTime();
        $now = $now->getTimestamp();

        $twitter_mention = new twitter_mention();
        $tweets = $twitter_mention
            ->where('need_mention', 1)
            //->where('user_id', 3)
            ->where('start_mention_time >=', $now)
            ->where('end_mention_time >=', $now)
            ->get();
        log_message('TASK_DEBUG', __FUNCTION__ . ' Sending '.count($tweets->all_to_array()).' Mentions' );
        //d('TASK_DEBUG', __FUNCTION__ . ' Sending '.count($tweets->all_to_array()).' Mentions');
        //d('Sending '.count($tweets->all_to_array()).' Mentions');
        $retweets_count = 0;
        if(!empty($tweets->all_to_array())){
            $errors = false;
            $retweets_count = 0;
            foreach ($tweets as $tweet) {
                try {
                    $user = new user($tweet->user_id);
                    if (!$user->ifUserHasConfigValue('smart_mention', $tweet->access_token_id)) {
                        continue;
                    }                

                    $this->load->library('Socializer/socializer');
                    $access_token = new access_token($tweet->access_token_id);
                    $twitter = Socializer::factory('Twitter', $tweet->user_id, $access_token->to_array());
                    $answer = $twitter->tweet($tweet->message);
                    //d($answer);
                    if (!empty($answer->errors)) {
                        $errors = true;
                        foreach($answer->errors as $err) {
                            log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message."\n");
                        }
                        $tweet->delete();
                    } else {
                        $retweets_count++;
                        $tweet->need_mention = 0;
                        $tweet->save();                        
                    }
                } catch (Exception $e) {
                    log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage()." ".$this->getDebugInfo($user, $tweet->access_token_id));
                    $tweet->delete();
                } 
            }
            Social_analytics::updateAnalytics(
                $tweet->access_token_id,
                Social_analytics::MENTION_ANALYTICS_TYPE,
                $retweets_count
            );
        }     
        log_message('TASK_DEBUG', __FUNCTION__ . 'Success Sending '.$retweets_count.' Menitons' );
        //d('Success Sending '.$retweets_count.' Mentions');                  
    }     
    /**
     * Send welcome message to new followers in Twitter.
     *
     * @access public
     * @param $args
     */
    public function sendWelcomeMessage($args) {
        // $args['user_id'] = 3;
        // $args['id'] = 4320;
        try {
            $user_id = (int)$args['user_id'];
            $user = new User($user_id);
            $access_token_id = $args['id'];

            if (!$user->ifUserHasConfigValue('auto_send_welcome_message', $access_token_id)) {
                return;
            }

            $twitter = $this->inicializeTwitterSocializer($user_id, $args);

            /* @var Twitter_follower[] $followers */
            $followers = $user
                ->twitter_follower
                ->where('still_follow', true)
                ->where('need_message', true)
                ->where('access_token_id', $access_token_id)
                ->get();
            $count = 0;
            foreach($followers as $follower) {
                if($count > 8){
                    break;
                }
                sleep(1);
                $answer = $twitter->direct_message(array(
                    'user_id' => $follower->follower_id,
                    'text' => $user->ifUserHasConfigValue('welcome_message_text', $access_token_id)
                ));
                $count++;
                if ($answer->errors) {
                    foreach($answer->errors as $err) {
                        log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message .
                            "\n". $this->getDebugInfo($user, $access_token_id));
                    }
                } else {
                    log_message('TASK_DEBUG', __FUNCTION__ . 'Success sending welcome menssage text '.$this->getDebugInfo($user, $access_token_id));
                }
                $follower->setNeedMessage(false);
                $follower->save();                
            }
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage());
        }
        //log_message('TASK_DEBUG', __FUNCTION__ . 'Success sending welcome menssage text '.$this->getDebugInfo($user, $access_token_id));       
    }

    /**
     * Follow new follower in Twitter.
     *
     * @access public
     * @param $args
     */
    public function followNewFollowers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_follow', $access_token_id)) {
            return;
        }

        log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start follow new followers.'.$this->getDebugInfo($user, $access_token_id));
        $twitter = $this->inicializeTwitterSocializer($user_id, $args);
        
        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', true)
            ->where('need_follow', true)
            ->where('start_follow_time', null)
            ->where('end_follow_time', null)
            ->where('access_token_id', $access_token_id)
            ->get();
        $new_followers_count = 0;
        foreach($followers as $follower) {
            $answer = $twitter->follow($follower->follower_id);
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $new_followers_count++;
                $follower->setNeedFollow(false);
                $follower->save();
            }
        }
        Social_analytics::updateAnalytics(
            $access_token_id,
            Social_analytics::NEW_FOLLOWING_ANALYTICS_TYPE,
            $new_followers_count
        );
        log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: finish follow new followers. Added '
            .$new_followers_count.' users.'."\n"
            .$this->getDebugInfo($user, $access_token_id)
        );

        $data = new DateTime('UTC');
        if (!$user->ifUserHasConfigValue('auto_follow_users_by_search', $access_token_id)) {
            $user->twitter_follower
                ->where('need_follow', true)
                ->where('start_follow_time IS NOT NULL')
                ->where('end_follow_time IS NOT NULL')
                ->where('access_token_id', $access_token_id)
                ->delete();
            $user->number_of_added_users_twitter
                ->where('date >= \''.$data->format('Y-m-d').'\'')
                ->delete();
        }
        /* @var Twitter_follower[] $followers */
        $time = $data->getTimestamp();
        $query = $user
            ->twitter_follower
            ->where([
                'need_follow' => true,
                'access_token_id' => $access_token_id
            ])
            ->where("((start_follow_time <= '{$time}' AND end_follow_time >= '{$time}') OR (start_follow_time = end_follow_time AND end_follow_time < '{$time}'))");
        $followers = $query->get();
        $new_followers_count = 0;
        foreach($followers as $follower) {
            sleep(1);
            $answer = $twitter->follow($follower->follower_id);
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $follower->setNeedFollow(false);
                $follower->setUnfollowTime($data->getTimestamp());
                $follower->setStillFollow(false);
                $follower->save();

                $new_followers_count++;
            }
        }
        Social_analytics::updateAnalytics(
            $access_token_id,
            Social_analytics::NEW_FOLLOWING_BY_SEARCH_ANALYTICS_TYPE,
            $new_followers_count
        );
        log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: finish follow new followers by search. Added '
            .$new_followers_count.' users.'."\n"
            .$this->getDebugInfo($user, $access_token_id)
        );
    }

    /**
     * Unfollow those who unsubscribed from your account in Twitter.
     *
     * @access public
     * @param $args
     */
    public function unfollowUnsubscribedUsers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_unfollow', $access_token_id)) {
            return;
        }
        $days_before_unfollow = $user->ifUserHasConfigValue('days_before_unfollow', $access_token_id);
        if(!$days_before_unfollow) {
            $days_before_unfollow = 3;
        }
        $date = new DateTime('- '.$days_before_unfollow.' days UTC');

        $twitter = $this->inicializeTwitterSocializer($user_id, $args);

        $new_unfollowing_count = 0;
//        $followersIds = $twitter->get_friends();
//        foreach($followersIds->ids as $followerId) {
//            If(!$user->isUserHasTwitterFollower($followerId, $access_token_id)) {
//                $answer = $twitter->unfollow($followerId);
//                if ($answer->errors) {
//                    foreach($answer->errors as $err) {
//                        log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
//                    }
//                } else {
//                    $new_unfollowing_count++;
//                }
//            }
//        }

        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', false)
            ->where('access_token_id', $access_token_id)
            ->where('unfollow_time < '.$date->getTimestamp())
            ->get();
        foreach($followers as $follower) {
            $answer = $twitter->unfollow($follower->follower_id);
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $follower->delete();
                $new_unfollowing_count++;
            }
        }
        Social_analytics::updateAnalytics(
            $access_token_id,
            Social_analytics::NEW_UNFOLLOWING_ANALYTICS_TYPE,
            $new_unfollowing_count
        );
    }

    /**
     * Search users and follow them
     *
     * @param $args
     */
    public function searchUsers($args) {
        try {
                // $social_group = new social_group(7);
                // $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
                // $args['profile_id'] = 7; 
            $user_id = (int)$args['user_id'];
            $user = new User($user_id);
            $access_token_id = $args['id'];
            if (!$user->ifUserHasConfigValue('smart_engage', $access_token_id)) {
                return;
            }

            log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start search users to follow.'.$this->getDebugInfo($user, $access_token_id));

            $date = new DateTime('UTC 00:00:00');

            $user_timezone = new DateTimeZone(User_timezone::get_user_timezone($user_id));
            $timezone_offset = $user_timezone->getOffset($date) / 3600;

            $twitter = $this->inicializeTwitterSocializer($user_id, $args);

            $user_search_keywords = $user->getUserSearchKeywords($args['profile_id']);
            $number_of_added_users = $user->getDateToAddUserTwitter($access_token_id);
            //$max_daily_auto_follow_users_by_search = (int)$user->ifUserHasConfigValue('max_daily_auto_follow_users_by_search', $access_token_id);
            $max_daily_auto_follow_users_by_search =  5;
            $old_date = DateTime::createFromFormat('!Y-m-d', $number_of_added_users->date);
            if (!$number_of_added_users->id) {
                $number_of_added_users->date = $date->format('Y-m-d');
                $number_of_added_users->setUserId($user_id);
                $number_of_added_users->token_id = $access_token_id;
                $number_of_added_users->count = 0;
            } elseif ($old_date < $date) {
                $number_of_added_users = new Number_of_added_users_twitter();
                $number_of_added_users->date = $date->format('Y-m-d');
                $number_of_added_users->setUserId($user_id);
                $number_of_added_users->count = 0;
                $number_of_added_users->token_id = $access_token_id;
                $number_of_added_users->save();
            } elseif($old_date > $date) {\
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                    . 'Twitter followers already added.'."\n"
                    .$this->getDebugInfo($user, $access_token_id)
                );
                return;
            }
            unset($old_date);

            $age_of_account = $user->ifUserHasConfigValue('age_of_account', $access_token_id);
            if(!$age_of_account) {
                $age_of_account = 0;
            } else {
                $age_of_account_splited = preg_split('/,/', $age_of_account);
                if(count($age_of_account_splited)) {
                    if(count($age_of_account_splited) > 1) {
                        $age_of_account =  $age_of_account_splited;
                    }
                }
            }

            $tweets_count = $user->ifUserHasConfigValue('number_of_tweets', $access_token_id);
            if(!$tweets_count) {
                $tweets_count = 0;
            } else {
                $tweets_count_splited = preg_split('/,/', $tweets_count);
                if(count($tweets_count_splited)) {
                    if(count($tweets_count_splited) > 1) {
                        $tweets_count =  $tweets_count_splited;
                    }
                }
            }

            foreach($user_search_keywords as $user_search_keyword) {
                $other_field = $user_search_keyword->get_other_fields();
                $query = $twitter->create_query(
                    $user_search_keyword->keyword,
                    $other_field['include'],
                    $other_field['exclude'],
                    $user_search_keyword->exact
                );
                $queryArgs = [
                    'min_followers' => $user_search_keyword->min_followers,
                    'max_followers' => $user_search_keyword->max_followers,
                    'max_id' => $user_search_keyword->max_id,
                    'age_of_account' => $age_of_account,
                    'tweets_count' => $tweets_count,
                    'lang' => $other_field['lang']
                ];
                $users = $twitter->search_users($query, $queryArgs);
                $count = 0;
                foreach($users['users'] as $twitter_user_id) {
                    if (!$user->isUserHasTwitterFollower($twitter_user_id, $access_token_id)) {
                        if (($max_daily_auto_follow_users_by_search
                            && $max_daily_auto_follow_users_by_search > $number_of_added_users->count) ||
                        !$max_daily_auto_follow_users_by_search) {
                            $date = DateTime::createFromFormat('!Y-m-d', $number_of_added_users->date);
                            $number_of_added_users->count += 1;
                            $number_of_added_users->save();
                            $count++;
                        } else {
                            $date = DateTime::createFromFormat('!Y-m-d', $number_of_added_users->date);
                            $date->modify('+1 days');
                            $number_of_added_users = new Number_of_added_users_twitter();
                            $number_of_added_users->date = $date->format('Y-m-d');
                            $number_of_added_users->setUserId($user_id);
                            $number_of_added_users->token_id = $access_token_id;
                            $number_of_added_users->count = 1;
                            $number_of_added_users->save();
                        }
                        $twitter_follower = new Twitter_follower();
                        $twitter_follower->setFollowerId($twitter_user_id);
                        $twitter_follower->setUserId($user_id);
                        $twitter_follower->setAccessTokenId($access_token_id);

                        $start_date = clone $date;

                        $start_date = $start_date->getTimestamp();
                        $start_date = $start_date + mt_rand(0,82800);
                        $start_date = new DateTime('@'.$start_date);

                        $end_date = clone $start_date;
                        $end_date = $end_date->modify('+1 days');
                        //$start_date->modify(($timezone_offset*-1).' hours');

                        //$end_date = clone $date;
                        //$end_date->modify(($timezone_offset*-1).' hours');

                        // if ($end_date <= $start_date) {
                        //     $end_date->modify('1 days');
                        // }

                        $twitter_follower->setStartFollowTime(
                            $user_search_keyword
                                ->getStartDateTime($start_date)
                                ->getTimestamp()
                        );
                        $twitter_follower->setEndFollowTime(
                            $user_search_keyword
                                ->getEndDateTime($end_date)
                                ->getTimestamp()
                        );
                        unset($start_date);
                        unset($end_date);
                        $twitter_follower->setNeedFollow(true);
                        $twitter_follower->save();
                        if($count > 5){
                            break;
                        }
                    }
                }
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                    .'By keywords '.$query.' add '.count($users['users']).' users.'."\n"
                    .$this->getDebugInfo($user, $access_token_id)
                );
                if($user_search_keyword->max_id != $users['max_id']) {
                    $user_search_keyword->max_id = $users['max_id'];
                } else {
                    $user_search_keyword->max_id = null;
                }
                $user_search_keyword->save();
            }
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage());
        }            
    }
    public function getLists($args)
    {
                // $social_group = new social_group(2);
                // $args = $social_group->access_token->where('type','twitter')->get()->all_to_array()[0];
                // $args['profile_id'] = 2;
        try {
            $user_id = (int)$args['user_id'];
            $profile_id = $args['profile_id'];
            $user = new User($user_id);
            $access_token_id = $args['id'];
            if (!$user->ifUserHasConfigValue('smart_engage', $access_token_id)) {
                return;
            }
            log_message('TASK_DEBUG', __FUNCTION__ . 'BEGINING'."\n"
                    .$this->getDebugInfo($user, $access_token_id));            
            $followers = $user->twitter_follower->where('access_token_id', $access_token_id)->order_by('id','random')->get()->all_to_array('follower_id');
            if(!empty($followers)){
                $twitter = $this->inicializeTwitterSocializer($user_id, $args);        
                $lists = $twitter->getLists($followers, 6);
                $suggested_list = new suggested_list();
                $suggested_list->to_db($lists, $user_id, $profile_id);
            }
            log_message('TASK_DEBUG', __FUNCTION__ . 'FINISH'."\n"
        .$this->getDebugInfo($user, $access_token_id));
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . $e->getMessage()."\n"
                    .$this->getDebugInfo($user, $access_token_id));
        }
        return true;
    }
    private function getDebugInfo($user, $access_token_id) {
        $info = [
            'User ID' => $user->id,
            'Username' => $user->username,
            'Token ID' => $access_token_id
        ];
        $text = '(';
        foreach($info as $key => $value) {
            $text.=$key.':'.$value.';';
        }
        $text.=')';
        return $text;
    }

    /**
     * @param $user_id
     * @param $token
     * @return Socializer_Twitter
     * @internal param $params
     */
    private function inicializeTwitterSocializer($user_id, $token) {
        $this->load->library('Socializer/socializer');
        /* @var Socializer_Twitter $twitter */
        $twitter = Socializer::factory('Twitter', $user_id, $token);
        return $twitter;
    }

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    protected $website_part = 'dashboard';

    public function __construct() {
        parent::__construct($this->website_part);
        $this->lang->load('dashboard', $this->language);
        $this->lang->load('social_create', $this->language);
        $this->lang->load('social_scheduled', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('dashboard', $this->language)
        ]);
        $this->template->set('breadcrumbs', false);
    }

    public function index() {

        if ($this->c_user->isTrialPlanEnds()) {
            $this->addFlash(lang('subscription_ends_error', [site_url('subscript/plans')]), 'error');
        }

        // UNCOMMENT TO USE

        // get average google rank for all keywords for chart in range

        $keyword_rank = Keyword::average_for_range($this->c_user->id, '-30 days', 'today'); // average result for all the range
        $keywords_trending = Keyword::average_for_range($this->c_user->id, '-30 days', 'today', FALSE); // average for each day in range


        // analytics data
    
        $google_access_token = Access_token::getByTypeAndUserId('googlea', $this->c_user->id);
        list( $ga_visits_chart , $ga_visits_count ) = $google_access_token->google_analytics_dashboard_visits();
    
        $review = new Review();
        $last_reviews_count = $review->last_period_count($this->c_user->id, $this->profile->id);
        $review->clear();

        $social_values = Social_value::inst();
        $social_values->set_values($this->c_user->id, $this->profile->id, array('from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d',time())));

        $all_socials_data = $social_values->get_data();

        $monthly_trending = array(
            'reviews' => $review->last_month_trending($this->c_user->id, $this->profile->id),
            'traffic' => $ga_visits_chart,
            'keywords' => $keywords_trending,
            'twitter_followers' => $all_socials_data['twitter'],
            'facebook_likes' => $all_socials_data['facebook'],
            
        );


        $keywordsForHighlight = Mention_keyword::inst()
            ->get_for_highlight($this->c_user->id, 0);
        CssJs::getInst()->add_js('www.google.com/jsapi', 'external', 'footer');
        CssJs::getInst()->add_js(array(
            'libs/lodash.compat.js',
            'libs/highcharts/highcharts.js',
            'libs/highcharts/highchart-theme.js'
        ))->c_js();

        $opportunities =  $this->getOpportunities();

        if (!empty($opportunities['web_radar'])) {
            CssJs::getInst()->add_js('controller/webradar/index.js');
        }

        JsSettings::instance()->add(array(
            'monthly_trending' => $monthly_trending,
            'dashboard' => true,
            'keywords' => $keywordsForHighlight,
            'opportunities' => $opportunities
        ));

        CssJs::getInst()
            ->c_js('settings/socialmedia', 'account_analytics');

        CssJs::getInst()->add_js(array(
            'libs/highcharts/highcharts.js'
        ));
        $access_tokens = array_filter(Access_token::getAllByUserIdAndProfileIdAsArray($this->c_user->id, $this->profile->id));
        //ddd($access_tokens);
        $analytics_const = array(
            "facebook" => Social_analytics::FACEBOOK_ANALYTICS_TYPE,
            "instagram" => Social_analytics::IGPOSTS_ANALYTICS_TYPE,
            // "linkedin"  => Social_analytics::LINKEDIN_ANALYTICS_TYPE,
            "twitter"   => Social_analytics::TWITTER_ANALYTICS_TYPE,
            "pinterest" => Social_analytics::PINTEREST_ANALYTICS_TYPE 
            );
        foreach ($access_tokens as $access_token) {
            $accessToken = new Access_token($access_token[0]['id']);
            echo "HALA : ".$access_token[0]['type'];
            $post_data[$access_token[0]['type']] = $accessToken
                ->social_analytics
                ->by_type($analytics_const[$access_token[0]['type']])
                ->get()->all_to_array();
        }
        foreach ($post_data as $social => $value) {
            $buffer = 0;
            foreach ($value as $_value) {
                //d($_value['value']);
                $total = $buffer + $_value['value'];
            }
            //d($total);
            $count[$social] = $total;
            unset($total);
        }
        

        if( ! $this->_check_access('twitter')) {
            $this->template->set('socializer_error', 'Twitter not connected. <a class="configure-fblink" href="' . site_url('settings/socialmedia') . '">Do it now</a>.');
            $this->template->render();
        } else {
            $access_tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray('twitter', $this->c_user->id, $this->profile->id);
            if(isset($_GET['token_id'])) {
                $token = new Access_token($_GET['token_id']);
                $token = $token->to_array();
            } else {
                $token = $access_tokens[0];
            }
            $this->load->library('Socializer/socializer');
            $twitter = Socializer::factory('Twitter', $this->c_user->id, $token);
            try {
                $prdata = $twitter->get_user_full_info($token['username']);
            } catch (Exception $e) {
                $prdata->favourites_count = 0;
                $prdata->followers_count = 0;
                $prdata->friends_count = 0;
                $prdata->statuses_count = 0;
                $this->addFlash('Twitter API is under high load at the moment, some app feautres might not work properly', 'error'); 
            }
            
            // if(empty($prdata->followers_count) && empty($prdata->favourites_count) && empty($prdata->friends_count) && empty($prdata->statuses_count)){
            //     $this->addFlash(lang('twitter-blocked-error'), 'error');                        
            // }
            $this->template->set('total_favorites', $prdata->favourites_count);
            $this->template->set('total_followers', $prdata->followers_count);
            $this->template->set('total_following', $prdata->friends_count);
            $this->template->set('total_tweets', $prdata->statuses_count);            
            $this->template->set('token', $token);
            $this->template->set('access_tokens', $access_tokens);

            CssJs::getInst()->c_js('twitter/activity', 'twitter');
        }


        $this->isSupportScheduledPosts = $this->getAAC()->isGrantedPlan('scheduled_posts');
        $this->load->helper('my_url_helper');
        $this->template->set('isSupportScheduledPosts', $this->isSupportScheduledPosts);
        $this->template->set('socials', Social_post::getActiveSocials($this->profile->id));
        $this->template->set('isSupportStats',$this->getAAC()->planHasFeature('view_stats'));

        // $this->is_user_set_timezone = User_timezone::is_user_set_timezone($this->c_user->id);
        // JsSettings::instance()->add(
        //     array(
        //         'twitterLimits' => array(
        //             'maxLength' => 140,
        //             'midLength' => 117,
        //             'lowLength' => 94
        //         ),
        //         'twitterLimitsText' => lang('twitter_error'),
        //         'linkedinLimits' => array(
        //             'maxLength' => 400,

        //         ),
        //         'linkedinLimitsText' => lang('linkedin_error'),
        //     )
        // );
        // CssJs::getInst()->add_css(array(
        //     'custom/pick-a-color-1.css'
        // ));
        // CssJs::getInst()->add_js(array(
        //     /*'ui/jquery.ui-1.9.2.min.js',*/
        //     'libs/jq.file-uploader/jquery.iframe-transport.js',
        //     'libs/jq.file-uploader/jquery.fileupload.js',
        //     'libs/fabric/fabric.min.js',
        //     'libs/fabric/StackBlur.js',
        //     'libs/color/tinycolor-0.9.15.min.js',
        //     'libs/color/pick-a-color-1.2.3.min.js'
        // ));

        // CssJs::getInst()->c_js('social/create', 'post_update');
        // CssJs::getInst()->c_js('social/create', 'post_cron');
        // CssJs::getInst()->c_js('social/create', 'post_attachment');
        // CssJs::getInst()->c_js('social/create', 'social_limiter');
        // CssJs::getInst()->c_js('social/create', 'schedule_block');
        // CssJs::getInst()->c_js('social/create', 'bulk_upload');

        // $this->template->set('is_user_set_timezone', User_timezone::is_user_set_timezone($this->c_user->id));

        // $user_posts = Social_post::inst()->get_user_scheduled_posts($this->c_user->id, $this->profile->id, 1, 3, 'all');

        // $this->template->set('posts', $user_posts);

        // $this->load->helper('Image_designer_helper');
        // $this->template->set('imageDesignerImages', Image_designer::getImages());
        $this->template->set('summary', $summary);
        $this->template->set('opportunities', $opportunities);
        //$this->template->set('need_welcome_notification',  User_notification::needShowNotification($this->c_user->id, User_notification::WELCOME));
        $this->template->set('count', $count);
        $this->template->render();
    }

    /**
     * Return opportunities for dashboard
     *
     * @return array
     */
    protected function getOpportunities()
    {
        $aac = $this->getAAC();

         $opportunities = array(
            'twitter' => $aac->isGrantedPlan('social_activity'),
            'facebook' => $aac->isGrantedPlan('social_activity'),
            'pinterest' => $aac->isGrantedPlan('social_activity'),
            'linkedin' => $aac->isGrantedPlan('social_activity'),
            // 'instagram' => $aac->isGrantedPlan('social_activity'),
            'web_traffic' => $aac->isGrantedPlan('website_traffic_monitoring'),
            'web_radar' => $aac->planHasFeature('brand_reputation_monitoring'),
            'reviews' => $aac->isGrantedPlan('reviews_monitoring'),
            'google_rank' => $aac->isGrantedPlan('local_search_keyword_tracking'),
        );

        $opportunities['summary'] = (
            $opportunities['twitter'] ||
            $opportunities['facebook'] ||
            $opportunities['pinterest'] ||
            $opportunities['linkedin'] ||
            $opportunities['reviews'] ||
            $opportunities['web_traffic']
        );

        $opportunities['trends'] = (
            $opportunities['twitter'] ||
            $opportunities['facebook'] ||
            $opportunities['pinterest'] ||
            $opportunities['linkedin'] ||
            $opportunities['web_traffic'] ||
            $opportunities['google_rank'] ||
            $opportunities['reviews']
        );

        return $opportunities;
    }
    private function _check_access( $type ) {
        $tokens = Access_token::inst()->get_by_type( $type, $this->c_user->id, $this->profile->id );
        if(empty($tokens)) {
            return false;
        } else {
            return true;
        }
    }    
}
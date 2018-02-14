<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Charts extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('twitter_tools', $this->language);
        $this->lang->load('socialmedia', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('socialmedia', $this->language)
        ]);
    }

	public function index(){
        CssJs::getInst()
            ->c_js('settings/socialmedia', 'index')
            ->c_js('settings/socialmedia', 'twitter')
            ->add_js(array(
                'masonry-docs.min.js',
                'masonry.pkgd.min.js'
            ))
            ->add_css(array('//fonts.googleapis.com/css?family=Roboto'),'external');		
	    if ($this->template->is_ajax()) {
	        $id = $this->input->post('access_token_id');
	        $period = $this->input->post('period');
	        $to = new DateTime('UTC');
	        $from = new DateTime('UTC');
	        $from->modify('-'.$period);
	        $token = Access_token::inst($id);
	        $data = $token
	            ->social_analytics
	            ->get_by_period($from->format('Y-m-d'), $to->format('Y-m-d'))
	            ->all_to_array();
	        $answer = [
	            Social_analytics::TWITTER_ANALYTICS_TYPE => [],
	            Social_analytics::TWITTER_RETWEET_COUNT_ANALYTICS_TYPE => [],
	            Social_analytics::TWITTER_FAVOURITE_COUNT_ANALYTICS_TYPE => [],
	            Social_analytics::RETWEETS_ANALYTICS_TYPE => [],
	            Social_analytics::FAVOURITES_ANALYTICS_TYPE => [],
	            Social_analytics::NEW_FOLLOWING_ANALYTICS_TYPE => [],
	            Social_analytics::NEW_UNFOLLOWERS_ANALYTICS_TYPE => [],
	            Social_analytics::NEW_FOLLOWING_BY_SEARCH_ANALYTICS_TYPE => [],
	            Social_analytics::NEW_UNFOLLOWING_ANALYTICS_TYPE => [],
	            Social_analytics::MENTION_ANALYTICS_TYPE => []

	        ];
	        foreach($data as $el) {
	            $answer[$el['type']][$el['date']] = $el['value'];
	        }

	        $social_values = Social_value::inst();
	        $social_values->set_values($this->c_user->id, $this->profile->id, array(
	            'from' => $from->format('Y-m-d'),
	            'to' => $to->format('Y-m-d'),
	            'type' => 'twitter'
	        ));
	        $answer['followers'] = $social_values->get_data()['twitter'];
	        unset($answer['followers']['']);

	        echo json_encode($answer);
	    }	
	}
}
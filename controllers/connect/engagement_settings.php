<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Engagement_settings extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('twitter_tools', $this->language);
        $this->lang->load('socialmedia', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('socialmedia', $this->language)
        ]);
    }

    /**
     * Used to show Social Media Settings Page
     * site_url/settings/socialmedia
     * Show Social-connect buttons
     *
     * @access public
     * @return void
     */
    public function edit($id) {
        $token = Access_token::inst($id);
        if ($token->user_id != $this->c_user->id) {
            $this->addFlash(lang('account_owner_error'), 'error');
            redirect('settings/socialmedia');
        }
        if (!$this->profile->has_account($id)) {
            redirect('settings/socialmedia');
        }
        $available_configs = Available_config::getByTypeAsArray($token->type, []);
        if ($this->input->post()) {
            $errors = array();
            $configs = $this->input->post('config');
            foreach($available_configs as $available_config) {
                $config_key = $available_config['key'];
                $value = isset($configs[$config_key]) ? $configs[$config_key] : '';
                $userConfig = $this->c_user->setConfig($config_key, ($value == 'on') ? true : $value, $token->id);
                if (!$userConfig) {
                    $error_message = preg_replace('|<p>|', '', $userConfig->error->string);
                    $error_message = preg_replace('|</p>|', '<br>', $error_message);
                    $errors[] = $error_message;
                }
            }
            if ($token->type == 'facebook') {
                try {
                    if( $this->input->post('page_group') == '0') {
                        throw new Exception(lang('fanpage_error'));
                    }

                    $this->load->library('Socializer/socializer');
                    /* @var Socializer_Facebook $facebook */
                    $facebook = Socializer::factory('Facebook', $this->c_user->id, $token->to_array());
                    $userdata = $facebook->get_profile();
                    Facebook_Fanpage::inst()->save_selected_page(
                        $this->c_user->id,
                        $this->input->post('page_group'),
                        $userdata['id'],
                        $token->id
                    );
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            if ($token->type == 'pinterest') {
                try {
                    if( $this->input->post('board_group') == '0') {
                        throw new Exception(lang('board_error'));
                    }
                    //$data = explode("_", $this->input->post('board_group'));
                    Pinterest_Board::inst()->save_selected_board(
                        $this->c_user->id,
                        $this->input->post('board_group'),
                        $token->id
                    );
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }            
            if(empty($errors)) {
                $this->addFlash(lang('config_save_success'), 'success');
                redirect('connect/engagement_settings/edit/'.$id);
            } else {
                $this->addFlash(implode('',$errors), 'error');
            }
        }
        if ($token->type == 'facebook') {
            try {
                $this->load->library('Socializer/socializer');
                /* @var Socializer_Facebook $facebook */
                $facebook = Socializer::factory('Facebook', $this->c_user->id, $token->to_array());
                $user_facebook_pages = $facebook->get_user_pages();
                $pages = $user_facebook_pages;

                $selected_fanpage = Facebook_Fanpage::inst()->get_selected_page($this->c_user->id, $token->id);
                $selected_fanpage_id = $selected_fanpage->fanpage_id;


                $this->template->set('pages', $pages);
                $this->template->set('selected_fanpage_id', $selected_fanpage_id);
            } catch (Exception $e) {
                if ($e->getCode() !== Socializer::FBERRCODE) {
                    $this->addFlash($e->getMessage());
                }
            }
        }
        if ($token->type == 'pinterest') {
            try {
                $this->load->library('Socializer/socializer');
                /* @var Socializer_Facebook $facebook */
                $pinterest = Socializer::factory('Pinterest', $this->c_user->id, $token->to_array());
                $user_pinterest_boards = $pinterest->get_boards();
                $boards = $user_pinterest_boards->toArray();

                $selected_board = Pinterest_board::inst()->get_selected_board($this->c_user->id, $token->id);
                $selected_board_id = $selected_board->board_id;

                $this->template->set('boards', $boards['data']);
                $this->template->set('selected_board_id', $selected_board_id);
            } catch (Exception $e) {
                if ($e->getCode() !== Socializer::FBERRCODE) {
                    $this->addFlash($e->getMessage());
                }
            }
        }      

        $access_token = new Access_token();
        $access_token = $access_token->getByTypeAndUserIdAndProfileId('twitter',$this->c_user->id,$this->profile->id);
        $this->load->library('Socializer/socializer');
        $twitter = Socializer::factory('Twitter', $this->c_user->id, $access_token->to_array());        
        $_locations = $twitter->get_available_locations();
        foreach ($_locations as $location) {
            if($location->name == 'Worldwide'){
                $locations[(int)$location->woeid] =  $location->name;  
            } else {
                $locations[(int)$location->woeid] = $location->name.', '.$location->country;
            }
        }
        //ddd($locations);

        $not_display_configs = [
            'welcome_message_text',


            'days_before_unfollow',

            'auto_favourite_min_favourites_count',
            'auto_favourite_max_favourites_count',
            'auto_favourite_min_retweets_count',
            'auto_favourite_max_retweets_count',

            'auto_retweet_min_favourites_count',
            'auto_retweet_max_favourites_count',
            'auto_retweet_min_retweets_count',
            'auto_retweet_max_retweets_count',

            'max_daily_auto_follow_users_by_search',
            'auto_follow_users_by_search',
            'age_of_account',
            'number_of_tweets',
            'auto_favourite',
            'retweet_quote',
            'mention_quote',
            'mention_website'
        ];
        $not_display_configs_values = [];
        foreach($available_configs as &$available_config) {
            if(in_array($available_config['key'], $not_display_configs)) {
                $not_display_configs_values[$available_config['key']] = [
                    'value' => $this->c_user->ifUserHasConfigValue($available_config['key'], $id),
                    'type' => Config::getConfigType($available_config['key'])
                ];
            }
            $available_config['value'] = $this->c_user->ifUserHasConfigValue($available_config['key'], $id);
            $available_config['type'] = Config::getConfigType($available_config['key']);
        }
        $this->template->set('locations', $locations);
        $this->template->set('available_configs', $available_configs);
        $this->template->set('not_display_configs', $not_display_configs);
        $this->template->set('not_display_configs_values', $not_display_configs_values);

        CssJs::getInst()
            ->add_js(array(
                'masonry-docs.min.js',
                'masonry.pkgd.min.js'
            ));

        $this->template->set('token', $token);
        $this->template->render();
    }
}
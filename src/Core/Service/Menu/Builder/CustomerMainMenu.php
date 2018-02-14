<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 11:44
 */

namespace Core\Service\Menu\Builder;

use Core\Service\Menu\MenuBuilder;
use Knp\Menu\MenuItem;

class CustomerMainMenu extends MenuBuilder
{
    public function build()
    {
        $menu = $this->getMenuFactory()->createItem('Customer Main Menu');

        $menu->addChild(lang('dashboard'), array(
            'path' => 'dashboard',
            'icon_class' => 'ti-panel',
        ));


        if ($this->getAAC()->isGrantedPlan('social_activity')) {
            $menu->addChild('Twitter', array(
                'path' => 'twitter/create',
                'icon_class' => 'ti-home',
            ));
            $menu['Twitter']->addChild('Create', array('path' => 'twitter/create'));
            $menu['Twitter']->addChild('Campaigns', array('path' => 'twitter/campaign'));
            $menu['Twitter']->addChild('Smart Posts', array('path' => 'twitter/smart_post'));
            if ($this->getAAC()->isGrantedPlan('scheduled_posts')) {
                $menu['Twitter']->addChild(lang('social_media_scheduled_posts'), array('path' => 'twitter/scheduled'));
                //$menu['Twitter']->addChild(lang('social_media_cron_posts'), array('path' => 'social/cron_posts'));
            }

        }

        $access_token = new \Access_token();
        $user_id = $access_token->load->_ci_cached_vars['c_user']->stored->id;
        $profile_id = $access_token->load->_ci_cached_vars['active_profile']->stored->id;
        $access_tokens = $access_token->getAllByTypeAndUserIdAndProfileIdAsArray('twitter', $user_id, $profile_id);

        $menu->addChild('Connect', array(
            'path' => 'connect',
            'icon_class' => 'fa fa-plug',
        )); 
        if(!empty($access_tokens[0]['id'])){       
            $menu['Connect']->addChild('Engagement Settings', array('path' => 'connect/engagement_settings/edit/'.$access_tokens[0]['id']));
            // $menu['Connect']->addChild('Follow Keywords', array('path' => 'connect/user_search_keywords'));
            $menu['Connect']->addChild('Smart Engage', array('path' => 'connect/engage'));
        }
        //New menu if granted by plan must have if
        if ($this->getAAC()->isGrantedPlan('animated_tweet') || $this->getAAC()->isGrantedPlan('parallax_tweet')) {
            $menu->addChild('Engage', array(
                'path' => 'engage',
                'icon_class' => 'fa fa-magnet',
            ));  
        }  
        if ($this->getAAC()->isGrantedPlan('animated_tweet')) {
            $menu['Engage']->addChild('Animated Tweet', array('path' => 'engage/gif')); 
        }
        if ($this->getAAC()->isGrantedPlan('parallax_tweet')) {
        $menu['Engage']->addChild('Parallax Video', array('path' => 'engage/parallax/update'));        
        }


        if ($this->getAAC()->isGrantedPlan('monetize')) {
            $menu->addChild('Monetize', array(
                'path' => 'monetize',
                'icon_class' => 'fa fa-money',
            ));  
            $menu['Monetize']->addChild('Monetize Tweet', array('path' => 'monetize/post'));
            $menu['Monetize']->addChild('Smart Campaigns', array('path' => 'monetize/automatic'));
            $menu['Monetize']->addChild('Smart Posts', array('path' => 'monetize/smart_post'));
        }

        $menu->addChild(lang('analytics'), array(
            'path' => 'traffic',
            'icon_class' => 'fa fa-bar-chart'
        ));
        $menu[lang('analytics')]->addChild('Charts', array('path' => 'graphics/charts/index/'.$access_tokens[0]['id']));





        $menu->addChild(lang('settings'), array('path' => 'settings/personal', 'icon_class' => 'ti-settings'));
        $menu[lang('settings')]->addChild(lang('settings_personal_settings'), array('path' => 'settings/personal'));

        $menu[lang('settings')]->addChild(lang('settings_my_profiles'), array('path' => 'settings/profiles'));

        if ($this->getAAC()->isGrantedPlan('reviews_monitoring')) {
            $menu[lang('settings')]->addChild(lang('settings_directory_settings'), array('path' => 'settings/directories'));
        }


        if ($this->getAAC()->isGrantedPlan('local_search_keyword_tracking')) {
            $menu[lang('settings')]->addChild(lang('settings_google_places_keywords'), array('path' => 'settings/keywords'));
        }

        if ($this->getAAC()->isGrantedPlan('social_activity') ||
            $this->getAAC()->planHasFeature('brand_reputation_monitoring')
        ) {
            $menu[lang('settings')]->addChild(lang('settings_social_media'), array('path' => 'settings/socialmedia'));

            if($this->getAAC()->planHasFeature('monetize')) {
                $menu[lang('settings')]->addChild('Affiliate Programs', array('path' => 'settings/affiliates'));
            }

            if ($this->getAAC()->planHasFeature('brand_reputation_monitoring')) {
                $menu[lang('settings')]->addChild(lang('settings_social_keywords'), array('path' => 'settings/mention_keywords'));
            }

        }
         $menu[lang('settings')]->addChild('Twitter API', array('path' => 'settings/api_settings'));
        if ($this->getAAC()->isGrantedPlan('website_traffic_monitoring')) {
            $menu[lang('settings')]->addChild(lang('settings_analytics'), array('path' => 'settings/analytics'));
            $menu[lang('settings')]->addChild(lang('settings_piwik'), array('path' => 'settings/piwik'));
        }


        if ($this->getAAC()->isGrantedPlan('social_activity') &&
            $this->getAAC()->isGrantedPlan('social_media_management')
        ) {
            //$menu[lang('settings')]->addChild(lang('settings_rss'), array('path' => 'settings/rss'));
        }

        if ($this->getAAC()->isGrantedPlan('collaboration_team')) {
            $menu[lang('settings')]->addChild(lang('settings_collaboration_team'), array('path' => 'settings/collaboration'));
        }

        if (!get_instance()->ion_auth->is_manager() &&
            !get_instance()->ion_auth->getManagerCode()
            && $this->get('core.status.system')->isPaymentEnabled()
        ) {
            $menu[lang('settings')]->addChild(lang('settings_subscriptions'), array('path' => 'settings/subscriptions'));
        }

        return $this->customizeMenu($menu);
    }

    /**
     * Customize menu
     *
     * @param MenuItem $menu
     *
     * @return MenuItem
     */
    protected function customizeMenu(MenuItem $menu)
    {

        $menu->setChildrenAttribute('class', 'menu clearfix');
        $request = $this->get('core.request.current');

        foreach ($menu->getChildren() as $child) {
            //$child->setAttribute('class', $child->getAttribute('class'));
            // $uri = $request->getUri();
            // $itemUri = $child->getUri();
            // $linkClass = ($uri == $itemUri || stripos($uri, $itemUri) !== false) ? ' active' : '';
            $child->setLinkAttribute('class', 'sidebar_link ');
            if ($child->hasChildren()) {
                $cucurrent =false;
                foreach ($child->getChildren() as $subChild) {
                    //$subChild->setAttribute('class', 'sidebar_submenu_item '.$subChild->getAttribute('class'));
                    if($request->getUri() == $subChild->getUri()){
                        $subLinkClass = ' current';
                        $cucurrent = true;
                    } else {
                        $subLinkClass = '';
                    }
                    //$subChild->setLinkAttribute('class', 'sidebar_submenu_link'.$subLinkClass);
                }
                if($cucurrent){
                    $child->setChildrenAttribute('class', 'collapse in');
                } else {
                    $child->setChildrenAttribute('class', 'collapse');
                }

                // $openClass = (stripos($uri, $itemUri) === false) ? '' : 'active';
                //$child->setChildrenAttribute('class', 'sidebar_submenu '.$openClass);


                $child->setExtra('safe_label', true);
                $child->setLabel($child->getLabel().' <span class="arrow"></span>');
            }


        }


        return $menu;
    }
}

<?php

/**
 * Credentials for social systems (used at settings/socialmedia) page
 */
$config['twitter_cache_dir'] = '/cache'; 
$config['facebook'] = array(
    'appId' => '',
    'secret' => '',
    'redirect_uri' =>  site_url('settings/socialmedia/facebook_callback')
);

$config['twitter'] = array(
    'consumer_key' => '',
    'consumer_secret' => '',
    'auth_callback' => site_url('settings/socialmedia/twitter_callback')
);
$config['youtube'] = array(
    'client_id' => '',
    'secret' => '',
    'algorithm' => 'HMAC-SHA1',
    'redirect_uri' => site_url('settings/socialmedia/youtube_callback'),
    'developer_key' => ''
);
$config['instagram'] = array(
    'in_client_id' => '',
    'in_client_secret' => '',
    'redirect_uri' => site_url('settings/socialmedia/instagram_callback'),
    'feed_limit' => 10
);
$config['google'] = array(
    'client_id' => '',
    'secret' => '',
    'algorithm' => 'HMAC-SHA1',
    'redirect_uri' => site_url('settings/socialmedia/google_callback'),
    'developer_key' => ''
);
$config['google_auth'] = array(
    'client_id' => '',
    'secret' => '',
    'algorithm' => 'HMAC-SHA1',
    'redirect_uri' => site_url('auth/google_signup'),
    'developer_key' => ''
);
$config['google_login'] = array(
    'client_id' => '',
    'secret' => '',
    'algorithm' => 'HMAC-SHA1',
    'redirect_uri' => site_url('auth/google_login'),
    'developer_key' => ''
);
$config['linkedin'] = array(
    'appKey' => '',
    'appSecret' => '',
    'redirect_uri' => site_url('settings/socialmedia/linkedin_callback')
    
);
$config['pinterest'] = array(
    'app_id' => '',
    'app_secret' => '',
    //'redirect_uri' => str_replace('http', 'https', site_url('settings/socialmedia/pinterest_callback'))
    //'redirect_uri' => 'https://app.autopixar.com/settings/socialmedia/pinterest_callback'
    'redirect_uri' =>  site_url('settings/socialmedia/pinterest_callback')
);
$config['base_url'] = "https://app.autopixar.com/";
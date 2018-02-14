<?php 
//use ColorThief\ColorThief;
defined('BASEPATH') OR exit('No direct script access allowed');

class trivia extends MY_Controller
{
    public function __construct(){
        parent::__construct();

    }

    public function index(){
    }

    public function go($campaign_id, $item_id){

    	$social_campaign = new social_campaign();
    	$campaign = $social_campaign->where('id', $campaign_id)->get();
    	$item = $campaign->social_campaigns_item->where('id',$item_id)->get();
    	$trivia = new social_trivia($item->social_trivia_id);

    	$campaign = $campaign->to_array();
    	$item = $item->to_array();
    	$image = site_url('public').'/uploads/'.$campaign['user_id'].'/'.$item['final_image_thumb'];
        
        $map = json_decode(file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/trivia/data.json"));   
        foreach ($map->templates->folders as  $key => $folder) {
        	//d($folder , $trivia->category);
            if($folder->name == $trivia->category){
                $default_description = $map->templates->folders[$key]->description;
            }
        }    	

        $ans[] = $trivia->answer1;
        $ans[] = $trivia->answer2;
        $ans[] = $trivia->answer3;
        $ans[] = $trivia->correct; 
        shuffle($ans);

        JsSettings::instance()->add([
            'correct' => $trivia->correct,
            'redirect' => $item['external_url'],
        ]);

        $trivia = $trivia->to_array();
        $user = new user($campaign['user_id']);
        $this->template->set('company', !empty($user->company)?$user->company:null);        
        $this->template->set('click_continue', $campaign['click_continue']);
        $this->template->set('options', $ans);
        $this->template->set('url', site_url('trivia')."/".$campaign->id."/".$item_id);
    	$this->template->set('trivia', $trivia);
    	$this->template->set('image', $image);
    	$this->template->set('external_url', $item['external_url']);
    	$this->template->set('description',$item['url_text']);
    	$this->template->set('default_description',$default_description);
    	$this->template->render('visitor');
    }
    public function preview($campaign_id){

        $social_campaign = new social_campaign();
        $campaign = $social_campaign->where('id', $campaign_id)->get();
        $items = $campaign->social_campaigns_item->get();

        $campaign = $campaign->to_array();
        $item = $items->all_to_array()[0];
        $image = site_url('public').'/uploads/'.$campaign['user_id'].'/'.$item['final_image_thumb'];
        
        $user = new user($campaign['user_id']);

        $this->template->set('company', !empty($user->company)?$user->company:null);
        $this->template->set('campaign', $campaign);
        $this->template->set('items', $items->all_to_array());
        $this->template->set('image', $image);
        $this->template->set('description',$item['url_text']);
        $this->template->render('visitor');
    }
}
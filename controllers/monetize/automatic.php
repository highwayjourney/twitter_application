<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Automatic extends MY_Controller {

    const POSTS_COUNT = 5;

    public function __construct() {
        parent::__construct();
        $this->is_user_set_timezone = User_timezone::is_user_set_timezone($this->c_user->id);
        if($this->is_user_set_timezone)
        {    
          //code added for Underscore, Backbone and Marionette
            CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js','external');
            CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/backbone.js/1.3.3/backbone-min.js','external');
            CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/backbone.marionette/2.4.5/backbone.marionette.min.js','external');        
            CssJs::getInst()->add_js(array('libs/bootstrap-toggle.js', 'libs/tagIt.js'));
            CssJs::getInst()->add_css(array('bootstrap-toogle.css','tagItz.css','tagIt.css'));
            CssJs::getInst()->c_js('twitter/campaign', 'app');        
        }

    }

    public function index() {
        CssJs::getInst()->c_js('twitter/campaign', 'index');
        JsSettings::instance()->add(array(
            'type' => 'affiliate'
        ));          
        $this->template->set('twitter/campaign');
        $this->template->set('is_user_set_timezone', User_timezone::is_user_set_timezone($this->c_user->id));
        $this->template->render();
    }

    // public function collection(){ 
    //     if ($this->template->is_ajax()) {
    //         $campaigns = Campaigns::inst();  
    //         $campaigns = $campaigns->get_user_campaigns($this->c_user->id, $this->profile->id, "affiliate");
    //         foreach ($campaigns as $_campaign) {
    //              $args = $_campaign->to_array();
    //              $_campaigns[] = $args; 
    //         }
    //         echo json_encode($_campaigns);
    //     }
    // }
   
   public function save(){
        if ($this->template->is_ajax()) {
            $stream = json_decode(file_get_contents("php://input"));
            $campaigns = Campaigns::inst();
            $stream->user_id = $this->c_user->id;
            $stream->profile_id = $this->profile->id;
            $stream->timezone = User_timezone::get_user_timezone($this->c_user->id);
            $stream->type = "affiliate";
            $result = $campaigns->todb($stream);
            echo json_encode($result);
        }
   }

    public function delete_campaign() {
        //if ($this->template->is_ajax()) {
            $campaign_id = $this->input->get();
            //var_dump($campaign_id); die();
            if(Campaigns::inst()->_delete( (int)$campaign_id["id"], $this->c_user->id )) {
                echo 'success '.$campaign_id["id"].' '.$this->c_user->id ;
            } else {
                $this->addFlash(lang('delete_campaign_error'));
            }
            //redirect('social/campaign');
        //}
    }

}
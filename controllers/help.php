<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends MY_Controller {
    public function __construct() {
        parent::__construct();
        //$this->template->layout = 'layouts/auth';
    }
    public function index()
    {
        CssJs::getInst()->add_js(array('libs/youtube-video-player/packages/perfect-scrollbar/perfect-scrollbar.js',
                                'libs/youtube-video-player/packages/perfect-scrollbar/jquery.mousewheel.js',
                                'libs/youtube-video-player/js/youtube-video-player.jquery.min.js')); 
        CssJs::getInst()->add_css(array('youtube-video-player/packages/perfect-scrollbar/perfect-scrollbar.css',
                                'youtube-video-player/packages/icons/css/icons.min.css',
                                'youtube-video-player/css/youtube-video-player.min.css'));   
        //CssJs::getInst()->add_single_js("$('#pm_video_list').youtube_video({playlist: 'PLI-QoAsgPlgxOZXgf51fxi4vnNd4ZqZgm'});", "inline");                                	
        $this->template->render();
    }    
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Autopixar extends MY_Controller {

    protected $is_user_set_timezone;

    public function __construct() {
        parent::__construct();

        $this->lang->load('social_create', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('social_create', $this->language)
        ]);

        $this->isSupportScheduledPosts = $this->getAAC()->isGrantedPlan('scheduled_posts');
        $this->load->helper('my_url_helper');
        $this->template->set('isSupportScheduledPosts', $this->isSupportScheduledPosts);

        $aac = $this->getAAC();
        
        if($aac->isGrantedPlan('pro_package')){
            $plan = "PRO";
            $templates[] = "PRO";
        }
        if($aac->isGrantedPlan('platinium_package')){
            $plan = "PLATINIUM";
            $templates[] = "PLATINIUM";            
        }
        if($aac->isGrantedPlan('diamond_package')){
            $plan = "PLATINIUM";
            $templates[] = "PLATINIUM"; 
        }    
        if($aac->isGrantedPlan('black_package')){
            $plan = "PLATINIUM";
            $templates[] = "PLATINIUM"; 
        }         


        $this->is_user_set_timezone = User_timezone::is_user_set_timezone($this->c_user->id);
        JsSettings::instance()->add(
            array(
                'twitterLimits' => array(
                    'maxLength' => 140,
                    'midLength' => 117,
                    'lowLength' => 94
                ),
                'twitterLimitsText' => lang('twitter_error'),
                'linkedinLimits' => array(
                    'maxLength' => 400,
                    
                ),
                'linkedinLimitsText' => lang('linkedin_error'),
                'user_tags'   => User_designs_tag::getUserTags($this->c_user->id),
                'user_id'   => $this->c_user->id,
                'plan'      => $plan,
                'templates' => $templates
            )
        );
        CssJs::getInst()->c_js('twitter/create', 'social_limiter');
        CssJs::getInst()->c_js('twitter/create', 'schedule_block');

    }
    public function index()
    {
        $aac = $this->getAAC();
        $hasGif = $aac->isGrantedPlan('minigif_creator')?true:false;
        $hasParallax = $aac->isGrantedPlan('parallax_creator')?true:false;
        $hasCloud = $aac->isGrantedPlan('cloud_storage')?true:false;
     
        CssJs::getInst()->c_js('twitter/autopixar', 'autopixar'); 


        $this->load->helper('Image_designer_helper');
        //$this->template->set('user_tags', User_designs_tag::getUserTags($this->c_user->id));
        $this->template->set('has_gif', $hasGif);
        $this->template->set('has_parallax', $hasParallax);
        $this->template->set('has_cloud', $hasCloud);

        //$this->template->set('imageDesignerImages', Image_designer::getImages());
        $this->template->set('socials', Social_post::getActiveSocials($this->profile->id));
        $this->template->set('is_user_set_timezone', User_timezone::is_user_set_timezone($this->c_user->id));
        $this->template->set('need_bulk_upload_notification',   User_notification::needShowNotification($this->c_user->id, User_notification::BULK_UPLOAD));

        $this->_attach_scripts();
        if($hasGif){
            CssJs::getInst()->add_js(array(
                    'libs/gif/gif.js',
                    'libs/gif/bm_gif_maker.js'
                ));
        }
        if($hasParallax){
            CssJs::getInst()->c_js('twitter/autopixar', 'LZWEncoder');
            CssJs::getInst()->c_js('twitter/autopixar', 'NeuQuant');
            CssJs::getInst()->c_js('twitter/autopixar', 'GIFEncoder');
        }        
        $this->template->render();
    }    

    public function pixabay(){
        $post = $this->input->post();
        //echo json_encode($post);
        if($this->template->is_ajax() && !empty($post)) {
            $pixabayClient = new \Pixabay\PixabayClient([
                'key' => '3500298-95c960fcb95285320b3778a38'
            ]);
            $results = $pixabayClient->get($post['obj'], true);    
            echo json_encode($results);        
        }
    }
    public function cloud($id = null){
        if($this->template->is_ajax()){
            //$urlUploadImages = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/assets/design-tool/user-designs/' . $this->c_user->id . '/';
            $urlUploadJson = dirname($_SERVER['SCRIPT_FILENAME']) . '/assets/user_designs/' . $this->c_user->id . '/';
            $urlUploadImages = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/assets/design-tool/data/user-designs/thumbs/' . $this->c_user->id . '/';
            $user_design = new User_design($id);
            $time = time();
            if(!empty($id)){
                unlink($urlUploadJson.$user_design->name.'.json');   
                unlink($urlUploadImages.$user_design->name.'.png'); 
                $time =  $user_design->name;   
            }        
            if(!is_dir($urlUploadImages)) {
                mkdir($urlUploadImages, 0755, TRUE);
            }
            if(!is_dir($urlUploadJson)) {
                mkdir($urlUploadJson, 0755, TRUE);
            }            
            $nameImage =  $time.'.png';
            move_uploaded_file($_FILES['thumb']['tmp_name'], $urlUploadImages . $nameImage); 
            $img = imagecreatefrompng($urlUploadImages . $nameImage);
            imageAlphaBlending($img, true);
            imageSaveAlpha($img, true);
            $answer = array(
                'success' => true
            );
            if ($img) {
                imagepng($img, $urlUploadImages . $nameImage, 0);
                imagedestroy($img);
            } else {
                $answer['success'] = false;
            }
            $nameJson =  $time.'.json';
            move_uploaded_file($_FILES['json']['tmp_name'], $urlUploadJson . $nameJson);

            $tags = json_decode($this->input->post()['tags']);
            $type = $this->input->post()['type'];
            //$user_design = new User_design($id);
            $user_design->newEntry($time, $this->c_user->id, $type);
            $answer['id'] = $user_design->newEntry($time, $this->c_user->id, $type);

            if(empty($id)){
                foreach ($tags as $tag) {
                    $user_tag = new User_designs_tag();
                    $user_tag->newEntry($tag);
                    $user_design->save($user_tag);
                }                
            }

            echo json_encode($answer);            
        }
    }

    public function getDesigns($page = 1){
        if($this->template->is_ajax()){  

            $post = $this->input->post();
            $user_design = new User_design();
            if(in_array('all', $post['inputs']['tags'])){
                if('all' == $post['inputs']['type']){
                    $paginate = $user_design->where('user_id', $this->c_user->id)->order_by('created', 'DESC')->get_paged($page, 9);
                } else {
                    //var_dump($post['inputs']['type']); die();
                    $paginate = $user_design->where('type', $post['inputs']['type'])->where('user_id', $this->c_user->id)->get_paged($page, 9);
                    //var_dump($user_design->all_to_array()); die();
                }   

            } else {

                $user_designs_tag = new User_designs_tag();
                $user_designs_tag->where_in('name', $post['inputs']['tags'])->get();

                foreach ($user_designs_tag->all_to_array() as  $value) {
                    $designs[] = $value['user_design_id'];
                }

                $user_design = new User_design();
                if('all' != $post['inputs']['type']){
                    $paginate = $user_design->where('user_id', $this->c_user->id)->where('type', $post['inputs']['type'])->where_in('id', $designs)->get_paged($page, 9);
                } else {
                     $paginate = $user_design->where('user_id', $this->c_user->id)->where_in('id', $designs)->get_paged($page, 9);
                }
            }

            $response = new stdclass;
            $pagination = new stdclass;
            $pagination->previous = $paginate->paged->previous_page;
            $pagination->next = $paginate->paged->next_page;
            $response->pagination = $pagination;
            $response->data = $paginate->all_to_array();
            echo json_encode($response);
        }     
    }

    public function youtube_search(){
        if($this->template->is_ajax()){    
            $post = $this->input->post();
            $this->load->library('Socializer/socializer');
            $youtube = Socializer::factory('Youtube',
            $this->c_user->id,
            $this->profile->getTokenByTypeAsArray('youtube')
            );
            $posts = $youtube->search($post['keyword'], $post['since']);
            echo json_encode($posts);             
            
        }        
    }

    public function deleteDesign($id= null){
        if($this->template->is_ajax()){ 
            $user_design = new User_design($id);
            $urlUploadJson = dirname($_SERVER['SCRIPT_FILENAME']) . '/assets/user_designs/' . $this->c_user->id . '/';
            $urlUploadImages = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/assets/design-tool/data/user-designs/thumbs/' . $this->c_user->id . '/';
            
            
            if(!empty($id)){
                unlink($urlUploadJson.$user_design->name.'.json');   
                unlink($urlUploadImages.$user_design->name.'.png');  
            }             
            $answer = array(
                'success' => true
            );            
            if($user_design->customDelete($id, $this->c_user->id)){
                echo json_encode($answer);
            } else {
                $answer['success'] = false;
                echo json_encode($answer);
            }
        }
    }

    public function getSlides(){
        if($this->template->is_ajax()){    
            $post = $this->input->post();
            $video_id = $post['video_id'];
            $begin = $post['begin'];
            $end = $post['end'];
            $frame_rate = $post['frame_rate'];
            $this->load->library('Socializer/socializer');
            $youtube = Socializer::factory('Youtube',
            $this->c_user->id,
            $this->profile->getTokenByTypeAsArray('youtube')
            );
            $youtube->createGiF($video_id, (int) $this->c_user->id, (int) $begin, (int) $end, (int) $frame_rate); 
            //echo $result;           
        }     
    }
    public function getGif(){
        $post = $this->input->post();
        if($this->template->is_ajax() && !empty($post['video_id']) && ($post['begin'] >= 0 ) && !empty($post['end'])){    
            $video_id = $post['video_id'];
            $begin = $post['begin'];
            $end = $post['end'];
            $frame_rate = $post['frame_rate'];
            $this->load->library('Socializer/socializer');
            $youtube = Socializer::factory('Youtube',
            $this->c_user->id,
            $this->profile->getTokenByTypeAsArray('youtube')
            );
            $youtube->createGiF($video_id, (int) $this->c_user->id, (int) $begin, (int) $end, (int) $frame_rate, true); 
            //echo $result;           
        } else {
            echo json_encode(array('success'  => false, 'message' => "Some paramaters are missing, please retry or contact support"));
        } 
    }

    private function _attach_scripts() {
        CssJs::getInst()->add_css(array(
            'custom/pick-a-color-1.css',
            'bootstrap-li-grid.css',
            'editor/index.css',
            'editor/bootstrap-slider.css',
            'editor/bootstrap-colorpicker.css',
            'editor/bm_gif_maker.css',
            'bootstrap-li-grid.css',
            'tagItz.css',
            'tagIt.css',
            'bootstrap-modal/bootstrap-modal-bs3patch.css',
            'bootstrap-modal/bootstrap-modal.css'
        ));
        CssJs::getInst()->add_js(array(
            'libs/jq.file-uploader/jquery.iframe-transport.js',
            'libs/jq.file-uploader/jquery.fileupload.js',
            'libs/fabric/fabric.min.js',
            'libs/fabric/StackBlur.js',
            'libs/tagIt.js',
            'libs/color/tinycolor-0.9.15.min.js',
            'libs/color/pick-a-color-1.2.3.min.js'
        ));
        CssJs::getInst()->c_js('twitter/create', 'post_update');
        CssJs::getInst()->c_js('twitter/autopixar', 'post_attachment');
        CssJs::getInst()->c_js('twitter/autopixar', 'editor2');
        CssJs::getInst()->c_js('twitter/autopixar', 'photomosaic');
        CssJs::getInst()->c_js('twitter/autopixar', 'zoom');
        CssJs::getInst()->c_js('twitter/autopixar', 'spinner');
        CssJs::getInst()->c_js('twitter/create', 'post_cron');
        CssJs::getInst()->c_js('twitter/create', 'bulk_upload');
        CssJs::getInst()->c_js('twitter/create', 'social_limiter');
        CssJs::getInst()->c_js('twitter/create', 'schedule_block');

        CssJs::getInst()->add_js('bootstrap-modal/bootstrap-modal.js');
        CssJs::getInst()->add_js('bootstrap-modal/bootstrap-modalmanager.js');
        //CssJs::getInst()->add_js('cdn.filesizejs.com/filesize.min.js','external');
        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/Sortable/1.4.2/Sortable.min.js','external');
        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.3.0/bootstrap-slider.min.js','external');
        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.6/js/bootstrap-colorpicker.min.js','external');
        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/webfont/1.6.26/webfontloader.js','external');
        //CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js','external');
    }    
}   
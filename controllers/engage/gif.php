<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Gif extends MY_Controller {



protected $website_part = 'dashboard';

    protected $is_user_set_timezone;





    const RSS_POSTS_COUNT = 5;

    

    public function __construct()

    {

        parent::__construct();

        $this->lang->load('social_create', $this->language);

        //$this->lang->load('engage_gif', $this->language);

        JsSettings::instance()->add([

            'i18n' => $this->lang->load('social_create', $this->language)

        ]);



        $supportCloak = $this->getAAC()->isGrantedPlan('cloak_link');

        $this->isSupportScheduledPosts = $this->getAAC()->isGrantedPlan('scheduled_posts');

        $this->load->helper('my_url_helper');

        $this->template->set('isSupportScheduledPosts', $this->isSupportScheduledPosts);

        $this->template->set('supportCloak', $supportCloak);



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

            )

        );       

        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js','external');

        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/backbone.js/1.3.3/backbone-min.js','external');

        CssJs::getInst()->add_js('cdnjs.cloudflare.com/ajax/libs/backbone.marionette/2.4.5/backbone.marionette.min.js','external');

        

        CssJs::getInst()->c_js('twitter/create', 'social_limiter');

        CssJs::getInst()->c_js('twitter/create', 'post_update');

        CssJs::getInst()->c_js('engage/gif', 'youtube_search');

        CssJs::getInst()->c_js('engage/gif', 'app');

        //CssJs::getInst()->c_js('social/create', 'post_attachment');

        // CssJs::getInst()->c_js('social/create', 'social_sources');

        // CssJs::getInst()->c_js('social/create', 'product_search');





    }



    public function index()

    {

        // if(!$this->maintenance_mode->check($this->input->ip_address())){

        //     $this->addFlash("We're sorry for the inconvenience, this section of SociBOOM is undergoing necessary scheduled maintenance.");

        //     //die("We're sorry for the inconvenience, this section of SociBOOM is undergoing necessary scheduled maintenance.");

        // }

        $this->_attach_update_scripts();

        $this->load->helper('Image_designer_helper');

        $affiliate_model = new affiliates_model();

        $affiliate_data = $affiliate_model->get_affiliate_info($this->c_user->id, $this->profile->id);





        $this->template->set('imageDesignerImages', Image_designer::getImages());

        $this->template->set('socials', Social_post::getActiveSocials($this->profile->id));

        $this->template->set('postSources', $affiliate_data);

        $this->template->set('is_user_set_timezone', User_timezone::is_user_set_timezone($this->c_user->id));

        $this->template->set('need_bulk_upload_notification',   User_notification::needShowNotification($this->c_user->id, User_notification::BULK_UPLOAD));

        $this->template->render();

    }



    /**

     * Create post action

     */

    public function post_create()

    {

        if ($this->template->is_ajax()) {

            $post = $this->input->post();



            if (!empty($post['posting_type']) &&

                $post['posting_type'] == 'schedule' &&

                !$this->isSupportScheduledPosts

            ) {

                $this->renderJson(array(

                    'errors' => array(

                        'when_post' => lang('when_post_error'),

                    ),

                ));

            }



            if(in_array('facebook', $post['post_to_socials'])) {

                $this->load->library('Socializer/socializer');

                /* @var Socializer_Facebook $facebook */

                $facebook = Socializer::factory('Facebook',

                    $this->c_user->id,

                    $this->profile->getTokenByTypeAsArray('facebook')

                );

                if(!$facebook->getFanpageId()) {

                    echo json_encode(array(

                        'success' => false,

                        'message' => lang('facebook_fanpage_error')

                    ));

                    exit();

                }

            }

            if($this->isDemo()) {

                echo json_encode(array(

                    'success' => false,

                    'message' => lang('demo_version_error')

                ));

                exit();

            }

            //var_dump($post['attachment_type']); die();

            //unset($post['attachment_type']);

            //var_dump($post); die();



            //Add permission 

            try{

                // if(!empty($post['url'])){ 

                //     $this->bitly_load();

                //     if ($this->bitly) {

                //         $bitly_data = $this->bitly->shorten($post['url']);

                //         if (strlen($bitly_data['url']) < strlen($post['url'])) {

                //             $post['url'] = $bitly_data['url'];

                //             //ddd($post['url']);

                //         }

                //     }

                // }

            } catch (Exception $e) {

                    echo json_encode(array(

                        'success' => false,

                        'message' => $e->getMessage()

                    ));        

                    exit();            

                }

            try {

                if(isset($post['is_cron'])) {

                    $this->add_cron_post($post);

                } else {

                    switch ($post['attachment_type']) {

                        case 'photos':

                            $this->post_photo($post);

                            break;

                        case 'videos':

                            $this->post_video($post);

                            break;

                        case 'image-designer':

                            $this->post_photo($post);

                            break;

                        default:

                            $this->post_link($post);

                            break;

                    }

                }

            } catch(Exception $e) {

                echo json_encode(array(

                    'success' => false,

                    'message' => $e->getMessage()

                ));

            }

        }

    }



    public function get_posts(){

        if ($this->template->is_ajax()) {

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



    public function youtube_gif(){

        //if ($this->template->is_ajax()) {

            $post = $this->input->post();

            $this->load->library('Socializer/socializer');

            $youtube = Socializer::factory('Youtube',

            $this->c_user->id,

            $this->profile->getTokenByTypeAsArray('youtube')

            );
            //var_dump($post); die();
            // $post['video_id'] = '8YseL7o17Eo';
            // $post['since'] = 8;
            // $post['until'] = 20;

            return $youtube->createGiF($post['video_id'], (int) $this->c_user->id, (int) $post['since'],(int) $post['until']);                           

        //}           

    }



    public function uploadImageDesignerFile() {

        if ($this->template->is_ajax()) {

            $post = $this->input->post();

            //$data = base64_decode($post['image_designer_data_url']);



            $urlUploadImages = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/uploads/' . $this->c_user->id . '/';

            if(!is_dir($urlUploadImages)) {

                mkdir($urlUploadImages, 0777, TRUE);

            }

            $nameImage = time() . '.gif';



            // $img = imagecreatefromstring($data);



            // imageAlphaBlending($img, true);

            // imageSaveAlpha($img, true);



            $answer = array(

                'success' => true,

                'image_name' => $nameImage

            );

            $decodedpic = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $post['image_designer_data_url']));

            file_put_contents($urlUploadImages . $nameImage, $decodedpic);



            echo json_encode($answer);

        }

    }



    /**

     * Post rss page

     */

    public function post_rss()

    {



        $post = $this->input->post();



        if (!empty($post['feed'])) {

            $feedId = $post['feed'];

        } else {

            $feeds = Rss_feed::inst()->user_feeds($this->c_user->id, $this->profile->id);

            $feedId = key($feeds);

        }



        $content = $this->getRssByFeedId($feedId);

        if ($this->template->is_ajax()) {

            echo json_encode(array(

                'success' => true,

                'html' => $content,

            ));

            exit;

        }

        $this->_attach_update_scripts();

        $data = $this->_check_socials_access();

        $this->_add_vars_to_template($data, array('feeds'=> $feeds, 'content' => $content));



        JsSettings::instance()->add('twitterDefaultType', 'midLength');



        $this->template->render();

    }



    public function redirect()

    {

        redirect('monetize/post');

    }



    public function update_notification() {

        if ($this->template->is_ajax()) {

            $post = $this->input->post();

            if(User_notification::setNotification($this->c_user->id, $post['notification'], $post['show'])) {

                echo json_encode([

                    'success' => true

                ]);

            } else {

                echo json_encode([

                    'success' => false,

                    'message' => lang('something_went_wrong')

                ]);

            }

        }

    }

    /**

     * Used to post a new link to socials

     * ('post a link' form action)

     *

     * @access public

     * @return void

     */

    public function post_link($post) {

        if ($this->template->is_ajax()) {

            $post['post_to_groups'] = array($this->profile->id);

            $post['title'] = 'from '.get_instance()->config->config['OCU_site_name'];

            $this->bitly_load();



            // add http://

            if (!empty($post['url']) && !preg_match("~^(?:f|ht)tps?://~i", $post['url'])) {

                $post['url'] = "http://" . $post['url'];

            }



            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);

            $errors = Social_post::validate_post($post);



            if (empty($errors)) {



                try {

                    if (!isset($post['post_id'])) {



                        if (!empty($post['url'])) {



                            if ($this->bitly) {

                                $bitly_data = $this->bitly->shorten($post['url']);

                                if (strlen($bitly_data['url']) < strlen($post['url'])) {

                                    $post['url'] = $bitly_data['url'];

                                    //ddd($post['url']);

                                }



                            }

                        }



                    }



                    $this->load->library('Socializer/socializer');

                    Social_post::add_new_post($post, $this->c_user->id, $this->profile->id);



                    $result['success'] = true;

                    $result['message'] = lang('post_was_successfully_added');

                } catch(Exception $e) {

                    $result['success'] = false;

                    $result['errors']['post_to_groups[]'] = '<span class="message-error">' . $e->getMessage() . '</span>';

                }



            } else {

                $result['success'] = false;

                $result['errors'] = $errors;

            }

            echo json_encode($result);

        }

        exit();

    }



    public function post_photo($post) {

        if ($this->template->is_ajax()) {

            $post['post_to_groups'] = array($this->profile->id);

            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);

            $errors = Social_post::validate_post($post);

            if (empty($errors)) {

                $this->load->library('Socializer/socializer');

                Social_post::add_new_post($post, $this->c_user->id, $this->profile->id);

                $result['success'] = true;

                $result['message'] = lang('post_was_successfully_added');

            } else {

                $result['success'] = false;

                $result['errors'] = $errors;

            }

            echo json_encode($result);

        }

        exit();

    }



    public function post_video($post) {

        if ($this->template->is_ajax()) {

            $post['post_to_groups'] = array($this->profile->id);

            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);

            $post['user_id'] = $this->c_user->id;

            $post['title'] = 'from '.get_instance()->config->config['OCU_site_name'];

            $errors = Social_post::validate_post($post);

            if (empty($errors)) {

                $this->load->library('Socializer/socializer');

                Social_post::post_video($post, $this->c_user->id, $this->profile->id);

                $result['success'] = true;

                $result['message'] = lang('post_was_successfully_added');

            } else {

                $result['success'] = false;

                $result['errors'] = $errors;

            }

            echo json_encode($result);

        }

        exit();

    }



    public function add_cron_post($post) {

        if ($this->template->is_ajax()) {

            $post['post_to_groups'] = array($this->profile->id);

            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);

            $post['user_id'] = $this->c_user->id;

            $errors = Social_post::validate_post($post);

            if (empty($errors)) {

                $errors = Social_post_cron::validate_cron($post);

                if(empty($errors)) {

                    Social_post_cron::add_new_post($post, $this->c_user->id, $this->profile->id);

                    $result['success'] = true;

                    $result['message'] = lang('post_was_successfully_added');

                } else {

                    $result['success'] = false;

                    $result['errors'] = $errors;

                }

            } else {

                $result['success'] = false;

                $result['errors'] = $errors;

            }

            echo json_encode($result);

        }

        exit();

    }



    /**

     * Check - is user have access to post into socials

     * Get Access Tokens for Facebook / Twitter from our database

     * Also need to check - is user select some Facebook fanpage

     *

     * @access private

     * @return array

     */

    private function _check_socials_access() {

        return Access_token::inst()->check_socials_access($this->c_user->id);

    }

    

    private function _attach_update_scripts() {

        CssJs::getInst()->add_css(array(

            'gif.creator/spectrum.css',

            'gif.creator/jquery.bootstrap-touchspin.min.css',

            'gif.creator/customcss.css',
			
			'gif.creator/slider.css',

            'bootstrap-li-grid.css'

        ));

        CssJs::getInst()->add_js(array(

            'libs/jq.file-uploader/jquery.iframe-transport.js',

            'libs/jq.file-uploader/jquery.fileupload.js',

            //'libs/gif.creator/jquery-3.1.0.min.js',

            //'libs/gif.creator/bootstrap.min.js',

            'libs/gif.creator/jquery.bootstrap-touchspin.min.js',

            'libs/gif.creator/spectrum.js',

            'libs/gif.creator/gifshot.js',

            'libs/gif.creator/fabric.js',

            'libs/gif.creator/giflogic.js'

        ));

        CssJs::getInst()->c_js('twitter/create', 'post_update');

    }



    /**

     * Add socials data and category id to template

     *

     * @access   public

     *

     * @param       $socials_data

     * @param array $params

     *

     * @internal param $category_slug

     */

    private function _add_vars_to_template($socials_data, $params = array()) {

        $this->template->set($socials_data);

        $this->template->set('is_user_set_timezone', $this->is_user_set_timezone);

        if (!empty($params)) {

            foreach ($params as $k=>$v) {

                $this->template->set($k, $v);

            }

        }

    }

    /**

     * Used to upload/delete campaign gallery images

     *

     * @access public

     * @return void

     */

    public function upload_images() {

        $this->load->library('MY_upload_handler');

        $upload_handler = new MY_upload_handler($this->c_user->id);

        header('Pragma: no-cache');

        header('Cache-Control: no-store, no-cache, must-revalidate');

        header('Content-Disposition: inline; filename="files.json"');

        header('X-Content-Type-Options: nosniff');

        header('Access-Control-Allow-Origin: *');

        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');

        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');



        switch ($_SERVER['REQUEST_METHOD']) {

            case 'OPTIONS':

                break;

            case 'HEAD':

            case 'GET':

                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {

                    $upload_handler->delete();

                } else {

                    $upload_handler->get();

                }

                break;

            case 'DELETE':

                if ($postId = $this->getRequest()->query->get('post_id', '')) {

                    $post = new Social_post($postId);

                    $post->media->delete_all();

                }

                $upload_handler->delete();

                break;

            case 'POST':

                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {

                    $upload_handler->delete();

                } else {

                    $upload_handler->post();

                }

                break;



            default:

                header('HTTP/1.1 405 Method Not Allowed');

        }

    }     

}
<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Socializer_Instagram {
    
    /**
     * Create a variable to hold the Oauth access token
     * @var string
     */
    private $device_id = FALSE;

    const MAX_DESCRIPTION_LENGTH = 2200;
    /**
     * Construct function
     * Sets the codeigniter instance variable and loads the lang file
     * @param integer|null $user_id
     * @param array|null $token
     */
    function __construct($user_id, $token) {
        //ddd();
        // Set the CodeIgniter instance variable
        $this->_ci =& get_instance();
        
        // Load the Instagram API language file
        $this->_ci->config->load('social_credentials');
        $this->_config = Api_key::build_config('instagram', $this->_ci->config->item('instagram'));
        //var_dump($this->_config);die;
        $this->_user_id = $user_id;
        $this->_token = $token;
        $this->device_id = $this->_token['token1'];
        $this->username = $this->_token['username'];
        if(!empty($this->device_id) && !empty($this->username)){
            $this->_instagram = new \InstagramAPI\Instagram($this->username, null, false, null, null, $this->device_id);
        } else {
            $this->_instagram = null;
        }

    } 
    
    
    /**
     * Used to get Instagram access token
     * After - return redirect url
     *
     * @access public
     *
     * @param $profile_id
     *
     * @return string
     * @throws OAuthException
     */
    public function add_new_account($profile_id, $username, $password) {
        if(!preg_match('/^[a-zA-Z0-9._]+$/', $username)){
            throw new Exception("Your Instagram username contains invalid Characters", 1);
        }
        $path = dirname(dirname(dirname(dirname(dirname( dirname(__FILE__))))))."/autopixar_ig_data/logs/failed_login/".$username."/";
        $path2 = dirname(dirname(dirname(dirname(dirname( dirname(__FILE__))))))."/autopixar_ig_data/".$username."/";

        $instagram = new \InstagramAPI\Instagram($username, $password);
        if($instagram->isLoggedIn){
            // if(!is_dir($path)) {
            //     mkdir($path, 0755, TRUE);
            // }        
            // $log = new KLogger($path."/log/", KLogger::INFO);
            // $log->logInfo("User ".$this->_user_id." already loggged, deleting data...");   

            unlink($path2."/".$username."-cookies.dat");
            unlink($path."/settings-".$username.".dat");
            $instagram = new \InstagramAPI\Instagram($username, $password);
        } 
            try {
                $loginResponse = $instagram->login(); 
            } catch (Exception $e) {
                if($e->getMessage() == "checkpoint_required"){
                    if(!is_dir($path)) {
                        mkdir($path, 0755, TRUE);
                    }        
                    $log = new KLogger($path, KLogger::INFO);
                    $log->logInfo("CheckPoint Required User ID: ".$this->_user_id." ".$e->getMessage());                    
                    throw new Exception("Please open Instagram in your phone and validate our Login Attempt.  Then try to login again", 1);
                }
                if($e->getMessage() == "The password you entered is incorrect. Please try again."){
                    throw new Exception("Please check your username/password combination", 1); 
                }
                if(!is_dir($path)) {
                    mkdir($path, 0755, TRUE);
                }        
                $log = new KLogger($path, KLogger::INFO);
                $log->logInfo("User ID: ".$this->_user_id." ".$e->getMessage());   
                throw new Exception("There was an Unknow Error, please try again or contact support", 1);                
            }           
            // if(!$loginResponse->isOk){
            //     if(!is_dir($path."/log/")) {
            //         mkdir($path."/log/", 0755, TRUE);
            //     }      
            //     $log = new KLogger($path."/log/", KLogger::INFO);
            //     $log->logInfo(serialize($loginResponse));             
            //     throw new Exception("There is an issue with your account, please contact Support" , 1);
            // }

            $access_token = new Access_token();
            $tokens = array(
                'token' => $instagram->device_id,
                'secret_token' => null,
                    'image' => $loginResponse->getProfilePicUrl(),
                    'name' => $loginResponse->getFullName(),
                    'username' => $username
                );
                $token = $access_token->add_token($tokens, 'instagram', $this->_user_id);
                $social_group = new Social_group($profile_id);
                $social_group->save(array('access_token' => $token));
   
    }

        public function createPost($data){
            if($this->_instagram->isLoggedIn){
                try{
                    $this->_instagram->uploadPhoto(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$data['image_name'], $data['description']);
                } catch (Exception $e){
                    $path = dirname(dirname(dirname(dirname(dirname( dirname(__FILE__))))))."/autopixar_ig_data/logs/failed_posts/".$this->username."/";
                    if(!is_dir($path)) {
                        mkdir($path, 0755, TRUE);
                    }        
                    $log = new KLogger($path, KLogger::INFO);
                    $log->logInfo("User: ".$this->_user_id." ".$e->getMessage());                      
                }
            } else {                 
                throw new Exception("There is an issue with your account, please try to recconect", 1);
        }
    }

}

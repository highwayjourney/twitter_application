<?php
use DirkGroenen\Pinterest\Pinterest;
/**
 * Pinterest service.
 *
 * @author Cesar Q. <cesarquintini@gmail.com>
 * 
 */
class Socializer_Pinterest {

    private $_ci;
    private $_config;
    private $_pinterest;
    private $_user_id;
    private $_base_url;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;

    const MAX_DESCRIPTION_LENGTH = 500; 

    /**
     * Method for get access to Pinterest
     *
     * @access public
     * @param $user_id id current user
     * @param array|null $token
     * @return Socializer_Pinterest
     */
    function __construct($user_id, $token) {
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');

        $this->_base_url = $this->_ci->config->item('base.url');
        $this->_config = Api_key::build_config('pinterest', $this->_ci->config->item('pinterest'));

        $this->_user_id = (string)$user_id;
        $this->_pinterest = new Pinterest($this->_config['app_id'], $this->_config['app_secret']);
        // ddd(Access_token::inst()->get_one_by_type('pinterest', $this->_user_id)->to_array(), $this->_config['app_id'], $this->_config['app_secret']);
        if (!$token) {
            $this->_token = Access_token::inst()->get_one_by_type('pinterest', $this->_user_id)->to_array();
        } else {
            $this->_token = $token;
            $this->_pinterest->auth->setOAuthToken($token);
        }
        if(!empty($token)){
            $this->_pinterest->auth->setOAuthToken($this->_token['token1']);
        }
    }
    /**
     * Method for get access to pinterest
     *
     * @access public
     * @return string
     */
    public function get_access(){
        $loginurl = $this->_pinterest->auth->getLoginUrl($this->_config['redirect_uri'], array('read_public', 'write_public'));
        return $loginurl;
    }
    /**
     * Method to get pinterest boards
     *
     * @access public
     * @return string
     */
    public function get_boards(){
        $boards = $this->_pinterest->users->getMeBoards(array("limit" => 100));
        return $boards;
    }    
    
    /**
     * Create access token for pinterest
     *
     * @access public
     * @return void
     */
    public function add_new_account($profile_id, $code){

        $token = $this->_pinterest->auth->getOAuthToken($_GET["code"]);
        $this->_pinterest->auth->setOAuthToken($token->access_token);
        $access_token = new Access_token();
        $profile = $this->_pinterest->users->me(array(
            'fields' => 'username,first_name,image[small]'
        ));
        //ddd($token->__get('access_token'), $profile->__get('access_token'));
        $tokens = array(
            'token' => $token->access_token,
            'data' => '',
            'name' => $profile->__get('first_name'),
            'username' => $profile->__get('username'),
            'image' => $profile->__get('image')['small']['url']
        );
        //d($token,$tokens);
        $token = $access_token->add_token($tokens, 'pinterest', $this->_user_id, $profile_id);
        $social_group = new Social_group($profile_id);
        //d($token);
        $social_group->save(array('access_token' => $token));
        redirect(site_url('settings/socialmedia/edit_account/'.$token->to_array()['id']));
        
    }
    
    public function get($id){
        $response = json_decode(file_get_contents("https://api.pinterest.com/v3/pidgets/pins/info/?pin_ids=".$id));
        $_response = new stdclass;
        $_response->repin_count = $response->data[0]->repin_count;
        $_response->like_count = $response->data[0]->like_count;
        return $_response;
    }

    /**
     * Creates post at pinterest
     *
     * @param array $data param of posting data
     * @return array
    */
    public function createPost($data, $selected_board){
        $flag = "";
        if(!empty($data['url'])){
            $note= $data['description']." ".$data['url'];          
        } else {
            $note= $data['description'];
        }
        try{
            if(!empty($data['image_name'])){
                $gif = false;
                if (preg_match("/\.gif$/", $data['image_name']))
                { 
                    $gif = true;
                }
                if($gif) {  
                    if(empty($data['url'])){
                        $data = array(
                            "note"          => $note,
                            "image_url"         => $this->_base_url.'public/uploads/'.$this->_user_id.'/'.$data['image_name'],
                            "board"         => $selected_board['board_id']
                        );                        
                    } else {
                        $data = array(
                            "note"          => $note,
                            "image_url"         => $this->_base_url.'public/uploads/'.$this->_user_id.'/'.$data['image_name'],
                            "board"         => $selected_board['board_id'],
                            "link"           => $data['url']
                        );  
                    }
                    $response = $this->_pinterest->pins->create($data);
                } else {
                    if(empty($data['url'])){
                        $data = array(
                            "note"          => $note,
                            "image"         => PUBPATH.'/uploads/'.$this->_user_id.'/'.$data['image_name'],
                            "board"         => $selected_board['board_id']
                        );
                    } else {
                        $data = array(
                            "note"          => $note,
                            "image"         => PUBPATH.'/uploads/'.$this->_user_id.'/'.$data['image_name'],
                            "board"         => $selected_board['board_id'],
                            "link"           => $data['url']
                        );
                    }
                    $response = $this->_pinterest->pins->create($data);                  
                }           
             
            } else {
                $flag = "rise";
                $response = $this->_pinterest->pins->create(array(
                    "note"          => $note,
                    "board"         => $selected_board['board_id']
                ));    
            }      
        } catch (Exception $e){
            return  (object) array('errors' => $e->getMessage().$rise); 
        } 
        return $response;
    }    
}


<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Social_sent_post extends DataMapper
{
    var $has_one = array('access_token');
    var $has_many = array();

    var $validation = array();

    var $table = 'social_sent_posts';
    var $created_field = 'date';
    var $cascade_delete = true;
        
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }
    public function getStatsbyGroup($user_id, $group_id){
    	$access_token = new Access_token;
    	$active_socials = $access_token->get_user_socials($user_id, $group_id);
    	$this->load->library('Socializer/socializer');
    	foreach ($active_socials as  $type) {
            //if($type == "facebook"){
    		$access_tokens = $access_token->get_by_type($type, $user_id, $group_id);
    		foreach ($access_tokens as $_access_token) {
    			$posts = $_access_token->social_sent_post->get()->all_to_array();
    			$buffer = array();
    			foreach ($posts as $post) {
			        $social= Socializer::factory($type, $user_id, $_access_token->to_array());
			        $post_data = $social->get($post['source_id']);
                    //d($post_data, $post['source_id']);
			        foreach ($post_data as $key => $property) {
			        	$arr[$key] = $buffer[$key] + $property;
			        	$buffer[$key] = $arr[$key];
			        }
    			}   
    			$data[$type][] = $arr;
    			unset($arr); 			
    		}
            //}
    	}

    	return $data;
    }

    public static function getLatestPosts($user_id, $type, $group_id){
        $access_token = $access_token->get_by_type($type, $user_id, $group_id);
        $where = array('user_id' => $user_id, 'access_token_id' => $access_token->id);
        unset($access_token);
        $user_posts = $this->where($where)
            ->order_by('id', 'DESC')
            ->get(500);  
        if(empty($user_posts)){
            $user_posts = array();
        }
        return $user_posts;              
    }

    public static function addPost($post_id, $access_token_id){
    	$add_post = new self();
    	$add_post->source_id = $post_id;
    	$add_post->access_token_id = $access_token_id;
    	$add_post->save();
    }

    public function delete_by_source($source_id){
    	$this->where('source_id', $source_id)->get()->delete();
    }
}

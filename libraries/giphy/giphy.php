<?php

define('GIPHY_API_URL', 'http://api.giphy.com');

class Giphy {

    private static $key;

    public function __construct ($key = 'dc6zaTOxFJmzC') {
        self::$key = $key;
    }

    public function search ($query, $limit = 25, $offset = 0) {
        $endpoint = '/v1/gifs/search';
        $params = array(
            'q' => urlencode($query),
            'limit' => (int) $limit,
            'offset' => (int) $offset
        );
        $data = $this->getSecureUrls($this->request($endpoint, $params));
        return $data;
    }

    public function getByID ($id) {
        $endpoint = "/v1/gifs/$id";
        $data = $this->getSecureUrls($this->request($endpoint, $params));
        return $data;
    }

    public function getByIDs (array $ids) {
        $endpoint = '/v1/gifs';
        $params = array(
            'ids' => implode(',', $ids)
        );
        $data = $this->getSecureUrls($this->request($endpoint, $params));
        return $data;
    }

    public function translate ($query) {
        $endpoint = '/v1/gifs/translate';
        $params = array(
            's' => urlencode($query)
        );
        $data = $this->getSecureUrls($this->request($endpoint, $params));
        return $data;
    }

    public function random ($tag = null) {
        $endpoint = '/v1/gifs/random';
        $params = array(
            'tag' => urlencode($tag)
        );
        //$data = $this->getSecureUrls($this->request($endpoint, $params));
        return $data;
    }

    private function getSecureUrls($data){
        $_data = $data->data;
        if(is_array($_data)){
            foreach ($_data as $key => $value) {
                if(strpos($_data[$key]->embed_url, 'https') === false){
                    $_data[$key]->embed_url =  str_replace('http', 'https', $_data[$key]->embed_url); 
                }
            }
        } else {
            if(strpos($_data[$key]->embed_url, 'https') === false){
             $_data->embed_url = str_replace('http', 'https', $_data->embed_url); 
            }
        }
        $data->data = $_data; 
        return $data;       
    }

    public function getFilteredPost($keyword, $dbPosts){
        $product = [];
        $results = $this->search($keyword, 5);  
        $hook = 1;
        $page=1;
        $nextPage= false;

        while ($hook == 1) {
            if($nextPage == true){
                $page++;
                $results = $this->search($keyword, 5, $offset);  
                $nextPage == false;             
            }
            if($results->meta->msg != 'OK' && $results->meta->status != 200){
                throw new Exception("Giphy Failed", 1);            
            }
            foreach ($results->data as $item) {
                $item_id = $item->id;     
                if($this->id_filter($item_id, $dbPosts)){
                    $product = $item;
                    $hook= 0;               
                    break;
                } 
            }   
            $offset = $results->pagination->offset + $results->pagination->count;
            if(empty($product)){
                $nextPage =true;
            }
            //avoid endeless loop
            if($page > 15){
                //$this->log->logInfo('Youtube Search Failed');
                break;
            }
        }

        return $product;        
    }

    protected function id_filter($id, $dbposts){
        foreach($dbposts as $_dbposts){
            $args = $_dbposts->to_array();  
            if($id == $args['source_id']){
                return false;
            }
        }
        return true;
    }

    public function trending ($limit = 25) {
        $endpoint = '/v1/gifs/trending';
        $params = array(
            'limit' => (int) $limit
        );
        $data = $this->getSecureUrls($this->request($endpoint, $params));
        return $data;
    }

    private function request ($endpoint, array $params = array()) {
        $params['api_key'] = self::$key;
        $query = http_build_query($params);
        $url = GIPHY_API_URL . $endpoint . ($query ? "?$query" : '');
        $result = file_get_contents($url);
        return $result ? json_decode($result) : false;
    }

}
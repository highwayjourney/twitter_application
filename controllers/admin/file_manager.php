<?php defined('BASEPATH') OR exit('No direct script access allowed');

class file_manager extends Admin_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {             
        
        CssJs::getInst()->add_css(array(
            'tagItz.css',
            'tagIt.css'            
            ));
        CssJs::getInst()->add_js(array(
            'libs/tagIt.js',
            'libs/jeditable.js'
            ));        
        $this->template->set('tags', file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/quote/keyword.json"));
        $this->template->set('MAX_UPLOAD_SIZE', min($this->asBytes(ini_get('post_max_size')), $this->asBytes(ini_get('upload_max_filesize'))));
        $this->template->render();
    }

    public function delete_category($category, $type){
        if(!empty($category)){
            $map = json_decode(file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/".$type."/data.json"));  
            foreach ($map->templates->folders as  $key => $folder) {
                if($folder->name != $category){
                    $folders[] = $folder;
                }
            } 
            $map->templates->folders = $folders;
            $fp = fopen("/home/socimattic/app.socimattic.com/assets/campaign/".$type."/data.json", 'w');
            if($fp){
                fwrite($fp, json_encode($map));
                fclose($fp);                
                echo json_encode(array("success" => true, "message" => "Category succesfully erased"));  
                exit();               
            } else {
                echo json_encode(array("success" => false, "message" => "Couldn't fetch data.json"));
                exit();
            }               
        } else {
            echo json_encode(array("success" => false, "message" => "there was an error in your request"));
            exit();
        }   
    }
    
    public function add_category($category, $type){
        if(!empty($category)){
            $map = json_decode(file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/".$type."/data.json"));
            $new = new stdclass;
            $new->name = $category;
            $new->tags = null;
            $map->templates->folders[] = $new;  
            $fp = fopen("/home/socimattic/app.socimattic.com/assets/campaign/".$type."/data.json", 'w');
            if($fp){
                fwrite($fp, json_encode($map));
                fclose($fp);                
                echo json_encode(array("success" => true, "message" => "Category succesfully saved"));  
                exit();               
            } else {
                echo json_encode(array("success" => false, "message" => "Couldn't fetch data.json"));
                exit();
            }            
        } else {
            echo json_encode(array("success" => false, "message" => "there was an error in your request"));
            exit();
        }         
    }

    public function add_description(){
        $post = $_POST;
        if(!empty($post['folder']) && !empty($post['description'])){
            $map = json_decode(file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/".$post['type']."/data.json"));   
            foreach ($map->templates->folders as  $key => $folder) {
                if($folder->name == $post['folder']){
                    $map->templates->folders[$key]->description = $post['description'];
                }
            } 
            $fp = fopen("/home/socimattic/app.socimattic.com/assets/campaign/".$post['type']."/data.json", 'w');
            //var_dump($map, $fp); die();
            if($fp){
                fwrite($fp, json_encode($map));
                fclose($fp);                
                echo $post['description'];  
                exit();               
            } else {
                echo json_encode(array("success" => false, "message" => "Couldn't fetch data.json"));
                exit();
            }
        } else {
            echo json_encode(array("success" => false, "message" => "there was an error in your request"));
            exit();
        }  
    }

    public function getMaches(){
        $keywords = $_POST['keywords'];
        $famous_quote = new Famous_quote;
        $matches = $famous_quote->where_in('keywords', $keywords)->count();
        echo json_encode(array("matches" => $matches));
    }


    public function add_tags(){
        $post = $_POST;
        if(!empty($post['folder']) && !empty($post['tags'])){
            $map = json_decode(file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/".$post['type']."/data.json"));   
            foreach ($map->templates->folders as  $key => $folder) {
                if($folder->name == $post['folder']){
                    $map->templates->folders[$key]->tags = $post['tags'];
                }
            } 
            $fp = fopen("/home/socimattic/app.socimattic.com/assets/campaign/".$post['type']."/data.json", 'w');
            //var_dump($map, $fp); die();
            if($fp){
                fwrite($fp, json_encode($map));
                fclose($fp);                
                echo json_encode(array("success" => true, "message" => "tags succesfully saved"));  
                exit();               
            } else {
                echo json_encode(array("success" => false, "message" => "Couldn't fetch data.json"));
                exit();
            }
        } else {
            echo json_encode(array("success" => false, "message" => "there was an error in your request"));
            exit();
        }  
    }

    public function req(){
        // Set to false to disable delete button and delete POST request.
        $allow_delete = true;
        // must be in UTF-8 or `basename` doesn't work
        setlocale(LC_ALL,'en_US.UTF-8');
        if(empty($_REQUEST['file'])){
            $_REQUEST['file'] = "/home/socimattic/app.socimattic.com/assets/campaign/".$_REQUEST['type'];
        }
        $type = $_REQUEST['type'];
        $map = json_decode(file_get_contents("/home/socimattic/app.socimattic.com/assets/campaign/".$_REQUEST['type']."/data.json"));
        foreach ($map->templates->folders as  $folder) {
            $folders[$folder->name]['tags'] = $folder->tags;
            $folders[$folder->name]['description'] = $folder->description;
        }
        if(realpath($_REQUEST['file'])){
            $tmp = realpath($_REQUEST['file']);
        }else {
            $tmp = realpath("/home/socimattic/app.socimattic.com/assets/campaign/".$_REQUEST['file']);
        }
        
        if($tmp === false)
            $this->err(404,'File or Directory Not Found');

        $file = $_REQUEST['file'] ?: '.';
        if($_GET['do'] == 'list') {
            if (is_dir($file)) {
                $directory = $file;
                $current = explode("/", $directory);
                $current =$current[count($current) -1];
                $result = array();
                $files = array_diff(scandir($directory), array('.','..'));
                foreach($files as $entry) if($entry !== basename(__FILE__)) {
                    if (!preg_match("/\.json$/", $entry)){
                        $i = $directory . '/' . $entry;
                        $stat = stat($i);
                        if(($current == 'quote' || $current == 'trivia' || $current == 'fact') && is_dir($i)){
                            $keywords = $folders[$entry]['tags'];
                            $description = $folders[$entry]['description'];
                        } else {
                            $keywords = false;
                            $description = false;
                        }
                        $result[] = array(
                            'mtime' => $stat['mtime'],
                            'size' => $stat['size'],
                            'name' => basename($i),
                            'path' => preg_replace('@^\./@', '', $i),
                            'is_dir' => is_dir($i),
                            // 'is_deleteable' => $allow_delete && ((!is_dir($i) && is_writable($directory)) ||
                            //                                            (is_dir($i) && is_writable($directory) && $this->$this->is_recursively_deleteable($i))),
                            'is_deleteable' => true,
                            'is_base' => $current == 'campaign'?true:fasle, 
                            'is_readable' => is_readable($i),
                            'is_writable' => is_writable($i),
                            'is_executable' => is_executable($i),
                            'keywords' => $keywords,
                            'description' => $description
                        );
                    }
                }
            } else {
                $this->err(412,"Not a Directory");
            }
            echo json_encode(array('success' => true, 'is_writable' => is_writable($file), 'results' =>$result));
            exit;
        } elseif ($_POST['do'] == 'delete') {
            if($allow_delete) {
                $this->rmrf($file);
                $current = explode("/", $file);
                $current =$current[count($current) -1];                
                $this->delete_category($current,$type);
            }
            exit;
        } elseif ($_POST['do'] == 'mkdir') {
            // don't allow actions outside root. we also filter out slashes to catch args like './../outside'
            $dir = $_POST['name'];
            $dir = str_replace('/', '', $dir);
            if(substr($dir, 0, 2) === '..')
                exit;
            chdir($file);
            @mkdir($_POST['name']);
            $this->add_category($dir,$type);
            exit;
        } elseif ($_POST['do'] == 'upload') {
            var_dump($_POST);
            var_dump($_FILES);
            var_dump($_FILES['file_data']['tmp_name']);
            //var_dump($tmp.'/'.$_FILES['file_data']['name']); die();
            move_uploaded_file($_FILES['file_data']['tmp_name'], $tmp.'/'.$_FILES['file_data']['name']);
            exit;
        } elseif ($_GET['do'] == 'download') {
            $filename = basename($file);
            header('Content-Type: ' . mime_content_type($file));
            header('Content-Length: '. filesize($file));
            header(sprintf('Content-Disposition: attachment; filename=%s',
                strpos('MSIE',$_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\"" ));
            ob_flush();
            readfile($file);
            exit;
        }


    }

    private function rmrf($dir) {
        if(is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file)
                $this->rmrf("$dir/$file");
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }
    private function is_recursively_deleteable($d) {
        $stack = array($d);
        while($dir = array_pop($stack)) {
            if(!is_readable($dir) || !is_writable($dir)) 
                return false;
            $files = array_diff(scandir($dir), array('.','..'));
            foreach($files as $file) if(is_dir($file)) {
                $stack[] = "$dir/$file";
            }
        }
        return true;
    }
    private function err($code,$msg) {
        echo json_encode(array('error' => array('code'=>intval($code), 'msg' => $msg)));
        exit;
    }
    private function asBytes($ini_v) {
        $ini_v = trim($ini_v);
        $s = array('g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10);
        return intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
    }    
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template_update extends Admin_Controller {

    public function __construct() {
		ini_set("memory_limit", "4096M");
		//phpinfo(); die();
		//ini_set("memory_limit", 2000000000);
		ini_set('max_execution_time', 900);
		ini_set('max_input_time ', 900);      	
        parent::__construct();
    }

    public function index() {
        $plan =  new Plan;
        $_plans = $plan->getActualPlans(false, true);
        foreach ($_plans as $key => $value) {
            $_plan = $value->to_array();
            if($_plan['id'] == 1 || $_plan['id'] == 3  || $_plan['id'] == 10){
                if($_plan['name'] != "Template Club Yearly"){
                    $plans[$_plan['id']] = $_plan['name'];
                } else {
                   $plans[$_plan['id']] = "VIP"; 
                }
            }
        }
        $this->template->set('plans', $plans);    	
    	$this->template->render();
    }

    public function update(){
   	
    	if($_POST['plan']){
    		$plan = $_POST['plan'];
			$path = realpath(dirname($_SERVER['SCRIPT_FILENAME']) .'/assets/plans/'.$plan.'/templates');
			// $folder = json_decode(json_encode($this->scan_dir($path)));
			$folder = $this->scan_dir($path);
			$json = new stdclass;
			$json->templates->folders = $folder;
			

			foreach ($folder as $_folder) {
				$_files = $this->scan_dir($path. "/".$_folder);
				
				foreach ($_files as  $file) {
					$_file = new stdclass;
					$type = explode(".", $file)[1];
					if($type == "json"){
						$_file->folder = $_folder;
						$_file->path = "templates/".$_file->folder."/".$file;
						$_file->picture = "templates/".$_file->folder."/thumb/".str_replace("json", "png", $file);
						$_file->class = $plan;
						$_file->updated = filemtime($path. "/".$_folder."/".$file);
						//$files[] = $_file;
						$json->templates->files[] = $_file;
						// $ff = "/home/autopixar/app.autopixar.com/public/assets/design-tool/plans/templates/VIP/".$_file->folder."/thumb/";
						$ff = dirname($_SERVER['SCRIPT_FILENAME']) ."/public/assets/design-tool/plans/templates/".$_file->folder."/thumb/";					

			            if(!is_dir($ff)) {
			                mkdir($ff, 0755, TRUE);
			            }  				

						$resizedFilename = $ff.str_replace("json", "png", $file);
						if(!file_exists($resizedFilename)){
							$this->_resize_image(dirname($_SERVER['SCRIPT_FILENAME']) ."/assets/plans/".$plan."/templates/".$_file->folder."/".str_replace("json", "png", $file), $resizedFilename);
						}						
					}
				}
			}		
			//$json->templates->files = $files;
			unset($files, $folder, $_files);

			//ddd($json);
			$path = realpath(dirname($_SERVER['SCRIPT_FILENAME']) .'/public/assets/design-tool/data');
			$folders = $this->scan_dir($path);

			foreach ($folders as $folder) {
				if($folder != "effects" && $folder != "user-designs"){
					if($folder == "fonts"){
						
						$_files = $this->scan_dir($path. "/".$folder);
						foreach ($_files as $file) {
							$item = new stdclass;
							$item->name = explode(".", $file)[0];
							$item->path = "fonts/".$file;
							$json->fonts[] = $item;
						}
						unset($_files);
					}
					if($folder == "graphics"){
						$sub_folders = $this->scan_dir($path. "/".$folder);
						foreach ($sub_folders as $sub_folder) {
							if(is_dir($path."/".$folder."/".$sub_folder)){
								$json->graphics->folders[] = $sub_folder;
							}
							$sub_files = $this->scan_dir($path. "/".$folder."/".$sub_folder);
							foreach ($sub_files as  $sub_file) {
								$type = explode(".", $sub_file)[1];
								if($type === "png" || $type === "jpg") {				
									$item = new stdclass;
									$item->folder = $sub_folder;
									$item->path = $folder."/".$sub_folder."/".$sub_file;
									$ff = dirname($_SERVER['SCRIPT_FILENAME']) ."/public/assets/design-tool/data/graphics/".$sub_folder;									
									list($width, $height) = getimagesize($ff."/".$sub_file);	
									//d($width, $sub_folder."/".$sub_file);				
									if($width >= 201){
										$item->thumb = $folder."/".$sub_folder."/thumb/".$sub_file;

									     if(!is_dir($ff."/thumb")) {
									         mkdir($ff."/thumb", 0755, TRUE);
									     }  				

										$resizedFilename = $ff."/thumb/".$sub_file;
										if(!file_exists($resizedFilename)){
											$this->_resize_image($ff."/".$sub_file, $resizedFilename, $type);
										}	
									}
							
									//}
									$json->graphics->files[] = $item;
								}
							}
							unset($sub_files);
						}
						unset($sub_folders);
					}									
				}
			}
			unset($folders);
			//ddd($json);
			$fp = fopen(dirname($_SERVER['SCRIPT_FILENAME']) ."/public/assets/design-tool/plans/".$plan."/data.json", 'w');
			fwrite($fp, json_encode($json));
			fclose($fp);	
			 $this->addFlash('Files for Plan: '.$plan.' successfully updated', 'success');
			 redirect('admin/template_update');
		} else {
			$this->addFlash('Error processing your request', 'error');
		}
    }

    private function _resize_image($filename, $dest, $type ='png'){
    	$this->load->library('image_lib');
        // Get new dimensions
        list($width, $height) = getimagesize($filename);
        $scale = 1;
        if($width > 250){
        	$scale = 250/$width;
        }    
        $config2['source_image'] = $filename;
        $config2['new_image'] = $dest;
        if($height*$scale > 0){
        	$config2['height'] = $height*$scale;	
        } else {
        	$config2['height'] = $width*$scale;
        }
        
        $config2['width'] = $width*$scale;
        $config2['maintain_ratio'] = TRUE;
        $this->image_lib->initialize($config2);
        $this->image_lib->resize(); 
    } 

    private function scan_dir($dir) {
	    $ignored = array('.', '..', '.svn', '.htaccess');

	    $files = array();    
	    foreach (scandir($dir, SCANDIR_SORT_ASCENDING) as $file) {
	        if (in_array($file, $ignored)) continue;
	        $files[] = $file;
	    }

	    //arsort($files);
	    //$files = array_keys($files);

	    return ($files) ? $files : false;
	}
// private function dir_tree($dir) {    
//     $files = array_map('basename', glob("$dir/*"));
//     foreach($files as $file) {
//         if(is_dir("$dir/$file")) {
//             $return[$file] = $this->dir_tree("$dir/$file");
//         } else {
//             $return[filemtime("$dir/$file")] = $file;
//         }
//     }
//     return $return;
// }

}
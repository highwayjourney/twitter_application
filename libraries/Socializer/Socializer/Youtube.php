<?php use Masih\YoutubeDownloader\YoutubeDownloader;
class Socializer_Youtube
{  
	protected $api_key;
    
    function __construct($user_id, $token) {
    	//var_dump($token); die();
  //  	 $this->api_key ='AIzaSyBHiMjKKKg3giAzagl0_xJTDSeP4D0e008';
$this->api_key ='AIzaSyDBAKyaN65qpY9dBsTELW66aFB9HFUpoiI';
    
	//$_token = json_decode($token['data']);
    	//var_dump($_token); die();
    	//$this->api_key = $_token->id_token;
    }
	public function search($keyword, $pageToken=null){

		$keyword = urlencode($keyword);

		$api_key = $this->api_key;


		$url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $keyword;

		if($pageToken){
			$url .= "&pageToken=$pageToken";
		}
		$url .= '&key=' . $api_key;
		$url .= '&maxResults=10';
		$url .= '&type=video';
		$url .= '&videoDuration=short';
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url 
		    
		));

		// Send the request & save response to $resp
		$response = curl_exec($curl);
		//var_dump($response); die();
		// Close request to clear up some resources
		curl_close($curl);
//var_dump($response);
		$response = json_decode($response);

		$nextPageToken = $response->nextPageToken;
		$prevPageToken = $response->prevPageToken;
		$items = $response->items;

		$videos = array();

		foreach ($items as $item) {
			
			if ($item->id->kind != "youtube#video"){
				continue;
			}

			$video = new stdClass();

			$video_id = $item->id->videoId;
			$video_url = "http://youtube.com/watch/?v=" . $video_id;
			$snippet = $item->snippet;
			//ddd($item);
			$thumbnails = $snippet->thumbnails;
			
			$video->source_id = $video_id;
			$video->media_url =  $thumbnails->medium->url;
			//$video->image =  $thumbnails->medium->url;
			$video->description = $item->snippet->description;
			$video->title = $item->snippet->title;
			$video->url = $video_url;
			$video->source = 'youtube';

			//Page Tokens
			$video->nextPageToken = $nextPageToken;
			$video->prevPageToken = $prevPageToken;

			$videos[] = $video;
		
		}


		return $videos;
	}

	public function video_info()
	{
		$url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&' ;
		$url .= '&key=' . $this->api_key;
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url 
		    
		));
	}

	public function getVideoIdFromURL($url)
	{
		$pattern = '/(?:youtube.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu.be/)([^"&?/ ]{11})/i';
		preg_match($pattern, $url, $matches);

		 return isset($matches[1]) ? $matches[1] : false;
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

    public function youtube_filter($keyword, $youtube, $dbpost){ 
        $product = [];
        $results = $youtube->search($keyword);  
        $hook = 1;
        $page=1;
        $nextPage= false;

        while ($hook == 1) {
            if($nextPage == true){
                $page++;
                $results = $youtube->search($keyword, $pageToken);  
                $nextPage == false;             
            }

            foreach ($results as $item) {
                $item_id = $item->source_id;     
                if($this->id_filter($item_id, $dbpost)){
                    $product = $item;
                    $hook= 0;               
                    break;
                } else {
                    $pageToken = $item->nextPageToken;
                }
            }   
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
    public function createGiF($video_id, $user_id, $since = 0, $until = 15) {
    		//$video_id = '7kXb-tX0Ces';
			// ini_set('max_execution_time', 180);
			// set_time_limit(180);
    		//var_dump($video_id, $user_id, $since, $until); die();
			$youtube = new YoutubeDownloader($video_id);
			$youtube->setPath(dirname($_SERVER['SCRIPT_FILENAME']).'/videos');

			$itags = array("5","6", "132", "133", "134", "160");
		
			$_video_info = $youtube->getVideoInfo();
			//d($_video_info);
			foreach ($_video_info->full_formats as $video_info) {
				if(in_array($video_info->itag, $itags)){
					$available_itag = $video_info->itag;
					break;
				}				
			}	
			if(empty($available_itag)){
				foreach ($_video_info->adaptive_formats as $video_info) {
					if(in_array($video_info->itag, $itags)){
						$available_itag = $video_info->itag;
						break;
					}				
				}							
			}
			$youtube->setDefaultItag($available_itag);
			//d($available_itag);
			//$available_itags = $youtube->getItags();
			//d($available_itags);

			//ddd($youtube->getVideoInfo());
			
			$_SESSION['user_id'] = $user_id;
			$_SESSION['since'] = $since;
			$_SESSION['until'] = $until;
			
			$youtube->onComplete = function ($filePath, $fileSize, $index, $count) {
				//$filePath = $this->pathSafeFilename($filePath);
				$ext = pathinfo($filePath, PATHINFO_EXTENSION);
				$new_name = dirname($_SERVER['SCRIPT_FILENAME']).'/videos/'.time().'.'.$ext;
				rename($filePath, $new_name);
				$filePath = $new_name;
				$user_id = $_SESSION['user_id'];
				$since = $_SESSION['since'];
				$until = $_SESSION['until'];
				$duration = $until - $since;


				unset($_SESSION['user_id']);
				unset($_SESSION['since']);
				unset($_SESSION['until']);

				$config = new PHPVideoToolkit\Config(array(
				    'temp_directory' => '/tmp/autosoci',
                'ffmpeg' => '/usr/bin/ffmpeg',
                'ffprobe' => '/usr/bin/ffprobe',
                'yamdi' => '/opt/local/bin/yamdi',
                'qtfaststart' => '/opt/local/bin/qt-faststart',
                'cache_driver' => 'InTempDirectory',
                'gifsicle' => '/usr/bin/gifsicle',
                'convert' => '/usr/bin/convert'
				), true);	

				$config->convert = null;
				$config->gif_transcoder = 'gifsicle';
				try {
					if($duration > 15){
						throw new Exception("Duration exceeds maximum allowed", 1);
					}
					$ffmpeg = new PHPVideoToolkit\FfmpegParser($config);
		            $nameImage = time() . '.gif';

		            $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/uploads/' . $user_id . '/';
		            if(!is_dir($path)) {
		                mkdir($path, 0755, TRUE);
		            }		            
		            $url = site_url() . '/public/uploads/' . $user_id . '/';

		            $default_format = 'ImageFormat';
		            $frame_rate = 6;
					$times = 0;
					$gif_size = 7;

					while($gif_size > 5){
						$output_path = $path.$nameImage;
						$output_format = PHPVideoToolkit\Format::getFormatFor($output_path, $config, $default_format);
						$output_format->setVideoFrameRate($frame_rate);

						$video = new PHPVideoToolkit\Video($filePath, $config);
						$process = $video->extractSegment(new PHPVideoToolkit\Timecode($since), new PHPVideoToolkit\Timecode($until))
						                ->save($output_path, $output_format, PHPVideoToolkit\Media::OVERWRITE_EXISTING);					
					    $output = $process->getOutput();

					    //d($duration);

					    $gif_delay = intval($duration/$frame_rate)*10;
					    //ddd($output_path);
					    // $img = new Imagick($output_path);
					    $image_size = filesize($output_path);
					    $gif_size = $image_size/1000000;
					    //d($gif_size, $output_path);

					    if($image_size > 5000001) {
					    	//$final_image_name = time() . '.gif';
					    	//$final_path = $path.$final_image_name;
					    	exec("/usr/bin/gifsicle -O3 --colors 256 --delay ".$gif_delay." ".$output_path." > ".$output_path);
					    	$gif_size = filesize($output_path);
					    	$gif_size = $gif_size/1000000;
					    	//d($gif_size, $output_path);
					    	if($gif_size > 5){
					    		if($times > 2 && $default_format == 'ImageFormat'){
					    			$default_format = 'VideoFormat';
					    			$frame_rate = 7;
					    			$times = 0;
					    		}
					    		if($times > 3 && $default_format == 'VideoFormat'){
					    			throw new Exception("Sorry but we can't proccess this Video", 1);
					    		}					    		
						    	$frame_rate--;
	                            // if(file_exists($final_path)){
	                            //     unlink($final_path);
	                            // }	                              	
					    	} else {
					    		$nameImage = $final_image_name;
					    	}				    	
					    } else {
					    	//break while
					    	//$gif_size = 4;
					    	//d($gif_size, 'final');
					    }
					    $times++;
					}
				    $result =	array(
	                	'success' => true,
	                	'image' => $nameImage,
	                	'url' => $url.$nameImage,
	                	'format' => $default_format,
	                	'frame_rate' => $frame_rate,
	                	'gif_size' => $gif_size,
	                	'message' => 'Animation Sucessfully Created'
	            	);		
				} catch (Exception $e) {
				    $result =	array(
	                	'success' => false,
	                	'message' => $e->getMessage()
	            	);					
				}
	            echo json_encode($result);	
	            unlink($filePath);				
				//return $nameImage;	 

		};
		// $youtube->onProgress = function ($downloadedBytes, $fileSize, $index, $count) {
		//     if ($count > 1) echo '[' . $index . ' of ' . $count . ' videos] ';
		// 	if ($fileSize > 0)
		// 		echo "\r" . 'Downloaded ' . $downloadedBytes . ' of ' . $fileSize . ' bytes [%' . number_format($downloadedBytes * 100 / $fileSize, 2) . '].';
		// 	else
		// 		echo "\r" . 'Downloading...'; // File size is unknown, so just keep downloading
		// };		
		$youtube->download();
		//d($youtube);
	}

	private function pathSafeFilename($string)
	{
		$string = str_replace(
			array_merge(range(chr(0), chr(31)), str_split("#%+&`‘/<>:\"/|?*\x5C\x7F")),
			' ',
			basename(trim($string))
		);
		$string = preg_replace('/\s{2,}/', ' ', $string);
		$string = str_replace(array(' ', '%20'), '_', $string);
		return $string;
	}	
}

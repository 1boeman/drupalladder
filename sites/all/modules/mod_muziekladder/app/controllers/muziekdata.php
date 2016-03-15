<?php
class Muziekdata extends Controller {
  private $image_cache = '';
  
  function __construct(){ 
    $this->image_cache = DRUPAL_ROOT.'/'.variable_get('file_public_path', conf_path() . '/files/muziek_agenda_image_cache');
  }

  function img() {
    // make sure the cache exists
    $image_cache = $this->image_cache;  
    if (!file_exists($image_cache)){
      mkdir($image_cache); 
      chmod($image_cache,0750);
    }
    if (isset($_GET['p']) && basename($_GET['p']) == $_GET['p']) {

        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));
        // the browser will send a $_SERVER['HTTP_IF_MODIFIED_SINCE'] if it has a cached copy 
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
            // if the browser has a cached version of this image, send 304
              header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
                exit;
        }

        $get_p = $_GET['p'];
         
        $p = base64_decode($get_p); // $url
        $cache_file = md5($p);
        $pic = $image_cache .'/'. $cache_file;
        $thumb = $pic.'_thumb'; 
        if (file_exists($pic) && is_readable($pic)) {
            //image in cache
        } elseif (preg_match('/^(http|HTTP|\/\/)/',$p)){
          // image not yet in cache 
          // download it and store it.
          if (substr($p,0,2) == '//'){
            $p = 'http:'.$p;  
          }
          set_time_limit(0);
          $fp = fopen ( $pic, 'w+');//This is the file where we save the    information
          $ch = curl_init(str_replace(" ","%20",$p));//Here is the file we are downloading, replace spaces with %20
          curl_setopt($ch, CURLOPT_TIMEOUT, 50);
          curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_exec($ch); // get curl response
          curl_close($ch);
          fclose($fp);
        } else { 
          watchdog('PHP',"Error trying to fetch image with incomplete url: ".$p);
          drupal_not_found();
          drupal_exit();  
        }
        
        // do we want the thumb or the full image
        if(isset($_GET['s'])){
          // thumb
          if (file_exists($thumb) && is_readable($thumb)) {
            $pic = $thumb;        
          } else {
            // create thumb;  
            require ('thumb.php');
            $thumb_img = new Thumbnail($pic); 
            $thumb_img->createThumb(Array('width'=>60,'maxDimension'=>60),$thumb);
            $pic = $thumb;
          }
        }

        // set the MIME type
        $info = @getimagesize($pic);
        $mime = false; 
        if (isset($info['mime'])) {
          $mime = $info['mime'];
        }
       
        // if a valid MIME type exists, display the image
        // by sending appropriate headers and streaming the file
        if ($mime) {
           $file =  fopen($pic, 'rb');
            if ($file) {
                header('Content-type: '.$mime);
                header('Content-length: '.filesize($pic));
                fpassthru($file);
                exit;
            }
        }
    }

//    drupal_not_found();
    drupal_exit();  
  }

}

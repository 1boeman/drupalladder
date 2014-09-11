<?php 

class Muziek extends Controller {
  function __construct(){ }

  function __call($name, $arguments) {
    if (stristr($name,'agenda-')){
      $daynum = str_replace(array('agenda-','.html'),'',$name); 
      return $this->day($daynum);
    }
  }

  function index() {
    return $this->day(0);
  }

  function setcity(){
    if (isset ($_REQUEST['city'])){
        if ($_REQUEST['city'] && $_REQUEST['city'] != '0'){
	   $this->setSession('city',$_REQUEST['city']); 
        }else{
           $this->setSession('city',0); 
        }
        drupal_json_output( $this->getSession('city') );
      }
    exit;       
  }

  function day($p){
    $this->get_city_menu(); 
    $session_city = $this->getSession('city');  
    $city = isset ($session_city) ? $session_city : 0; 
    if ($city){
      $lcs = '';
      $cities = explode(',',$city); 
      foreach ($cities as $value){
        $lcs .=' .locationUnit'.$value.'{display:block}';
      }

      drupal_add_js(array('muziekladder'=>array('sessionLocations'=>$cities)), 'setting');
 
//      $css = '<style type="text/css" id="hideLocations"> .locationUnit{display:none}'.$lcs.'</style>';
    }
   
    $date = new DateTime();
    $date->modify('+'.$p.' day');

    $date->modify('-4 hour'); //don't change first agenda page to tomorrow till 4 am

    $file = MUZIEK_DATA.'/'.$date->format('Y').'/'.$date->format('d-m').'.xml';
    $this->init_view(); 
  
    if (!file_exists($file)){
      header("HTTP/1.0 404 Not Found");
      $content = trim($view->notfound);
    }else{
      $xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);

      $nextp = $p+1 < 89 ? $p+1 : 89;
      $nextlink = '/muziek/agenda-'.$nextp.'.html';
      $prevp = $p-1 > 0 ? $p-1 : 0;
      $prevlink = '/muziek/agenda-'.$prevp.'.html';    
      $controls = theme ('agenda_header', array( 'prevlink'=>$prevlink,'nextlink'=>$nextlink )); 
      
      $titlearr = explode ('-' ,trim($xml->title)); 
      $dateday = $titlearr[0];
      $titletag = 'Agenda ' . $dateday . ' -  concerten, optredens en evenementen ' ; 

      $this->set_head_title($titletag);
      $this->set_title('Agenda '.$dateday. ' - concerten, optredens en evenementen'  );
      $content = trim($xml->content);

      $content = str_replace('##controls##',$controls,$content);
            
      $frontend_date = array (
        'day' => $date->format('d'),
        'month' =>  $date->format ('m'),
        'year' => $date->format('Y')
      );
      drupal_add_js(array('muziekladder' => array('date'=> $frontend_date)), 'setting');
      return array('html'=>$content); 
    }
  }
}

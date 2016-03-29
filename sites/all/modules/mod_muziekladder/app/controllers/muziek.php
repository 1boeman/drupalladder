<?php

class Muziek extends Controller {

  function __call($name, $arguments) {
    //city based lists
    // get cityno
    if (preg_match('/([0-9]+)-[a-zA-Z]*/',$name,$matches)){
      // get day offset
      if (preg_match('/[0-9]+-[a-zA-Z]*\/agenda-([0-9]+)/',$_SERVER['REQUEST_URI'],$matches2)){
        return $this->city_events($matches[1],$matches2[1]);
      }
      return $this->city_events($matches[1],0);
    } elseif (stristr($name,'agenda-')){

      $daynum = str_replace(array('agenda-','.html'),'',$name);
      return $this->city_events(0,$daynum);
    }
  }

  function navigation_links($cityno,$cityname,$day){
    $ct = $cityno? $cityno.'-'.$cityname.'/' :'';
    $daynext = $day + 1 < 90 ? $day +1 : 90;
    $dayprev = $day - 1 > 0 ? $day - 1 : 0;
    $cities = Muziek_db::get_cities();
    drupal_add_js(array('muziek_cities'=>$cities),'setting');
 
    $prefix= Muziek_util::lang_url();
    $date_array=array();
    $dt = new DateTime();
    $dt->modify('-4 hour'); //don't change first agenda page till 4 am

    for ($i=0; $i<90; $i++){
      $date_option = $dt->format('d/m/Y') .' - '. t($dt->format('l')) ;
      $date_array []=  $date_option;
      $dt->modify('+ 1 day' );
    }

    if (!$cityno && !$day){
      $menucities = Muziek_db::get_cities_by_ids(array(1,8,5,1412801590,1413406572,4,7,15,6,));
      $city_menu = theme('city_menu',array('cities' => $menucities, 'simple_list'=>1 ));
    }else{
      $city_menu = '';
    }

    return theme('agenda_city_nav',array(
      'dates' => $date_array,
      'cityno' => $cityno,
      'cities' => $cities,
      'city_menu'=>$city_menu,
      'day' => $day,
      'daynext' => $daynext,
      'dayprev' => $dayprev,
      'add_event'=> $prefix.'muziekformulier',
      'next' =>  $prefix.'muziek/'.$ct.'agenda-'.$daynext.'.html',
      'prev' =>  $prefix.'muziek/'.$ct.'agenda-'.$dayprev.'.html',
      'tday' =>  $prefix.'muziek/'.$ct )
    );
  }

  function link_to_agenda($cityno,$cityname) {
    $ct = $cityno? $cityno.'-'.$cityname.'/' :'';
    $prefix= Muziek_util::lang_url();
    return '<a href="' . $prefix . 'muziek/'.$ct . '">'.t('Jump to the') .' '. $cityname .' '. t('calendar'). ' pages &raquo;</a>';
  }

  function index() {
    return $this->city_events(0);
  }

  function ajax_agenda(){

    if (!isset($_GET['city'])){
      $cityno = 0;
    } else {
      $cityno = $_GET['city'];
    }

    $events = $this->city_events($cityno,0,300,1);
    $nav ='';

    if ($cityno){
        $cityname = $events[0]['City_Name'];
        $nav = $this->link_to_agenda($cityno,$cityname);
    }

    $content = theme('agenda_city_gig',array(
      'count'=> 0,
      'rpp' =>0,
      'page'=>0,
      'navigation'=>$nav,
      'cityname'=>'',
      'content'=>$events));

    if ($cityno){
      $content .= '<div class="calendar_sublink">'.$this->link_to_agenda($cityno,$cityname).'</div>';
    }

    return array (
        'html_fragment'=> $content);
  }

  function city_events($cityno,$day=0, $results_per_page=300, $raw=0){
    //get pagina
    if (isset($_REQUEST['pagina'])){
      $page = (int)$_REQUEST['pagina'];
    }else{
      $page = 0;
    }

    $date = new DateTime();
    if ($day) $date->modify('+'.$day.' day');

    $date->modify('-4 hour'); //don't change first agenda page till 4 am
    $db_date = $date->format('Y-m-d');

    $gigs = Muziek_db::get_city_gigs($cityno,$db_date,$page,$results_per_page);
    $contentarr = Array();
    while($res = $gigs->fetchArray(SQLITE3_ASSOC)){
      $contentarr []= $res;
    }

    if ($raw) return $contentarr;
    $titletag = '';
    if ($cityno && isset($contentarr[0])) {
      $cityname = $contentarr[0]['City_Name'];
      $titletag .= $cityname;
    } else {
      $cityname = '';
    }

    $titletag .= ' Muziekagenda ';

    $title_date = t($date->format('l')).' '.$date->format('j').' '. t($date->format('F'));
    $titletag .= ' v.a. '.$title_date;
    $titletag .= ' (dag:'.($day+1) .'- pagina:'.($page+1) .')';
    $this->set_head_title($titletag);

    $h1 = (strlen ($cityname) ? ucfirst ($cityname) .' '.t('calendar'): t('Music Calendar '));
    $h1 .= ' - '.t(' music, concerts, events ') . ' - '. t('Starting from').' '.$title_date;
    $this->set_title($h1);

    $content = theme('agenda_city_gig',array(
      'title_date'=> $title_date,
      'count'=> count($contentarr),
      'rpp' =>$results_per_page,
      'page'=>$page,
      'navigation'=> $this->navigation_links($cityno,$cityname,$day),
      'cityname'=>$cityname,
      'content'=>$contentarr));

    $render_array = array(
      'agenda_content'=>array(
        '#type'=>'markup',
        '#markup'=>$content,
      ),
    );

    return array('render_array'=>$render_array);
  }

 /**
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
    $cities = Muziek_util::getCities();
    drupal_add_js('var muziek_cities = '.json_encode($cities),
         array('type' => 'inline', 'scope' => 'footer', 'weight' => -10)
    );

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
      $inline_css = "\n".'<style type="text/css" id="hideLocations" class="session_based_css"> .locationUnit{display:none}'.$lcs.'</style>'."\n";
      $element = array(
          '#type' => 'markup',
          '#markup' => $inline_css,
      );
      drupal_add_html_head($element, 'inline_css');

    }

    $datefile = $this->getDateFile($p);
    $file = $datefile['file'];
    $frontend_date = $datefile['date'];
    $this->init_view();
    if (!file_exists($file)){
      header("HTTP/1.0 404 Not Found");
      $content = trim($view->notfound);
    } else {
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

      drupal_add_js(array('muziekladder' => array('date'=> $frontend_date)), 'setting');
      return array('html'=>$content);
    }
  }

  function getDateFile($day){
    $date = new DateTime();
    if ($day) $date->modify('+'.$day.' day');

    $date->modify('-4 hour'); //don't change first agenda page to tomorrow till 4 am

    $file = MUZIEK_DATA.'/'.$date->format('Y').'/'.$date->format('d-m').'.xml';
    $frontend_date = array (
        'day' => $date->format('d'),
        'month' =>  $date->format ('m'),
        'year' => $date->format('Y')
    );

    return array('date'=>$frontend_date,'file'=> $file);
  }
  **/
}

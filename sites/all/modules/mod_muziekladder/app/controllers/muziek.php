<?php

class Muziek extends Controller {

  function __call($name, $arguments) {
    //city based lists
    // get cityno

    if (preg_match ('/^regio\-/',$name,$matches)) {

      // get day offset
      if (preg_match('/\/agenda-([0-9]+)/',$_SERVER['REQUEST_URI'],$matches3)){
        return $this->city_events($name,$matches3[1]);
      }

      return $this->city_events($name,0);

    } elseif (preg_match('/([0-9]+)-[a-zA-Z]*/',$name,$matches)){
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
    if ($cityno) $cityno = str_replace('regio-','',$cityno);
    $ct = $cityno ? $cityno .'-'.$cityname.'/' :'';
    $daynext = $day + 1 < 90 ? $day +1 : 90;
    $dayprev = $day - 1 > 0 ? $day - 1 : 0;
    $cities = Muziek_db::get_cities();
    $regios = Muziek_db::get_regios();
 
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

    $regio_menu = theme('regio_menu',array('regios' => $regios, 'day'=>$day, 'simple_list'=>1, 'current_regio' => $cityno ));

    return theme('agenda_city_nav',array(
      'dates' => $date_array,
      'cityno' => $cityno,
      'cities' => $cities,
      'city_menu'=>$regio_menu,
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
    $is_region = false; 

    $date = new DateTime();
    if ($day) $date->modify('+'.$day.' day');

    $date->modify('-4 hour'); //don't change first agenda page till 4 am
    $db_date = $date->format('Y-m-d');
   

    if(preg_match('/^regio-/',$cityno)){
      $is_region = true;
      $regio_id = str_replace('regio-','',$cityno);
      $regio_row = Muziek_db::get_regio($regio_id);
      $gigs = Muziek_db::get_regio_gigs($regio_id,$db_date,$page,$results_per_page);
    } else {
      $gigs = Muziek_db::get_city_gigs($cityno,$db_date,$page,$results_per_page);
    }

    $contentarr = Array();
    while($res = $gigs->fetchArray(SQLITE3_ASSOC)){
      $contentarr []= $res;
    }

    if ($raw) return $contentarr;
    $titletag = '';
    if ($cityno && isset($contentarr[0])) {
      if ($is_region) {
        $cityname = t($regio_row['Name']); 
      } else {
         $cityname = $contentarr[0]['City_Name'];
      }
    } else {
      $cityname = '';
    }
   
   $titletag .= ' '.t($cityname);

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

}

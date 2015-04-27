<?php 

class Locaties extends Controller {

  function __call($name, $arguments) {
      if (preg_match('#(.+)-[a-zA-Z]+#',$name,$matches)){
        return $this->locatie($matches[1]);
      }else{
        global $base_url; 
        $url = $base_url.'/uitgaan';
//        drupal_goto($url,array(),301);   
      } 
  }
 
  private function locatie ($venue_id){
    $db = new Muziek_db(); 
    $venue = $db->get_venue($venue_id);
    if (!$venue) {
      drupal_not_found();
      drupal_exit();
    }
     
    $content = theme('locaties',array(
      'venue' => $venue,
      'events' => Muziek_db::get_venue_gigs($venue_id),
      'lang_prefix' => Muziek_util::lang_url(), 
    ));

    $title = $venue['Title'];
    $this->set_head_title($title . ' - ' . $venue['City_name']);
    $this->set_title($title);
    drupal_add_js(array('locatiepagina' => array(
        'status' => 'venue',
        'venue'=>$venue
    )), 'setting');
      
    return array('html'=>$content);    
  } 

  public function index () {
    //@todo redirect
    if (isset($_GET['l'])) {
      $l = $_GET['l'];
      $data = Muziek_util::loadGigdata();
      $locationData = Muziek_util::loadLocationIndex();
      $this->init_view();

      // $bodyClass= 'locationPage';

      $locationlink = $locationData->xpath('//link[text()="'.$l.'"]');  
      if (count($locationlink)){
        $location = $locationlink[0]->xpath("parent::*"); 
        $location= $location[0];
        
        $str = Muziek_util::template(Array(
          'location_title'=>$location->title,
          'city'=>$location->city,
          'cityno'=>$location->cityno,
          'location_desc'=>$location->desc,
          'zip'=>$location->zip,
          'street'=> $location->street,
          'location_link'=> $location->link,
          'location_location'=> $location->location,
          'streetnumber'=> $location->streetnumber,
          'twitter'=> $location->twitter,
          'streetnumberAddition'=> $location->streetnumberAddition),$this->view->location);       
        $content = $str; 
        $titletag = join(' - ',Array($location->title,$location->city));
        $this->set_head_title($titletag); 
        $this->set_title($location->title); 
        $desc = join(' - ',Array($titletag,$location->desc) );
        $this->set_meta_desc($desc); 
        $key = $location->xpath("parent::*");
        $key = (string)$key[0]['value'];
        
        $events = $data->xpath('//day/event/src[text()="'.$key.'"]/parent::*');
        $eventArr = Array();
        if (!empty($events)){
          foreach($events as $event){
            //var_dump($event->title);
            $date = $event->date;
            $datearr = explode('-',$date);
            $weekday = Muziek_util::getWeekDay($datearr[2],$datearr[1],$datearr[0]);
            $displaydate = $weekday . ' '. join('-',Array($datearr[2],$datearr[1],$datearr[0]));
            
            $str = Muziek_util::template(Array(
              'internallink'=>'/gig/?datestring='.$date.'&g='.rawurlencode($event->link),
              'link'=>$event->link,
              'title'=>$event->title, 
              'desc'=>$event->desc,
              'date'=>$displaydate),$this->view->event);
            $desc .= ' -- '. htmlspecialchars(strip_tags($event->title));

            $eventArr []= $str; 
          }
        } 
        sort($eventArr);
        foreach($eventArr as $val) {
          $content .=$val;
        }
        $content  .= '<p><a target="_blank" href="'.$location->link.'">'.$location->link. '</a></p>'; 
      }
      if (isset($_REQUEST['ajax'])){
        return array('html_fragment'=>$content); 
      } else {
        return array('html'=>$content);  
      }  
    }
  }
  
  function pagina(){
    echo 'hier';
    exit; 
  }

  function info(){
      $this->init_view();    
      $l = $_REQUEST['l'];

      $data = Muziek_util::loadGigdata();
      $locationData = Muziek_util::loadLocationIndex();
          
      $locationlink = $locationData->xpath('//link[text()="'.$l.'"]');  
      if (count($locationlink)){
        $location = $locationlink[0]->xpath("parent::*"); 
        $location= $location[0];
        $str = Muziek_util::template(Array(
          'location_title'=>(string)$location->title,
          'city'=>(string)$location->city,
          'cityno'=>(string)$location->cityno,
          'location_desc'=>$location->desc,
          'zip'=>$location->zip,
          'street'=> $location->street,
          'location_link'=> $location->link,
          'location_location'=> $location->location,
          'streetnumber'=> $location->streetnumber,
          'streetnumberAddition'=> $location->streetnumberAddition),$this->view->location);

        return array('html_fragment'=>$str);
      }
  }
}

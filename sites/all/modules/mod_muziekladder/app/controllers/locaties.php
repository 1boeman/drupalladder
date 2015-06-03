<?php 

class Locaties extends Controller {

  function __call($name, $arguments) {
      if (preg_match('#^([\-a-z\.0-9_]+)-[A-Z]#',$name,$matches)){
        //regular venue_id
        
        return $this->locatie($matches[1]);
      } elseif (preg_match('#^([0-9]+_[A-Z][a-z\-]+)-[A-Z]#',$name,$matches)){
        $id_arr = explode('-',$name);
        if (count($id_arr) > 2){
          $id = $id_arr[0].'-'.$id_arr[1];
          return $this->locatie($id);
        } else {
        //diverse_locaties venue_id
          return $this->locatie($matches[1]);
        }
      } else {
        global $base_url; 
        $url = $base_url.'/uitgaan';
        drupal_goto($url,array(),301);   
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
      $p = $_GET['l'];

      header( "HTTP/1.1 301 Moved Permanently" );
      header( "Status: 301 Moved Permanently" );
      header( "Location: ".rawurldecode($p) );
      exit;
    } else{
      drupal_not_found();
      drupal_exit();
    }
      
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

<?php

class Gig extends Controller{
  
  function index(){
    if (!isset($_GET['g'])){
      drupal_not_found();
      drupal_exit();   
    }
  
    $url = rawurldecode($_GET['g']);
    $db  = new Muziek_db(); 
    $gig = false; 
    $id = 0;
    $date = 0; 
    if (isset( $_GET['id'] )){
      $id = $_GET['id'];
      $gig = $db->get_gig_by_id($id);
    }
    
    if (!$gig && isset($_GET['datestring'])){
      $date = $_GET['datestring']; 
      $gig = $db->get_gig($date,$url);
    }
    
    if (!$gig){
       $render_array = $this->dont_redirect($id,$date,$url);
       return array('render_array'=>$render_array);
    } else {
   
      $gig = $gig[0];    
     
      $venue = $db->get_venue($gig['Venue']);

      if (!$venue){
        $render_array = $this->dont_redirect($id,$date,$url); 
      } else {

        // display the gig
        $prefix =  Muziek_util::lang_url();

        $hd = Muziek_util::human_date($gig['Date']);
        $human_date = $hd['dayname']. ' ' .$hd['daynumber'].' '.$hd['monthname']. ' ' .$hd['year'];

     
        $html = theme('gig',array(
          'gig'=>$gig,
          'venue'=>$venue,
          'prefix'=> $prefix,
          'human_date' => $human_date,
          'location_link'=>$prefix.'locaties/'.$venue['Id'].'-'.rawurlencode($venue['City_name'])
        ));

        #  var_dump($venue);
       # var_dump($gig); 
      }

      $titletag = htmlspecialchars($gig['Title']) .' - '. htmlspecialchars($venue['Title']) . ' - '. $venue['City_name'] . ' - ' .$human_date; 
      $this->set_head_title($titletag);
    }
    
    if (isset($_GET['ajax'])){

      return array('html_fragment'=>$html);

    } else {

      $render_array = array(
        'gig'=>array(
          '#type'=>'markup',
          '#markup'=>$html,
        ),
      );

      return array('render_array'=>$render_array);
    }
  }
 
  function dont_redirect($id, $date, $url){
    $venue = false; 
    $location_link = false;
    $title_tag = array();  
    if ($id) {
      // first check if id contains venue_id
      $array_id = explode('_',$id);
      if (count($array_id) > 1){
        $venue_id = implode('_',array_slice($array_id,1));
        $db = new Muziek_db(); 
        $venue = $db->get_venue($venue_id);
        if ($venue){
          $location_link = Muziek_util::lang_url() .'locaties/'.$venue['Id'].'-'.rawurlencode($venue['City_name']);
          $title_tag[]=$venue['Title'];
          $title_tag[]=$venue['City_name']; 
        }else{
          $this->redirect($url);  
        }
        // Only show the link if it seems safe  - contains the the venue.
        // We dont want folks displaying any old link here. 
        if (!stristr($url,$venue['Link'])){
          $url = false; 
        }
      } else {
        $this->redirect($url);  
      } 
    } else {
      $this->redirect($url);  
    }
    
    if ($date){
      $hd = Muziek_util::human_date($date);
      $human_date = $hd['dayname']. ' ' .$hd['daynumber'].' '.$hd['monthname']. ' ' .$hd['year'];
      $title_tag[]=$human_date; 
    }else{
      $this->redirect($url);  
    }

    $title_tag = join(' - ',$title_tag); 
    $this->set_head_title($title_tag);

    $html = theme('gig',array(
      'gig'=>false,
      'url_only'=> 1,
      'url'=>$url,
      'title_tag' => $title_tag,
      'venue'=>$venue,
      'prefix'=> Muziek_util::lang_url(),
      'human_date' => $human_date,
      'location_link'=> $location_link
    ));

    $render_array = array(
      'gig'=>array(
        '#type'=>'markup',
        '#markup'=>$html,
      ),
    );
    
    return $render_array; 
  }
  
  function redirect($url){
      header("HTTP/1.1 303 See Other");
      header( "Location: ".$url );
      exit(); 
  }

}

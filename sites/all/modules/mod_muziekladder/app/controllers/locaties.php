<?php 

class Locaties extends Controller {

  function __call($name, $arguments) {
    if (preg_match('#^([0-9]+_[A-Z][a-z\-]+)-[A-Z]#',$name,$matches)) {
      //diverse_locaties venue_id
      $id_arr = explode('-',$name);
      
      if (count($id_arr) > 2){
        $id = $id_arr[0].'-'.$id_arr[1];
        return $this->locatie($id);
      } else {
        return $this->locatie($matches[1]);
      }

    } elseif (preg_match('#^([\-a-zA-Z\.0-9_]+)-[A-Z]#',$name,$matches)) {
      //regular venue_id
      return $this->locatie($matches[1]);
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
      $content = theme('locaties',array(
        'not_found' =>1,
        'lang_prefix' => Muziek_util::lang_url() 
      ));
    
      $this->set_head_title(t('Currently no information available for this location'));

    } else {
      // venue_found
      // get node
      $diverse_locaties = strstr($venue['Title'],'diverse locaties'); 
      if (!$diverse_locaties){
        $node = $this->get_location_node($venue_id);
        if (!$node) { 
          $this->create_locatie_node($venue,$venue_id);
          $node = $this->get_location_node($venue_id);
        }
        
        $node_view = node_view($node);
        $node_view['comments'] = comment_node_page_additions($node);
        $rendered_node = drupal_render($node_view);
      }else{
        $rendered_node = '';  
      }
       
      //render node + comments and add it tot $content
      $lang_prefix = Muziek_util::lang_url();
      $content = theme('locaties',array(
        'venue' => $venue,
        'diverse_locaties' => $diverse_locaties,
        'venue_node' => $rendered_node,
        'events' => Muziek_db::get_venue_gigs($venue_id),
        'lang_prefix' => $lang_prefix, 
      ));
      $title = $venue['Title'];
      $this->set_head_title($title . ' - ' . $venue['City_name']);
      $this->set_title($title);
      
      $this->crumbs(array(
        array(
          'text'=>t('Locations'),
          'link'=>$lang_prefix . 'uitgaan'
        ),
        array(
          'text' => $venue['City_name'],
          'link' => Muziek_util::city_link(
            array(
              'Id' => $venue['Cityno'],
              'Name'=> $venue['City_name']
            ),'uitgaan'
          )
        ),
        array('text'=> $venue['Title'])
      ));

      drupal_add_js(array('locatiepagina' => array(
          'status' => 'venue',
          'venue'=>$venue
      )), 'setting');
    }
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

  private function get_location_node($venue_id,$single = true){
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
          ->entityCondition('bundle', 'locatiespagina') // ex. article
          ->propertyCondition('status', 1) // published nodes
          ->fieldCondition('field_locaties_id','value',$venue_id, '=');
    $result= $query->execute();

    if (isset($result['node'])) {
      $nids = array_keys($result['node']);
      $items = node_load_multiple($nids);
      //var_dump($items)  ;
      if ($single) {
        return array_pop($items); 
      } else {
        return $items;
      }
    }
    return false;
  }

  private function create_locatie_node($venue,$venue_id) {
    global $user;
    // entity_create replaces the procedural steps in the first example of
    // creating a new object $node and setting its 'type' and uid property
    $values = array(
      'type' => 'locatiespagina',
      'uid' => $user->uid,
      'status' => 1,
      'promote' => 0,
    );
    $entity = entity_create('node', $values);

    // The entity is now created, but we have not yet simplified use of it.
    // Now create an entity_metadata_wrapper around the new node entity
    // to make getting and setting values easier
    $ewrapper = entity_metadata_wrapper('node', $entity);
     // Using the wrapper, we do not have to worry about telling Drupal
    // what language we are using. The Entity API handles that for us.
    $ewrapper->title->set($venue['Title']. ', '.$venue['City_name']);
    $ewrapper->field_locaties_id->set($venue_id);

    // Setting the body is a bit different from other properties or fields
    // because the body can have both its complete value and its
    // summary
    $content = theme('locatie_reviews_body', array (
        'venue' => $venue 
    ));
    
    $ewrapper->body->set(array('value' => $content));
    
    // Setting the value of an entity reference field only requires passing
    // the entity id (e.g., nid) of the entity to which you want to refer
    // The nid 15 here is just an example.
#    $ref_nid = 15;
    // Note that the entity id (e.g., nid) must be passed as an integer not a
    // string
 #   $ewrapper->field_my_entity_ref->set(intval($ref_nid));

    // Entity API cannot set date field values so the 'old' method must
    // be used

    // Now just save the wrapper and the entity
    // There is some suggestion that the 'true' argument is necessary to
    // the entity save method to circumvent a bug in Entity API. If there is
    // such a bug, it almost certainly will get fixed, so make sure to check.
    $ewrapper->save();
  }

}

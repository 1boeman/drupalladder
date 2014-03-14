<?php 

class Locaties extends Controller {
  public function index () {
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
          'twitterwidgetid'=> $location->twitterwidgetid,
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
        if (count($events)>1){
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

      return array('html'=>$content); 
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
//	var_dump($location[0]); exit;
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

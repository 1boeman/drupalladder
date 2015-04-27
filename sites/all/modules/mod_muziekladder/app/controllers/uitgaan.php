<?php 
class Uitgaan extends Controller {
    
    function __call($name, $arguments) {
      //city based lists
      // get cityno
      if (preg_match('/([0-9]+)-[a-zA-Z]*/',$name,$matches)){
        return $this->city_main($matches[1]);
      }else{
        global $base_url; 
        $url = $base_url.'/uitgaan';
        drupal_goto($url,array(),301);   
      } 
    }
   
    public function city_main($cityno){
      $lang_prefix = Muziek_util::lang_url(); 
      $mdb = new Muziek_db(); 
      $city = $mdb->get_city($cityno);
      $venues = $mdb->get_city_venues($cityno,false);
      drupal_add_js(array('locatiepagina' => array(
        'status' => 'city_main',
        'city' => $city
      )), 'setting');
        
      // Get the current language
      global $language;
       
      // Setup the EntityFieldQuery
      $query = new EntityFieldQuery();
      $query->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', 'stadspagina') // ex. article
            ->propertyCondition('status', 1) // published nodes
            ->propertyCondition('language', $language->language, '=')
            ->fieldCondition('field_cityno','value',$cityno, '=')
            ->fieldOrderBy('field_sortorder','value'); 
      $result= $query->execute();
      if (isset($result['node'])) {
        $nids = array_keys($result['node']);
        $items = node_load_multiple($nids);
        //var_dump($items)  ;
      }
      
      $content = theme('uitgaan',array(
        'city' => $city,
        'venues' => $venues,
        'nodes' => $items,
        'lang_prefix'=> $lang_prefix,
        'agenda_link' => $lang_prefix.'muziek/'.$city['Id'].'-'.rawurlencode($city['Name']), 
        'tpl'=>'city_main'
        
      ));
      $title = $city['Name'].' - '. t(' find concerts, parties, events');
      $this->set_head_title($title);
      $this->set_title($title);
      
      return array('html'=>$content); 
    }


    public function index () {
      drupal_add_js(array('locatiepagina' => array('status' => 'index')), 'setting');
     
      $lang_prefix = Muziek_util::lang_url(); 
      drupal_add_js(array('city_names' => array('en'=>$this->countrynames_EN,'nl'=>$this->countrynames) ), 'setting');
     
      $cities = Muziek_db::get_cities(); 
      drupal_add_js(array('muziek_cities'=>$cities),'setting');
      
      $dircontent = scandir(MUZIEK_DATA_UITGAAN);
      $content = theme('uitgaan',array(
        'cities'=>$cities,
        'lang_prefix'=>$lang_prefix,
        'tpl'=>'index'
      ));
      
      if ( isset($_GET['c'])) {
          // @todo : redirect
        $steden = explode(',',$_GET['c']);
        foreach($steden as $stad){
            $stadnaam = $stad.'.html';
            if (in_array($stadnaam,$dircontent))
            {
                $content .= file_get_contents(MUZIEK_DATA_UITGAAN.'/'.$stad.'.html');
                $titletag = $_GET['c'].' - locaties voor live muziek, optredens en feesten';
            }
        }


      } else {
          /* index */     
          if ($file = file_get_contents(MUZIEK_GEODATA_JSON)){
              drupal_add_js(array('muziekladder'=>array('location_data'=>json_decode($file))), 'setting');
          }   
          $titletag = t('Find concerts, clubs, festivals and parties' );          
      }
      $this->set_head_title(ucfirst($titletag));
      $this->set_title(ucfirst($titletag));
      
      if ($_GET['ajax']){
        return array('html_fragment' => $content);
      }
      return array('html'=>$content); 
    }

    /**
     **
     **/
    function getgeo(){
        if (!isset($_REQUEST['l'])) {
          throw new Exception("getgeo needs a location param");
        } 
        
        if (!(int)$_REQUEST['l']) {
          throw new Exception("getgeo location param ==  0");
        } 
         
        $request_location = trim($_REQUEST['l']);
        $db = new Muziek_db(); 
        $city = $db->get_city($request_location);

        /* if cache file exists -->  */
        if($file = @file_get_contents(MUZIEK_GEODATA_JSON)){
            $filedata = json_decode($file);
            // location requested: 
            // return the requested location or add it if is not yet present
            if (property_exists($filedata,$request_location)){
              return array('json'=>$filedata->$request_location); 
            } elseif ($data = $this->getCityData($city['Name'],$city['Country_name'])){
                $storefound = json_decode($data);
                //normalize to prevent duplication
                $filedata->$city['Id']=$storefound;
                file_put_contents(MUZIEK_GEODATA_JSON,json_encode($filedata));
                return (array('json'=>$storefound));
            } else {
              throw new Exception("getgeo cannot find location");
            }
        } else {
        // no cachefile yet - create it :
              if($data = $this->getCityData($city['Name'],$city['Country_name'])){
                $save = array ($city['Id'] => json_decode($data));
                file_put_contents(MUZIEK_GEODATA_JSON,json_encode($save));
                return array('json'=> json_decode($data));
              }else{
                throw new Exception("getgeo cannot find location2");
              }
        }
    }
    
    private function getCityData($cityName,$country){
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($cityName.', '.$country).'&sensor=false';
        error_log($url); 
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));
        // Send request
        $resp = curl_exec($curl);
        curl_close($curl);
        if (stristr($resp,'address_components')){
           return $resp;
        }
        return false;  
    }
}

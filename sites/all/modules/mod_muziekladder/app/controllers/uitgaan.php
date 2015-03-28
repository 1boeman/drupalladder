<?php 
class Uitgaan extends Controller {
    
    function __call($name, $arguments) {
      //city based lists
      // get cityno
      if (preg_match('/([0-9]+)-[a-zA-Z]*/',$name,$matches)){
        //var_dump ($matches[1]);
      } 
    }
   

    public function index () {
        drupal_add_js(array('city_names' => array('en'=>$this->countrynames_EN,'nl'=>$this->countrynames) ), 'setting');

        $dircontent = scandir(MUZIEK_DATA_UITGAAN);
        $content = '';

        if ( isset($_GET['c'])) {
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
            $bodyClass = 'locaties';
            if ($file = file_get_contents(MUZIEK_GEODATA_JSON)){
                drupal_add_js(array('muziekladder'=>array('location_data'=>json_decode($file))), 'setting');
            }   
            $content .= file_get_contents(MUZIEK_DATA_UITGAAN.'/stedenlijst.html');
            $titletag = 'Locaties voor live muziek, optredens en feesten';          
        }
        $this->set_head_title(ucfirst($titletag));
        
        if ($_GET['ajax']){
          return array('html_fragment' => $content);
        }
        return array('html'=>$content); 
    }

    function getgeo(){
        $request_location = false;
        if (isset($_REQUEST['l'])){
            $request_location = trim($_REQUEST['l']);
        }
        /* if cache file exists -->  */
        if($file = @file_get_contents(MUZIEK_GEODATA_JSON)){
            $filedata = json_decode($file);
            // location requested: return the requested location or add it if is not yet present
            if ($request_location){
                if (isset($_REQUEST['c'])){
                    $country = $this->countrynames_EN[(int)$_REQUEST['c']];    
                }else{
                    $country = $this->countrynames_EN[0];    
                }
                $found = false;
                foreach($filedata as $value){
                    if (!isset($value->results[0])){
                        continue;
                    }
                        
                    $cityname = $value->results[0]->address_components[0]->long_name;
                    if (strtolower($request_location) === strtolower($cityname)){ //avoid duplication
                        $found =  $value;   
                    }
                }
                
                if (!$found && $data = $this->getCityData($request_location,$country)){
                    $storefound = json_decode($data);
                    //normalize to prevent duplication
                    if ($storefound->results[0]->address_components[0]->long_name){
                        $storefound->results[0]->address_components[0]->long_name = $request_location;
                        $found = $storefound;
                        $filedata[]=$found;
                        file_put_contents(MUZIEK_GEODATA_JSON,json_encode($filedata));
                    }
                }
                return array('json'=>$found); 
            } else {
                // no specific location requested : return all the data
                return array('json'=>$filedata); 
            }
        }else{
            // no cachefile yet - create it :
            if (isset($request_location)){
                $data = $this->getCityData($request_location); 
                $save = array (json_decode($data));
                file_put_contents(MUZIEK_GEODATA_JSON,json_encode($save));
                return array('json_string'=> $data);
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
        return $resp;
    }
}

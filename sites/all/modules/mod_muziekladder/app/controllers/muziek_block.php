<?php 

class Muziek_block extends Controller {

    public function muziekladder_nieuws_block(){


    }

    public function muziekladder_nieuws_block_1(){
      $cntnt = file_get_contents(MUZIEK_COMPONENTS.'/randomgigs').file_get_contents(MUZIEK_COMPONENTS.'/randomgigs2');
      $list = ''; 
      
      if (drupal_is_front_page()){      
        $cities = Muziek_db::get_cities();  
        $list = '<nav><h2>Stadagenda\'s</h2><ul class="city_agendas">';
        foreach($cities as $key=>$value){
          $list .= '<li><a href="/muziek/'.$value['Id'].'-'.$value['Name'].'">'.$value['Name'].'</a></li>';
        } 
        $list .= '<ul></nav>';
      }
       
      return array(
        'content'=>$list.$cntnt 
      ); 
    }

    public function muziekladder_nieuws_block_2(){
      return array('content'=> ''); 
    }

}

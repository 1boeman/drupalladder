<?php 

class Muziek_block extends Controller {

    public function muziekladder_nieuws_block(){

    }

    public function muziekladder_nieuws_block_1(){
      $list = ''; 
      
      if (drupal_is_front_page()){      
        $cities = Muziek_db::get_cities();  
        $list = '<nav><h2>'.t('City calendars').'</h2><ul class="city_agendas">';
        $lang_prefix = Muziek_util::lang_url();  
        
        foreach($cities as $key=>$value){
          $list .= '<li><a href="'.$lang_prefix.'muziek/'.$value['Id'].'-'.$value['Name'].'">'.$value['Name'].'</a></li>';
        } 
        $list .= '<ul></nav>';
      }
       
      return array(
        'content'=>$list 
      ); 
    }

    public function muziekladder_nieuws_block_2(){
      return array('content'=> ''); 
    }

}

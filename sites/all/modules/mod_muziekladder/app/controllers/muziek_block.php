<?php 

class Muziek_block extends Controller {

    public function muziekladder_nieuws_block(){


    }

    public function muziekladder_nieuws_block_1(){

      return array(
        'content'=> file_get_contents(MUZIEK_COMPONENTS.'/randomgigs').file_get_contents(MUZIEK_COMPONENTS.'/randomgigs2')
      ); 
    }

    public function muziekladder_nieuws_block_2(){
      return array('content'=> ''); 
    }

}

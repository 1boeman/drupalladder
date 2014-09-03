<?php
class Muziekformulier extends Controller {
  function __construct(){ }

  function index() {

    $this->set_head_title('Tip muziekladder');
    $this->set_title('Tip de Muziekladder agenda'  );
  
    $form = drupal_get_form('mod_muziekladder_mailtipform'); 
    return array('render_array'=>$form); 
  }
}

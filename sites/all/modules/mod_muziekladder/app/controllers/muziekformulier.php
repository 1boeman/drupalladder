<?php
class Muziekformulier extends Controller {
  function __construct(){ }

  function index() {
    $form = array();
   
    $form['example_textfield'] = array(
        '#type' => 'textfield',
        '#title' => 'Titel',
          '#default_value' => 'sdvvw',
   );

   $form['submit_button']=array(
        '#value'=>'Verzendenen', 
        '#type'=>'submit'
   ); 



    return array('render_array'=>$form); 
  }
}

<?php

class Muziekformulier extends Controller {
  function __construct() {
  
  }

  function index() {
    $this->set_head_title(t('Muziekladder recommendation'));
    $this->set_title(t('Recommend stuff to the Muziekladder Calendar'));
    $form = drupal_get_form('mod_muziekladder_mailtipform');
    $tips = Muziek_util::showTips();
    
    return array('render_array'=>array('muziekform'=>$form,
      'tips'=> array(
        '#type'=>'markup',
        '#markup'=>$tips,
        '#prefix' => '<div class="printed-tips eventfull clearfix">
                        <h3>'.t('Recent recommendations').':</h3>',
        '#suffix' => '</div>',
      )
    )); 
  }

  function updatenode() {
    $file_name = array_pop(explode('/',$_GET['q']));
    Muziek_util::saveTipNode($file_name);


  }


  function edit() {
    global $user;  
  
    $file_name = array_pop(explode('/',$_GET['q']));

    //check if its the legitimate owner editing 
    $gig = Muziek_util::getTip($file_name);
    if ( $gig['uid'] !== $user->uid ){
      Muziek_util::deny();
    }
    $gig['file_name'] = $file_name;

    $form = array(
      '#type'=>'markup',
      '#markup'=>'<p>'.t('The event you are trying to edit is not available (anymore)').'</p>',
    );
    if(count($gig)){
      $this->set_head_title(t('Muziekladder recommendation'));
      $this->set_title(t('Edit'));
      $form = drupal_get_form('mod_muziekladder_mailtipform',$gig);
    }

    return array('render_array'=>array(
        'muziekform'=>$form,
    )); 
  }

} 

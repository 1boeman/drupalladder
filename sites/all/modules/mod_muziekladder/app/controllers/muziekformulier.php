<?php

class Muziekformulier extends Controller {
  function __construct() {
  
  }

  function index() {
    global $user; 
    $this->set_head_title(t('Muziekladder recommendation'));
    $this->set_title(t('Recommend stuff to the Muziekladder Calendar'));
    $formfree = drupal_get_form('mod_muziekladder_mailtipform');
    $form = drupal_get_form('mod_muziekladder_freetextform');

    $tips = Muziek_util::showTips();
    
    $rv = array(
      'render_array'=>array(
      'freeform'=> $formfree,
      'muziekform'=>$form,
      'tips'=> array(
        '#type'=>'markup',
        '#markup'=>$tips,
        '#prefix' => '<div class="printed-tips eventfull clearfix">
                        <h3>'.t('Recent recommendations').':</h3>',
        '#suffix' => '</div>',
      ),
    ));
    if ($user->uid){
       $rv['render_array']['#prefix'] = '<ul class="nav nav-tabs" id="formtabs">
        <li><a href="#tab-1">'.t('Event form').'</a></li>
        <li><a href="#tab-2">'.t('Free text').'</a></li>
      </ul>';
    }
    
    return $rv;   
  }

  function updatenode() {
    $file_name = array_pop(explode('/',$_GET['q']));
    Muziek_util::saveTipNode($file_name);
  }

  function delete() {
    global $user;  
    $file_name = array_pop(explode('/',$_GET['q']));

    $rv = array(
      '#type'=>'markup',
      '#markup'=>'<p>'.t('The event you are trying to delete is not available (anymore)').'</p>',
    );
    
    $gig = Muziek_util::getTip($file_name);
    if(count($gig)){
      //check if its the legitimate owner editing 
      if ( $gig['uid'] !== $user->uid ){
        Muziek_util::deny();
      }
      $gig['file_name'] = $file_name;

      if ( Muziek_util::deleteTip( $file_name ) === 1 ){
        $this->set_head_title(t('Muziekladder recommendation'));
        $this->set_title(t('Delete'));
        $rv = array(
          '#type'=>'markup',
          '#markup'=>'<p>'.t('The event has been deleted').'</p>',
        );
      }
    }

    return array('render_array'=>array(
        'muziekform'=>$rv,
    )); 
  }

  function edit() {
    global $user;  
  
    $file_name = array_pop(explode('/',$_GET['q']));

    $form = array(
      '#type'=>'markup',
      '#markup'=>'<p>'.t('The event you are trying to edit is not available (anymore)').'</p>',
    );
    $gig = Muziek_util::getTip($file_name);

    if(count($gig)){
      //check if its the legitimate owner editing 
      if ( $gig['uid'] !== $user->uid ){
        Muziek_util::deny();
      }
      $gig['file_name'] = $file_name;

      $this->set_head_title(t('Muziekladder recommendation'));
      $this->set_title(t('Edit'));
      $form = drupal_get_form('mod_muziekladder_mailtipform',$gig);
    }

    return array('render_array'=>array(
        'muziekform'=>$form,
    )); 
  }
} 

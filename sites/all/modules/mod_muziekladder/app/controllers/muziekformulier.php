<?php

class Muziekformulier extends Controller {
  function __construct() {

  }

  function index() {
    global $user;
    global $language;
    if ($language->language == 'nl'){
      $legend2 =  ' <p>Tips worden op deze pagina geplaatst, en na controle ook aan de Muziekladder agenda toegevoegd.
 </p><p>Voor algemene opmerkingen of vragen kunt u ons ook mailen (info at muziekladder.nl), of contact opnemen via twitter.: <a target="_blank" href="https://twitter.com/muziekladder">@Muziekladder</a></p> ';
      $legend = '<p><em>Geen zin om het formulier in te vullen? Kies dan voor "vrije tekst".</em></p>';

    } else {
      $legend2 = '<p>Your recommendations will be placed on this page, and after a human check also in the Muziekladder Calendar.</p>'.
      '<p>For general remarks you may also mail (info at muziekladder.nl) or use twitter: <a target="_blank" href="https://twitter.com/muziekladder">@Muziekladder</a></p>';
      $legend = '<p><em>Don\'t have time to fill out the entire form? Select  "free text"</em> </p>';

    }

    $this->set_head_title(t('Muziekladder recommendation'));
    $this->set_title(t('Recommend stuff to the Muziekladder Calendar'));
    $formfree = drupal_get_form('mod_muziekladder_mailtipform');
    $form = drupal_get_form('mod_muziekladder_freetextform');

    if (user_is_logged_in()){
      $tips = Muziek_util::showTips();
      drupal_add_js(array('rows'=>$tips), 'setting');      
    }

    $view = views_get_view('recent_tips');
    $view->set_display('page');
    $args = $view->args;
    $args[0] = $node->nid;
    $view->set_arguments($args);
    $view->pre_execute();
    $view->execute();

    $response = $view->render();

    $rv = array(
      'render_array'=>array(
        'freeform'=> $formfree,
        'muziekform'=>$form,
        'view_tips'=>array(
          '#type'=>'markup',
          '#markup'=> $response,
          '#prefix' => '<div class="printed-tips eventfull clearfix">
                          <h3>'.t('Recent recommendations').':</h3>',
          '#suffix' => '</div>',
        ),
    ));
    if ($user->uid) {
       $rv['render_array']['#prefix'] = '<div class="eventfull tab-container">
        <div class="legenda2">'.$legend2.'</div>
         <div class="legenda1">'.$legend.'</div>
      <ul class="nav nav-tabs" id="formtabs">
          <li><a href="#tab-1">'.t('Event form').'</a></li>
          <li><a href="#tab-2">'.t('Free text').'</a></li>
        </ul></div>';
    }

    return $rv;
  }
/*
  function updatenode() {
    $file_name = array_pop(explode('/',$_GET['q']));
    Muziek_util::saveTipNode($file_name);
  }
*/
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


      $nid = isset($gig['node_id']) ? $gig['node_id'] : false;

      if ( Muziek_util::deleteTip( $file_name,$nid ) === 1 ){
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
    $get_q =  $_GET['q'];
    $q = explode('/',$get_q);
    $file_name = array_pop($q);
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

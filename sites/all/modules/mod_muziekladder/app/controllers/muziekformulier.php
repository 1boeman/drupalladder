<?php

class Muziekformulier extends Controller {
  function __construct() {

  }

  function index() {
    global $user;
    global $language;
    if ($language->language == 'nl'){
      $legend2 = '<p><strong>Waarover gaat uw tip?</strong></p>';
      $legend = '<p><em>Kies een van de tabs hieronder voor een onderwerp.</em></p>';

      $this->set_title(t('Aanraden'));
      $this->set_head_title(t('Tips voor Muziekladder'));
 
    } else {
      $legend2 ='<p><strong>What\'s your recommendation about?</strong></p>';
      $legend = '<p><em>Please choose one of the tabs below to specify a subject.</em> </p>';
      $this->set_title(t('Recommend'));
      $this->set_head_title(t('Muziekladder recommendations'));
    }

    $formartist = drupal_get_form('mod_muziekladder_artistform');
    $formfree = drupal_get_form('mod_muziekladder_mailtipform');
    $formlink = drupal_get_form('mod_muziekladder_linkform');
    $form = drupal_get_form('mod_muziekladder_freetextform');

    if (user_is_logged_in()){
      $tips = Muziek_util::showTips();
      drupal_add_js(array('rows'=>$tips), 'setting');
    }

    $response = $this->recent_tips();
    $response .= '<a class="naar-agenda-link btn btn-inverse" href="/'.$language->prefix.'/archief"> '.t('More').' &raquo;</a>';
    $rv = array(
      'render_array'=>array(
        'freeform'=> $formfree,
        'linkform'=>$formlink,
        'muziekform'=>$form,
        'artistform'=>$formartist,
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
              <li><a href="#tab-1"><i class="icon-music"></i> '.t('Concert or event').'</a></li>
              <!-- li><a href="#tab-3">'.t('A link').'</a></li -->
              <li><a href="#tab-2"><i class="icon-star"></i> '.t('Promote artist or group').'</a></li>
              <!-- li><a href="#tab-4">'.t('Something else').'</a></li -->
            </ul>
        </div>';
    }

    return $rv;
  }

  function recent_tips_ajax(){
    return array(
      'html_fragment' => $this->recent_tips() 
    );
  } 
  
  private function recent_tips () {
    $view = views_get_view('recent_tips');
    $view->set_display('page');
    $view->pre_execute();
    $view->execute();

    return $view->render();
  }

  function delete() {
    global $user;
    $file_name = array_pop(explode('/',$_GET['q']));

    $rv = '<p>'.t('The page you are trying to delete is not available (anymore)').'</p>';

    if (preg_match('/^n_/',$file_name)){
      $node_id = str_replace('n_','',$file_name);
      if ($node = node_load($node_id)) {
        if (Muziek_util::can_i_edit($node)) {
          node_delete($node_id);
         $rv = '<p>'.t('The artist page has been deleted').'</p>';
        }
      }
    } else {

      $gig = Muziek_util::getTip($file_name);
      if(count($gig)){
        //check if its the legitimate owner editing
        if ( $gig['uid'] !== $user->uid ){
          Muziek_util::deny();
        }
        $gig['file_name'] = $file_name;
        $nid = isset($gig['node_id']) ? $gig['node_id'] : false;

        if ( Muziek_util::deleteTip( $file_name,$nid ) === 1 ){
//          $this->set_head_title(t('Muziekladder recommendation'));
  //        $this->set_title(t('Delete'));
          $rv = '<p>'.t('The event has been deleted').'</p>';
        }
      }
    }
    
    return array( 'html_fragment' => $rv );
  }

  function edit() {
    global $user;
    $form = array(
      '#type'=>'markup',
      '#markup' =>
        '<p>'.t('The event you are trying to edit is not available (anymore)').'</p>',
    );

    $get_q =  $_GET['q'];
    $q = explode('/',$get_q);

    $file_name = array_pop($q);
    if (preg_match('/^n_/',$file_name)){
      $node_id = str_replace('n_','',$file_name);
      if ($node = node_load($node_id)) {
        if (Muziek_util::can_i_edit($node)) {
          $form = drupal_get_form('mod_muziekladder_artistform',$node);
        }
      }
      $this->set_title($node->title);
    } else {  
     $gig = Muziek_util::getTip($file_name);
      if(count($gig)){
        //check if its the legitimate owner editing
        if ( $gig['uid'] !== $user->uid ){
          Muziek_util::deny();
        }
        $gig['file_name'] = $file_name;
        $form = drupal_get_form('mod_muziekladder_mailtipform',$gig);
        $this->set_title($gig['title']);
      }
    }

    $this->set_head_title(t('Muziekladder recommendation'));
    return array('render_array'=>array(
      'muziekform'=>$form,
    ));
  }
}

<?php

function mod_muziekladder_artistform ($form, &$form_state, $node = false) {
  $form = array();
  $form['#prefix'] = '<div class="eventfull muziek-tab tab-2 nodisplay" id="artist_form_wrapper">' ;
  $form['#prefix'].= '<p>'.t('Promote an artist or group.').'</p>';  
  $form['#suffix'] = '</div>' ;

  $form['nid'] = array('#type' => 'hidden', '#value' =>0 );
 
  if( $node ){
    $form['nid']['#value'] = $node->nid;
  }

  $form['artist_name'] = array(
    '#type' => 'textfield', 
    '#title' => t('Artist or group name'), 
    '#size' => 120, 
    '#maxlength' => 128, 
    '#required' => TRUE,
  );

  if ($node){
    $form['artist_name']['#default_value'] = $node->title; 
  } 
  
  $form['link'] = array(
     '#type' => 'textfield',
     '#title' => t('Link to artist website or social media'),
     '#required' => true,
     '#attributes' =>array('placeholder' => 'http:// ...... '),
  );

  if ($node){
    $form['link']['#default_value'] = $node->field_url['und'][0]['value']; 
  } 

  $form['artist_image'] = array(
    '#type' => 'managed_file',
    '#name' => 'artist_image',
    '#title' => t('Image - poster, flyer or logo (optional) - jpg, gif or png'),
    '#description' => t("Image should be in .jpg, .gif or .png format."),
    '#upload_validators' => array(
      'file_validate_size' => array(5*1024*1024),
      'file_validate_extensions' => array('gif png jpg jpeg'),
    ),
    '#upload_location' => 'public://'
  );

  if ($node){
    $form['artist_image']['#default_value'] = $node->field_artist_image['und'][0]['fid']; 
  } 

  $form['artist_video'] = array(
    '#type' => 'textfield',
    '#title' =>t('Link to video or song'),
    '#attributes' => array('placeholder' => t('Youtube or other media links')),
  );

  if ($node){
    $form['artist_video']['#default_value'] = $node->field_media_url['und'][0]['value']; 
  } 

  $form['artist_info'] = array(
    '#type' => 'textarea',
    '#title' =>t('Additional information'),
    '#required' => true,
    '#attributes' => array('placeholder' =>
      t('Please share any relevant information. Genre, bio, discography,  etc. ')),
  );

  if ($node){
    $form['artist_info']['#default_value'] = $node->body['und'][0]['value']; 
  } 

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#attributes' => array('class'=>array('btn btn-large btn-inverse')),
  
    '#ajax' => array(
      'callback' => 'mod_muziekladder_artistform_ajax_submit',
      'wrapper' => 'artist_form_wrapper',
      'method' => 'replace',
      'effect' => 'fade',
    )
  );
  return $form;
}

function mod_muziekladder_artistform_ajax_submit($form, &$form_state) {
  $form_state['rebuild'] = TRUE;
 
  if (form_get_errors()) {
    return $form;   
  } 
  //
  $form_id = $form['#form_id'];

  // Get the unpopulated form.
  $form = form_get_cache($form_state['input']['form_build_id'], $form_state);

  // Rebuild it, without values
  $form_state['input'] = array();
  $form_state['values'] = array();
  
  $form = form_builder($form_id, $form, $form_state); 
  
  if ((int)$form_state['values']['nid']){

    $node_id = (int)$form_state['values']['nid'];
    $url = url(drupal_get_path_alias('node/' . $node_id), array('absolute' => TRUE));
    $message_success = t('Item successfully updated: ').'<a href="'.$url.'">' . $url .'</a>';
 
   } else {

    $message_success = t('Item successfully created.');

  }
 
  $output = array( '#markup' =>
      '<div class="alert alert-success">'.
      '  <button type="button" class="close" data-dismiss="alert">&times;</button>'.
      $message_success.' </div>');
  $form['some_text'] = $output;

 return $form;
}

function mod_muziekladder_artistform_validate($form, &$form_state) {
  // Validation logic.
 // var_dump($form_state);
  //exit;
}



function mod_muziekladder_artistform_submit($form, &$form_state) {
  save_artist_node($form_state['values']);
}


function save_artist_node($data){
  global $user;   
  
  $node_id = (int)$data['nid'];

  /* 
  if (isset($data['uid'])){
    $userobj = user_load($data['uid']);
    $data['user_name'] = $userobj->name;
  }
  */
 //  var_dump($data); exit;   
  if ($node_id){
    $node = node_load($node_id);
    if ( !Muziek_util::can_i_edit($node) ){
      throw new Exception('user '.$user->uid.' tried to update '. $node->uid);
    }
    $entity = entity_load_single('node',$node_id);

  } else {
   // entity_create replaces the procedural steps in the first example of
   // creating a new object $node and setting its 'type' and uid property
   $values = array(
      'type' => 'artist',
      'uid' => $user->uid,
      'status' => 1,
      'comment' => 2,
      'promote' => 1,
    );
    $entity = entity_create('node', $values);
  }

  $ewrapper = entity_metadata_wrapper('node', $entity);
  $ewrapper->title->set($data['artist_name']);
 
  $data['link'] = Muziek_util::http_link($data['link']);
  $data['artist_video'] = Muziek_util::http_link($data['artist_video']);
   
  $ewrapper->field_url[0]->set($data['link']); 
  $ewrapper->field_media_url[0]->set($data['artist_video']); 

  $body_content = $data['artist_info'];

  $ewrapper->body->set(array(
    'format' => 'full_html',
    'value' => $body_content));

  //$ewrapper->body->summary->set($my_body_content_summary);
  if($data['artist_image']){
    // deal with image if it was uploaded
    $uploaded_file = file_load($data['artist_image']);
    $uploaded_file->status = FILE_STATUS_PERMANENT;
    file_save($uploaded_file);
    $ewrapper->field_artist_image->file->set($uploaded_file);
  }else{
    $ewrapper->field_artist_image->set(NULL);
  }
  
  $ewrapper->save();
  $node_id = $ewrapper->getIdentifier();
  return $node_id;  
}
  


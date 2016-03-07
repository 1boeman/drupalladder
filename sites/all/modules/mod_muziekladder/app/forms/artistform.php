<?php

function mod_muziekladder_artistform ($form, &$form_state, $node = false) {
  $form = array();
  $form['#prefix'] = '<div class="eventfull muziek-tab tab-2 nodisplay" id="artist_form_wrapper">' ;
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
    '#attributes' => array('placeholder' =>
      t('Please share any relevant information here')),
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
  // validate the form
  drupal_validate_form('mod_muziekladder_artistform', $form, $form_state);
  // if there are errors, return the form to display the error messages
  if (form_get_errors()) {
    $form_state['rebuild'] = TRUE;
    return $form;
  }
  // process the form
  mod_muziekladder_artistform_submit($form, $form_state);
  $output = array(
    '#markup' => 'Form submitted.'
  );
  // return the confirmation message
  return $output;
}

function mod_muziekladder_artistform_validate($form, &$form_state) {
  // Validation logic.
 // var_dump($form_state);
  //exit;
}



function mod_muziekladder_artistform_submit($form, &$form_state) {
//  var_dump($form);
//  exit;

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
  // var_dump($data); exit;   
  if ($node_id){
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
  var_dump($node_id);
  return $node_id;
  
}
  


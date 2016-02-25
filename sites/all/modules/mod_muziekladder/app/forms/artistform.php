<?php

function mod_muziekladder_artistform (){

  $form = array();

  $form['#prefix'] = '<div class="eventfull muziek-tab tab-2 nodisplay"><div id="artist_form_wrapper">' ;
  $form['#suffix'] = '</div></div>' ;

  $form['artist_name'] = array(
    '#type' => 'textfield', 
    '#title' => t('Artist or group name'), 
    '#default_value' => $node->title, 
    '#size' => 120, 
    '#maxlength' => 128, 
    '#required' => TRUE,
  );
 
  $form['link'] = array(
     '#type' => 'textfield',
     '#title' => t('Link to artist website or social media'),
     '#required' => true,
     '#attributes' =>array('placeholder' => 'http:// ...... '),
  );


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

  $form['artist_video'] = array(
    '#type' => 'textfield',
    '#title' =>t('Link to video or song'),
    '#attributes' => array('placeholder' =>
      t('Youtube or other media links')),
  );



  $form['artist_info'] = array(
    '#type' => 'textarea',
    '#title' =>t('Additional information'),
    '#attributes' => array('placeholder' =>
      t('Please share any relevant information here')),
  );

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
  var_dump($form_state);
  exit;
}



function mod_muziekladder_artistform_submit($form, &$form_state) {
  var_dump($form);
  exit;
}


function save_artist_node(){
    global $user; 

    $event_date = $data['date'];
    // check for multiple dates
    if (strstr($event_date,',')){
      $date_array = explode(',',$event_date);
    } else {
      $date_array = array($event_date);
    }

    // sort dates
    $timestamp_array = array();
    foreach($date_array as $date_value){
      $timestamp_array[strtotime($date_value)] = $date_value;
    }

    ksort ($timestamp_array);
    $final_date = end($timestamp_array);
    reset ($timestamp_array);

    $data['timestamp_array'] = $timestamp_array;

    if (isset($data['uid'])){
      $userobj = user_load($data['uid']);
      $data['user_name'] = $userobj->name;
    }
    if (isset($data['venue_select']) && strlen($data['venue_select']) && $data['venue_select'] !== '0'){
      $data['db_venue'] = $db->get_venue($data['venue_select']);
      $data['locatie_link']  = $data['db_venue'] ? self::locatie_link ($data['db_venue']) : false;
    }else{
      $data['venue_select'] = false;
    }
    if (isset($data['city_select']) && (int)$data['city_select']){
      $data['db_city'] = $db->get_city($data['city_select']);
    }else{
      $data['city_select'] = false;
    }
    $my_body_content = theme('tip_node',array( 'tip' => $data, 'summary' => 0 ));
    $my_body_content_summary = theme('tip_node',array('tip'=>$data ,'summary' => 1));

    if ($node_id){
     $entity = entity_load_single('node',$node_id);
    } else {
     // entity_create replaces the procedural steps in the first example of
     // creating a new object $node and setting its 'type' and uid property
     $values = array(
        'type' => 'article',
        'uid' => $user->uid,
        'status' => 1,
        'comment' => 2,
        'promote' => 1,
      );

      $entity = entity_create('node', $values);
    }
    // The entity is now created, but we have not yet simplified use of it.
    // Now create an entity_metadata_wrapper around the new node entity
    // to make getting and setting values easier
    $ewrapper = entity_metadata_wrapper('node', $entity);

    // Using the wrapper, we do not have to worry about telling Drupal
    // what language we are using. The Entity API handles that for us.
    $ewrapper->title->set($data['title']);

    // Setting the body is a bit different from other properties or fields
    // because the body can have both its complete value and its
    // summary
# $ewrapper->body->value = 'new value';
    $ewrapper->body->set(array(
    'format' => 'full_html',
    'value' => $my_body_content));

    $ewrapper->field_file_id->set($tip_id);

    $ewrapper->body->summary->set($my_body_content_summary);

    // Setting the value of an entity reference field only requires passing
    // the entity id (e.g., nid) of the entity to which you want to refer
    // The nid 15 here is just an example.
#$ref_nid = 15;
    // Note that the entity id (e.g., nid) must be passed as an integer not a
    // string
#$ewrapper->field_my_entity_ref->set(intval($ref_nid));

    // Entity API cannot set date field values so the 'old' method must
    // be used
    /*     $my_date = new DateTime('January 1, 2013');
    $entity->field_my_date[LANGUAGE_NONE][0] = array(
      'value' => date_format($my_date, 'Y-m-d'),
         'timezone' => 'UTC',
            'timezone_db' => 'UTC',
             );
    */
    // Now just save the wrapper and the entity
    // There is some suggestion that the 'true' argument is necessary to
    // the entity save method to circumvent a bug in Entity API. If there is
    // such a bug, it almost certainly will get fixed, so make sure to check.

    if($uploaded_file){
      $ewrapper->field_image->file->set($uploaded_file);
    }else{
      $ewrapper->field_image->set(NULL);
    }

    $ewrapper->save();
    $node_id = $ewrapper->getIdentifier();

    return $node_id;
 


    
}
  


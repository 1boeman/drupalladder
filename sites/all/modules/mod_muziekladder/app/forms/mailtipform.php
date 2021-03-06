<?php
function mod_muziekladder_mailtipform($form, &$form_state,$presets=array()) {
  global $user;
  global $language;
  //var_dump($presets);
  // only show form if logged in
  if ($user->uid) {
    $form['#prefix'] = '<div class="eventfull muziek-tab tab-1 nodisplay">' ;

    if ($language->language == 'nl'){
      $legend2 =  '<p>Aangemelde evenementen worden op deze pagina geplaatst, en na controle ook aan de Muziekladder agenda toegevoegd.</p>';
    } else {
      $legend2 = '<p>Submitted events will be placed on this page, and after a human check also in the Muziekladder Calendar.</p>';
    }

    $form['#prefix'].= $legend2; 
    $form['#suffix'] = '</div>' ;

    $form['#attributes']['enctype'] = 'multipart/form-data';
    
    $visible_c_f_i = array(
     'visible'=> array(
      ':input[name="soort"]' => array(
          array('value' =>'concert'),
          array('value' =>'festival'),
          array('value' =>'iets_anders'),
      )
    ));

    $form['item-instructies'] = array(
      '#type' => 'item',
      '#attributes' => array('class'=>array('item-instructies')),
      '#markup' =>  '<p>'.t('Fields marked with * are mandatory').'...</p>',
      '#states' =>$visible_c_f_i
    );


    // if we are in edit mode add the file_name
    if (isset($presets['file_name'])){
      $form['name'] = array('#type' => 'hidden', '#value' => $presets['file_name']);
    }

    $locaties_db = Muziek_db::open_locaties_db();
    $cities = Muziek_db::get_cities(1);
    $city_options = array('0'=>t('* City not in this  list?  Select this option! *'));
    $venue_options = array();

    while ($row = $cities->fetchArray()) {
      $city_options[$row['Id']] = $row['Name'];
    }

    $city_options['00'] = '** City not in this list? Select this option! *';

    // check if a city has been selected from the list
    $selected_city = isset($form_state['input']['city_select']) ? $form_state['input']['city_select'] : false;

    // check if a city has been selected in presets
    // but don't overrule a newly selected city
    // - and only if a nocity option ('0' or '00') hasn't been explicly selected
    if ( !(int)$selected_city ){
      if(!isset($form_state['input']['city_select']) ||
          ( isset($form_state['input']) &&
            isset($form_state['input']['city_select']) &&
              $form_state['input']['city_select'] !=='0' &&
                $form_state['input']['city_select'] !=='00') ){
                  if (isset($presets['city_select']) && $presets['city_select'] ){
                    $selected_city = $presets['city_select'];
                  }
                }
    }
    // if we have a selected city - go get the venues
    if ( strlen($selected_city) && (int) $selected_city ) {
      $venues = Muziek_db::get_city_venues($selected_city);
      $venue_options = array('0' =>' ** '.t('Venue not in this list? Select this option!').' * ');

      while ( $row = $venues->fetchArray() ){
        $venue_options[$row['Id']] = html_entity_decode($row['Title']);
      }
    }

    $form['soort'] = array(
       '#type' => 'select',
       '#title' => t('Type of event'),
       '#options' => array(
          'concert' => t('Concert or performance'),
          'festival'=> t('Festival or party'),
          'iets_anders'=>t('Something completely different')
       ),
       '#description' => t('What type of event do you want to recommend to the Muziekladder Calendar ...'),
       '#required'=> true,
    );

    // presets
    if (isset($presets['soort'])){
      $form['soort']['#default_value'] = $presets['soort'];
    }

    /* main fieldsets */
    $form['locatie'] = array(
        '#type' => 'fieldset',
        '#attributes' => array('class'=>array('locatie')),
        '#title' => t('General information'),
        '#states' =>$visible_c_f_i
    );

    $form['venue'] = array(
        '#type' => 'fieldset',
        '#attributes' => array('class'=>array('venue')),
        '#title' => t('Location'),
        '#prefix'=>'<div id="venue_fieldset">',
        '#suffix' => '</div>',
        '#states' =>$visible_c_f_i
    );

    $form['details'] = array(
        '#type' => 'fieldset',
        '#attributes' => array('class'=>array('details')),
        '#title' => t('Details'),
        '#states' =>$visible_c_f_i
    );

    $form['details']['title'] = array(
       '#type' => 'textfield',
       '#title' => t('Title of event / name of artist(s)'),
       '#states' =>array(
          'required' => array (
            ':input[name="soort"]' => array(
                array('value' =>'concert'),
                array('value' =>'festival'),
            )
          )
       ),
       '#attributes' =>array('placeholder' => t('Name')),
    );

    // presets
    if (isset($presets['title'])){
      $form['details']['title']['#default_value'] = $presets['title'];
    }

    $form['details']['link'] = array(
       '#type' => 'textfield',
       '#title' => t('Link to event or venue'),
       '#required' => true,
       '#attributes' =>array('placeholder' => 'http:// ...... '),
       '#description' => t('Without a working link we won\'t be able to process this event.'),
    );

    // presets
    if (isset($presets['link'])){
      $form['details']['link']['#default_value'] = $presets['link'];
    }

    $form['venue']['city_select'] = array(
       '#type' => 'select',
       '#title' => t('City'),
       '#options' => $city_options,
       '#attributes' =>array(),
       '#required' => true,
       '#ajax' => array(
          'callback' => 'ajax_mailtipform_cityselect_callback',
          'wrapper' => 'venue_fieldset',
          'method' => 'replace',
          'effect' => 'fade',
        ),
    );

    // presets
    if (isset($presets['city_select']) ){
      $form['venue']['city_select']['#default_value'] = $presets['city_select'];
    }

    $form['venue']['city'] = array(
       '#type' => 'textfield',
       '#title' => t('Please specify the name of the city, municipality or village hosting the event or performance'),
       '#states' => array(
          'visible'=> array(
            ':input[name="city_select"]' => array(array('value' =>'0'),array('value' =>'00'))
          ),
          'required' => array(
            ':input[name="city_select"]' => array(array('value' =>'0'),array('value' =>'00'))
          )
       ),
       '#attributes' =>array('placeholder' =>
       t('Name or zip of city,village or area')),
        '#required' => false,
    );

    // presets
    if (isset($presets['city']) && strlen($presets['city']) ){
      $form['venue']['city']['#default_value'] = $presets['city'];
    }

    if (count ($venue_options) > 1){
      $form['venue']['venue_select'] = array(
        '#type'=> 'select',
        '#title' => t('Location / venue'),
        '#options' => $venue_options,
        '#required' => true,
        '#ajax' => array(
          'callback' => 'ajax_mailtipform_cityselect_callback',
          'wrapper' => 'venue_fieldset',
          'method' => 'replace',
          'effect' => 'none',
        ),
      );

      $selected_venue = isset($form_state['input']['venue_select'] ) ? $form_state['input']['venue_select'] : false ;
      // presets
      if (!$selected_venue && $selected_venue !== '0'){
        if (isset($presets['venue_select']) && strlen($presets['venue_select']) ){
          $form['venue']['venue_select']['#default_value'] = $presets['venue_select'];
          $selected_venue = $presets['venue_select'];
        }
      }
    }

      // if no city selected or venue unknown selected
    if ( $selected_city === '0' || $selected_city === '00' ||
        (isset($presets) && isset($presets['venue_freetext'])) ||
        (isset($selected_venue) && strlen($selected_venue) && $selected_venue === '0')){
        $form['venue']['venue_freetext'] = array(
          '#type' => 'textarea',
          '#title' => t(
          'Please specify the name and address of the venue, club, area, terrain (or what not) that will host this event.'),
          '#attributes' => array('placeholder' =>
          t('Name / address')),
          '#required' => true );
        // presets
        if ( isset( $presets['venue_freetext'] ) ){
        $form['venue']['venue_freetext']['#default_value'] = $presets['venue_freetext'];
      }
    }

    //$date = format_date(REQUEST_TIME, 'custom', 'd-m-Y');
    //$format = 'd-m-Y';
    $form['locatie']['date'] = array(
       '#title' => t('Selected Date(s)'),
       '#type' => 'textfield', // types 'date_text' and 'datei_timezone' are also supported. See .inc file.
       '#required' => true,
       '#maxlength' => 999,
       '#prefix'  => '<label>'.t('Select the event date(s)')
       .' <span style="color:red">*</span></label>'.
        '<div id="datepicker"></div>',
       '#attributes'=> array(
          'readonly'=>'readonly',
          'class'=>array('full-width'))
    );

    // presets
    if ( isset( $presets['date'] ) ){
      $frontend_dates = array();
      $dates = explode(',',$presets['date']);
      foreach ($dates as $value){
        $frontend_dates[]= implode('-',array_reverse(explode('-',$value)));
      }
      $form['locatie']['date']['#default_value'] = implode(',',$frontend_dates);
    }

    $form['details']['event_image'] = array(
      '#type' => 'managed_file',
      '#name' => 'event_image',
      '#title' => t('Image - poster, flyer or logo (optional) - jpg, gif or png'),
      '#description' => t("Image should be in .jpg, .gif or .png format."),
      '#upload_validators' => array(
        'file_validate_size' => array(5*1024*1024),
        'file_validate_extensions' => array('gif png jpg jpeg'),
      ),
      '#upload_location' => 'public://'
    );
    if ( isset( $presets['event_image'] ) ){
      $form['details']['event_image']['#default_value'] = $presets['event_image'];
    }

    $form['details']['description'] = array(
      '#type' => 'textarea',
      '#title' => t('Remarks and/or extra information (optional)'),
      '#attributes' => array('placeholder' => t('(Support) acts, time, entrance fee, etc.. ')),
    );

    // presets
    if ( isset( $presets['description'] ) ){
      $form['details']['description']['#default_value'] = $presets['description'];
    }

    $form['details']['email'] = array(
     '#type' => 'textfield',
     '#title' => t('Your email-address for any other questions that might arise (optional / will of course not be shared in any way on or via this site)'),
     '#attributes' =>array('placeholder' => t('Email address'))
    );

    // presets
    if ( isset( $presets['email'] ) ){
      $form['details']['email']['#default_value'] = $presets['email'];
    }

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
        '#attributes' => array('class'=>array('btn btn-large btn-inverse')),
        '#states' =>$visible_c_f_i,
    );

  } else {
      $form['item2'] = array(
        '#type' => 'item',
        '#attributes' => array('class'=>array('item-iets-anders')),
        '#markup' => '<a class="aanraadloginlink" href="'.Muziek_util::lang_url().'user?destination=muziekformulier"><i class="icon icon-white icon-plus-sign"></i> '.t('Please log in or register to recommend your events to Muziekladder').'</a>',
        '#prefix'=>'<div class="btn btn-large btn-inverse">',
        '#suffix' => '</div>',

      );
  }

  $form['item'] = array(
        '#type' => 'item',
        '#markup' => ''
    );

  return $form;
}

function ajax_mailtipform_cityselect_callback($form,$form_state) {
  $rv = array();
  return $form['venue'];
}

function mod_muziekladder_mailtipform_validate($form, &$form_state) {
  // Validation logic.
}

function mod_muziekladder_mailtipform_submit($form, &$form_state) {
    // Submission logic.
    if(!user_is_logged_in()) exit;

    global $user;
 
    $update = false;
    $msg = array();
    $dom_doc = new DOMDocument('1.0', 'utf-8');
    $dom_doc->formatOutput = true;
    $root_el = $dom_doc->createElement('input');

    // add the user
    $e = $dom_doc->createElement('uid');
    $data_section = $dom_doc->createCDATASection($user->uid);
    $e->appendChild($data_section);
    $root_el->appendChild($e);

    // add the form data
    foreach($form_state['values'] as $key => $value) {
      if ($key == 'date'){
        $corrected_date_values = array();
        // format received is [dd-mm-yyyy]
        // but we will store [yyyy-mm-dd]
        $arr_values = explode(',',$value);
        foreach ( $arr_values as $dty ) {
          $arr_dty = array_reverse(explode('-',$dty));
          $corrected_date_values[]= implode('-',$arr_dty);
        }
        sort ($corrected_date_values);
        $value = implode(',',$corrected_date_values);
      }
      $e = $dom_doc->createElement($key);
      $data_section = $dom_doc->createCDATASection(trim($value));
      $e->appendChild($data_section);
      $root_el->appendChild($e);
      $msg[]= $key.' : ' .$value;
    }
    $e = $dom_doc->createElement('time',time());
    $root_el->appendChild($e);
    $dom_doc->appendChild($root_el);

    if (!file_exists(MUZIEK_USERDATA_DIR)){
      mkdir ( MUZIEK_USERDATA_DIR,0766 );
      chmod ( MUZIEK_USERDATA_DIR,0755 );
    }

    // check if we are updating or creating
    if (isset($form_state['values']['name'])){
      // updating..
      // check if we are the legitimate owner of the gig
      $gig = Muziek_util::getTip($form_state['values']['name']);
      if ( $gig['uid'] !== $user->uid ){
        Muziek_util::deny();
      }

      $update=true;
      $form_state['redirect'] = 'muziekformulier';
      $file_name = $form_state['values']['name'];
      $nid = isset($gig['node_id']) ? $gig['node_id'] : false;
    } else {
      $file_name = uniqid(date("Y-m-d_H:i:s_")).'_'.$user->uid;
      $nid = false;
    }


    $uploaded_file = false;
    // deal with image if it was uploaded

    if (isset($form_state['values']['event_image']) && $form_state['values']['event_image']) {
      $uploaded_file = file_load($form_state['values']['event_image']);
      $uploaded_file->status = FILE_STATUS_PERMANENT;
      file_save($uploaded_file);
    }

    $filepath = MUZIEK_USERDATA_DIR.'/'.$file_name;

    //save to file ;
    file_put_contents(  $filepath, $dom_doc->saveXML());
    chmod ( $filepath,0765 );

    // save to node ;
    if ($node_id = Muziek_util::saveTipNode($file_name,$nid,$uploaded_file)){
      // - add the node id to xml and resave file for future reference and resave
      $e = $dom_doc->createElement('node_id');
      $data_section = $dom_doc->createCDATASection($node_id);
      $e->appendChild($data_section);
      $root_el->appendChild($e);
      file_put_contents(  $filepath, $dom_doc->saveXML());


    }

    // send me a notification email
    $body = array();
    $from = 'muziekladder@hardcode.nl';
    $body[] = implode("\n",$msg);
    $to = 'info@hardcode.nl';
    $params = array(
        'body' => $body,
        'subject' => 'Muziekladder muziekformulier',
    );
    try {
      if (drupal_mail('mailtipform', 'some_mail_key', $to, language_default(), $params, $from, TRUE)) {
          if (!$update){
            drupal_set_message(t('
              Thanks! Your recommendation has been successfully submitted. <br>We will process it as soon as possible.'));
          }else{
            drupal_set_message(t('
              Thanks! Your updates have been successfully processed.
              They will also be updated in the calendar pages as soon as possible.
            '));
          }
      } else {
          drupal_set_message('Sorry... the submission failed because of technical problems. Please try again later.');
      }
    } catch(Exception $e){

      drupal_set_message(t('Thanks!'));
     
    }
}

function mailtipform_mail($key, &$message, $params) {
    $headers = array(
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8;',
        'Content-Transfer-Encoding' => '8Bit',
        'X-Mailer' => 'Drupal'
    );
    foreach ($headers as $key => $value) {
            $message['headers'][$key] = $value;
    }
    $message['subject'] = $params['subject'];
    $message['body'] = $params['body'];
}



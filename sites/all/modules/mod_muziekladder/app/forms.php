<?php
function mod_muziekladder_mailtipform($form, &$form_state) {
  global $user; 
  
  // only show form if logged in  
  if ($user->uid) {  
    $locaties_db = Muziek_db::open_locaties_db();
    $cities = Muziek_db::get_cities(1);
    $city_options = array(t('* City not in list?  Click here... *'));    
    $venue_options = array(); 

    while ($row = $cities->fetchArray()) {
      $city_options[$row['Id']] = $row['Name'];
    }

    $city_options['00'] = '** City not in list? Click here... *';    
    
    $selected_value = isset($form_state['input']['city_select']) ? $form_state['input']['city_select'] : false;

    if ( strlen($selected_value) && (int) $selected_value ) {
      $venues = Muziek_db::get_city_venues($selected_value); 
      $venue_options = array('0' =>' ** '.t('Venue not in list? Click here...').' * ');
      
      while ( $row = $venues->fetchArray() ){
        $venue_options[$row['Id']] = html_entity_decode($row['Title']);
      }                                             
    }

    $visible_c_f_i = array(
     'visible'=> array(
      ':input[name="soort"]' => array(
          array('value' =>'concert'),
          array('value' =>'festival'),
          array('value' =>'iets_anders'),
      )        
    ));
           
    $form['#prefix'] = '<div class="eventfull">' ;
    $form['#suffix'] = '</div>' ;

    $form['soort'] = array(
       '#type' => 'select',
       '#title' => t('Type of recommendation'),
       '#options' => array(
          'concert' => t('Concert or performance'),
          'festival'=> t('Festival or party'),
          'iets_anders'=>t('Something completely different')
       ),
       '#description' => t('What do you want to recommend to the Muziekladder Calendar ...'),
       '#required'=> true,
      );

      $form['item-instucties'] = array(
        '#type' => 'item',
        '#attributes' => array('class'=>array('item-instructies')),
        '#markup' =>  '<p>'.t('Fields marked with * are mandatory').'...</p>',
        '#states' =>$visible_c_f_i
      ); 

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
        
      $form['details']['link'] = array(
         '#type' => 'textfield',
         '#title' => t('Link to event or venue'),
         '#required' => true, 
         '#attributes' =>array('placeholder' => 'http:// ...... '),
         '#description' => t('Without a working link we won\'t be able to process this event.'),
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
      }

      if ( $selected_value == '0' || $selected_value =='00' ||  
          (isset($selected_venue) && strlen($selected_venue) && $selected_venue == '0')){
          $form['venue']['venue_freetext'] = array(
            '#type' => 'textarea',
            '#title' => t(
            'Please specify the name and address of the venue, club, area, terrain (or what not) that will host this event.'),
            '#attributes' => array('placeholder' => 
            t('Name / address')),
            
            '#required' => true );
      } 


      $date = format_date(REQUEST_TIME, 'custom', 'd-m-Y');
      $format = 'd-m-Y';

      $form['locatie']['date'] = array(
       '#type' => 'date_select', // types 'date_text' and 'datei_timezone' are also supported. See .inc file.
       '#title' => t('Date on which the event will take place'),
       '#default_value' => $date,
       '#date_format' => $format,
       '#required' => true
      );

      $form['details']['description'] = array( 
       '#type' => 'textarea',
       '#title' => t('Remarks and/or extra information'),
       '#attributes' =>array('placeholder' => t('(Support) acts, time, entrance fee, etc.. ')), 
      );
      
      $form['details']['email'] = array(
       '#type' => 'textfield',
       '#title' => t('Your email-address for any other questions that might arise (optional / will of course not be shared in any way on or via this site)'),
       '#attributes' =>array('placeholder' => t('Email address'))
      );

      $form['submit'] = array(
          '#type' => 'submit',
          '#value' => t('Submit'),
          '#states' =>$visible_c_f_i
      );

    } else {
       $form['item2'] = array(
          '#type' => 'item',
          '#attributes' => array('class'=>array('item-iets-anders')),
          '#markup' => '<p><a href="'.Muziek_util::lang_url().'user?destination=muziekformulier">'.t('Please log in or register to recommend your events to Muziekladder').'</a></p>'
          
      ); 
    } 
  
    $form['item'] = array(
          '#type' => 'item',
          '#markup' => '<p>'. t('Your recommendations will be placed on this page, and after a human check also in the Muziekladder Calendar').'</p>'.
          
                '<p>'.t('
                  For general remarks you may also mail (info at muziekladder.nl) or use twitter ').': <a target="_blank" href="https://twitter.com/muziekladder">@Muziekladder</a></p>'
      ); 

  
    return $form;
}

function ajax_mailtipform_cityselect_callback($form,$form_state) {
  $rv = array();
  return $form['venue'];
}

function mod_muziekladder_mailtipform_validate($form, &$form_state) {
  // Validation logic.
  if (!preg_match('/http(s)?:\/\/(.)+/i',$form_state['values']['link'])) {
    form_set_error('link', '
      Please fill out a full working url, including the "http://" of "https://"');
  }
}

function mod_muziekladder_mailtipform_submit($form, &$form_state) {
    // Submission logic.
    global $user; 

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
      mkdir (MUZIEK_USERDATA_DIR,0766);
      chmod(MUZIEK_USERDATA_DIR,0755);
    }
     
    $filepath = MUZIEK_USERDATA_DIR.'/'.uniqid(date("Y-m-d_H:i:s_")); 
    $dom_doc->save($filepath,true );
    chmod ($filepath,0766);

    // send me a notification email

    $body = array();
    $from = 'muziekladder@hardcode.nl';
    $body[] = implode("\n",$msg); 
    $to = 'info@hardcode.nl';
    $params = array(
        'body' => $body,
        'subject' => 'Muziekladder muziekformulier',
    );
    if (drupal_mail('mailtipform', 'some_mail_key', $to, language_default(), $params, $from, TRUE)) {
        drupal_set_message(t('
        Thanks! Your recommendation has been successfully submitted. <br>We will process it as soon as possible.'));
    } else {
        drupal_set_message('Sorry... the submission failed because of technical problems. Please try again later.');
    }
}

function mailtipform_mail($key, &$message, $params) {
    $headers = array(
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8;',
        'Content-Transfer-Encoding' => '8Bit',
        'X-Mailer' => 'Drupal'
    );
    foreach ($headers as $key => $value) {
            $message['headers'][$key] = $value;
    }
    $message['subject'] = $params['subject'];
    $message['body'] = $params['body'];
}

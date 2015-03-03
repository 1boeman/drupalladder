<?php
function mod_muziekladder_mailtipform($form, &$form_state) {

   $locaties_db = Muziek_db::open_locaties_db();
   $cities = Muziek_db::get_cities();
   $city_options = array('* Plaats  niet in deze lijst?  Klik hier... *');    
   $venue_options = array(); 

   while ($row = $cities->fetchArray()) {
      $city_options[$row['Id']] = $row['Name'];
   }
   $city_options['00']= '* Plaats  niet in deze lijst? Klik hier... *';    

  $selected_value = $form_state['input']['city_select'];
  if (strlen($selected_value) && (int) $selected_value){
    $venues = Muziek_db::get_city_venues($selected_value); 
    $venue_options = array('0' =>' * Locatie niet in deze lijst? Klik hier... * ');
    
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
     '#title' => t('Onderwerp tip'),
     '#options' => array(
        'concert' => t('Concert of optreden'),
        'festival'=> t('Festival of feest'),
        'iets_anders'=>t('Iets anders')
     ),
     '#description' => t('Waarover wilt u Muziekladder tippen...'),
     '#required'=> true,
    );

    $form['item-instucties'] = array(
        '#type' => 'item',
        '#attributes' => array('class'=>array('item-instructies')),
        '#markup' =>  '<p>Velden  met een * zijn verplicht...</p>',
        '#states' =>$visible_c_f_i

    ); 

    /* main fieldsets */
    $form['locatie'] = array(
        '#type' => 'fieldset',
        '#attributes' => array('class'=>array('locatie')),
        '#title' => 'Praktische informatie',
        '#states' =>$visible_c_f_i

    ); 
    
    $form['venue'] = array(
        '#type' => 'fieldset',
        '#attributes' => array('class'=>array('venue')),
        '#title' => 'Locatie',
        '#prefix'=>'<div id="venue_fieldset">',
        '#suffix' => '</div>',
        '#states' =>$visible_c_f_i

    ); 
    
    $form['details'] = array(
        '#type' => 'fieldset',
        '#attributes' => array('class'=>array('details')),
        '#title' => 'Details',
        '#states' =>$visible_c_f_i
  
    ); 
      
    $form['details']['link'] = array(
       '#type' => 'textfield',
       '#title' => 'Link naar evenement pagina of podium',
       '#required' => true, 
       '#attributes' =>array('placeholder' => 'http:// ...... '),
       '#description' => t('Zonder werkende link kunnen we dit verzoek helaas niet in behandeling nemen'),
    );
    
    $form['details']['title'] = array(
       '#type' => 'textfield',
       '#title' => 'Titel',
       '#states' =>array(
          'required' => array (
            ':input[name="soort"]' => array(
                array('value' =>'concert'),
                array('value' =>'festival'),
            )        
          )
       ), 

       '#attributes' =>array('placeholder' => 'Naam'),
       '#description' => t('Naam van de artiest(en), het evenement of optreden'),
    );

    $form['venue']['city_select'] = array(
       '#type' => 'select',
       '#title' => 'Plaatsnaam',
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
       '#title' => 'Specificeer a.u.b. de naam van de stad, gemeente of het dorp waarin het evenement plaats heeft.',
       '#states' => array(
          'visible'=> array(
            ':input[name="city_select"]' => array(array('value' =>'0'),array('value' =>'00'))
          ),
          'required' => array(
            ':input[name="city_select"]' => array(array('value' =>'0'),array('value' =>'00'))
          )
       ),
       '#attributes' =>array('placeholder' => 'Naam stad, dorp, gemeente of postcodegebied. '),
       '#required' => false,    
    );

    if (count ($venue_options) > 1){
      $form['venue']['venue_select'] = array(
        '#type'=> 'select',
        '#title' => 'Locatie / podium',
        '#options' => $venue_options,
        '#required' => true,
        '#ajax' => array(
          'callback' => 'ajax_mailtipform_cityselect_callback',
          'wrapper' => 'venue_fieldset',
          'method' => 'replace',
          'effect' => 'none',
        ),
      );
      
      $selected_venue = $form_state['input']['venue_select'];
    }

    if ( $selected_value == '0' || $selected_value =='00' ||  
        (isset($selected_venue) && strlen($selected_venue) && $selected_venue == '0')){
        $form['venue']['venue_freetext'] = array(
          '#type' => 'textarea',
          '#title' => 'Specificeer a.u.b. naam en adres van de locatie, club, cafe of terrein waar het evenement plaats zal vinden.',
          '#attributes' => array('placeholder' => 'Naam / adres podium of locatie'),
          '#required' => true );
    } 


    $date = format_date(REQUEST_TIME, 'custom', 'd-m-Y');
    $format = 'd-m-Y';

    $form['locatie']['date'] = array(
     '#type' => 'date_select', // types 'date_text' and 'datei_timezone' are also supported. See .inc file.
     '#title' => 'Datum concert of evenement',
     '#default_value' => $date,
     '#date_format' => $format,
     '#required' => true
    );

    $form['details']['description'] = array( 
     '#type' => 'textarea',
     '#title' => 'Opmerkingen en/of extra informatie',
     '#attributes' =>array('placeholder' => 'Evt. Support acts, tijden, prijzen etc.. '), 
    );
    
    $form['details']['email'] = array(
     '#type' => 'textfield',
     '#title' => 'Uw e-mail voor eventuele vragen (optioneel / wordt uiteraard niet weergegeven op de site of gedeeld )',
     '#attributes' =>array('placeholder' => t('E-mail address'))
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Verzenden'),
        '#states' =>$visible_c_f_i
    );

    $form['item'] = array(
        '#type' => 'item',
        '#attributes' => array('class'=>array('item-iets-anders')),
        '#markup' => '<p>Tips worden op deze pagina geplaatst, en na controle ook in de Muziekladder agenda toegevoegd.</p>'.
              '<p>Voor algemene opmerkingen kunt u ook terecht op ons Twitter account: <a target="_blank" href="https://twitter.com/muziekladder">@Muziekladder</a></p>'
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
      form_set_error('link', 'Vul aub een volledige url in, inclusief "http://" of "https://"');
    }
                

}

function mod_muziekladder_mailtipform_submit($form, &$form_state) {
    // Submission logic.
    $msg = array();
    $dom_doc = new DOMDocument('1.0', 'utf-8');
    $dom_doc->formatOutput = true;
    $root_el = $dom_doc->createElement('input');

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
        drupal_set_message(t('Dank u wel. Uw tip is succesvol verzonden.<br> Wij zullen er zo spoedig mogelijk aandacht aan besteden.'));
    } else {
        drupal_set_message('Er ging helaas iets mis bij het verzenden. Probeer het aub later nog eens.');
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
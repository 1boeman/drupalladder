<?php 

function mod_muziekladder_locationtipform(){
  $form = array ();
  $form['venue_title'] = array(
    '#type'=> 'textfield',
    '#title'=>t('Venue name'),
    '#attributes' => array('placeholder' =>
      t('Name')),
    '#required' => true,
  );

  $form['venue_url'] = array(
    '#type'=> 'textfield',
    '#size'=> '90',
    '#title'=>t('Venue website or social media url'),
    '#attributes' => array('placeholder' =>  'website'),
    '#required' => true,
  );

  $form['venue_freetext'] = array(
    '#type' => 'textarea',
    '#title' => t('Address'),
    '#attributes' => array('placeholder' =>
      t('Name / address')),
    '#required' => true,
  );

  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#attributes' => array('class'=>array('btn btn-large btn-inverse')),
  );

  return $form;
}

function mod_muziekladder_locationtipform_submit($form, &$form_state) {
    // send me a notification email
    $body = array();
    $from = 'muziekladder@hardcode.nl';
    $msg = array();
    foreach($form_state['values'] as $key=>$value){
      $msg[]= $key.' : ' .$value."\n\r";
    }

    $body[] = implode("\n",$msg);
    $to = 'info@hardcode.nl';
    $params = array(
        'body' => $body,
        'subject' => 'Muziekladder locatie formulier',
    );
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
}

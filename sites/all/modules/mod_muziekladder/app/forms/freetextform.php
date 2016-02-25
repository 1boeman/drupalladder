<?php


function mod_muziekladder_freetextform(){
  $form = array();
  $form['#prefix'] = '<div class="eventfull muziek-tab tab-4 nodisplay">' ;
  $form['#suffix'] = '</div>' ;

  $form['freetext'] = array(
    '#type' => 'textarea',
    '#title' =>t('Free text'),
    '#attributes' => array('placeholder' =>
      t('Please put all relevant information in this textfield')),
    '#required' => true,
  );

  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#attributes' => array('class'=>array('btn btn-large btn-inverse')),
  );

  return $form;
}

function mod_muziekladder_freetextform_submit($form, &$form_state) {
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
      'subject' => 'Muziekladder freetext formulier',
    );
    if (drupal_mail('mailtipform', 'some_mail_key', $to, language_default(), $params, $from, TRUE)) {
          drupal_set_message(t('
            Thanks! Your recommendation has been successfully submitted. <br>We will process it as soon as possible.'));
    } else {
        drupal_set_message('Sorry... the submission failed because of technical problems. Please try again later.');
    }
}



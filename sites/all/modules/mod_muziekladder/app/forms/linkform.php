<?php
function mod_muziekladder_linkform(){
  $form = array();
  $form['#prefix'] = '<div class="eventfull muziek-tab tab-3 nodisplay">' ;
  $form['#suffix'] = '</div>' ;

  $form['link'] = array(
    '#type' => 'textfield',
    '#title' =>t('url'),
    '#attributes' => array('placeholder' =>
      t('http://...')),
    '#required' => true,
  );

  $form['link_title'] = array(
    '#type' => 'textfield',
    '#title' =>t('Title'),
    '#attributes' => array('placeholder' =>
      t('Title text used to display the link.')),
    '#required' => true,
  );


  $form['link_description'] = array(
    '#type' => 'textarea',
    '#title' =>t('Description'),
    '#attributes' => array('placeholder' =>
      t('Please briefly describe the content referenced by this url.')),
    '#required' => true,
  );

  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#attributes' => array('class'=>array('btn btn-large btn-inverse')),
  );

  return $form;
}



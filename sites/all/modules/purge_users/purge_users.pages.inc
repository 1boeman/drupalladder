<?php

/**
 * @file
 * Purge users module config form file.
 */

/**
 * Menu callback function.
 */
function purge_users_config_form($form, &$form_state) {
  $form = array();

  // Add stylesheet.
  drupal_add_css(drupal_get_path('module', 'purge_users') . "/css/purge_users.css");

  // Confirmation form.
  if (isset($form_state['storage']['confirm'])) {
    $uids = purge_users_get_users_list($form_state);
    if (!empty($uids)) {
      $cancel_method = $form_state['values']['purge_users_cancel_method'];
      return purge_users_multiple_cancel_confirm($form, $form_state, $uids, $cancel_method);
    }
    else {
      drupal_set_message(t('No user account found in the system to purge.'));
    }
  }

  // Fetch user roles.
  $user_roles = user_roles(TRUE);
  foreach ($user_roles as $rid => $roles) {
    $roles_array[$rid] = ucfirst($roles);
  }

  $notification_text = t("Dear User, \n\nYour account has been deleted due the website’s policy to automatically remove users who match certain criteria. If you have concerns regarding the deletion, please talk to the administrator of the website. \n\n Thank you");

  $form['never_loggedin_user'] = array(
    '#type' => 'fieldset',
    '#title' => t('Purge users who have never logged in for'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['never_loggedin_user']['purge_users_user_never_lastlogin_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Interval'),
    '#default_value' => variable_get('purge_users_user_never_lastlogin_value', ''),
    '#prefix' => '<div class="purge-interval-selector clearfix">',
    '#attributes' => array('class' => array('purge-value')),
  );

  $form['never_loggedin_user']['purge_users_user_never_lastlogin_period'] = array(
    '#title' => t('Period'),
    '#type' => 'select',
    '#options' => array(
      'days' => t('Days'),
      'month' => t('Months'),
      'year' => t('Year'),
    ),
    '#default_value' => variable_get('purge_users_user_never_lastlogin_period', ''),
    '#attributes' => array('class' => array('purge-period')),
    '#suffix' => '</div>',
  );

  $form['never_loggedin_user']['purge_users_enabled_never_loggedin_users'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enabled'),
    '#default_value' => variable_get('purge_users_enabled_never_loggedin_users', ''),
  );

  $form['not_loggedin_user'] = array(
    '#type' => 'fieldset',
    '#title' => t('Purge users who have not logged in for'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['not_loggedin_user']['purge_users_user_lastlogin_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Interval'),
    '#default_value' => variable_get('purge_users_user_lastlogin_value', ''),
    '#prefix' => '<div class="purge-interval-selector clearfix">',
    '#attributes' => array('class' => array('purge-value')),
  );

  $form['not_loggedin_user']['purge_users_user_lastlogin_period'] = array(
    '#title' => t('Period'),
    '#type' => 'select',
    '#options' => array(
      'days' => t('Days'),
      'month' => t('Months'),
      'year' => t('Year'),
    ),
    '#default_value' => variable_get('purge_users_user_lastlogin_period', ''),
    '#attributes' => array('class' => array('purge-period')),
    '#suffix' => '</div>',
  );

  $form['not_loggedin_user']['purge_users_enabled_loggedin_users'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enabled'),
    '#default_value' => variable_get('purge_users_enabled_loggedin_users', ''),
  );

  $form['not_active_user'] = array(
    '#type' => 'fieldset',
    '#title' => t('Purge users whose account has not been activated for'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['not_active_user']['purge_users_user_inactive_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Interval'),
    '#default_value' => variable_get('purge_users_user_inactive_value', ''),
    '#prefix' => '<div class="purge-interval-selector clearfix">',
    '#attributes' => array('class' => array('purge-value')),
  );

  $form['not_active_user']['purge_users_user_inactive_period'] = array(
    '#title' => t('Period'),
    '#type' => 'select',
    '#options' => array(
      'days' => t('Days'),
      'month' => t('Months'),
      'year' => t('Year'),
    ),
    '#default_value' => variable_get('purge_users_user_inactive_period', ''),
    '#attributes' => array('class' => array('purge-period')),
    '#suffix' => '</div>',
  );

  $form['not_active_user']['purge_users_enabled_inactive_users'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enabled'),
    '#default_value' => variable_get('purge_users_enabled_inactive_users', ''),
  );

  $form['blocked_user'] = array(
    '#type' => 'fieldset',
    '#title' => t('Purge users who have been blocked for'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['blocked_user']['purge_users_user_blocked_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Interval'),
    '#default_value' => variable_get('purge_users_user_blocked_value', ''),
    '#prefix' => '<div class="purge-interval-selector clearfix">',
    '#attributes' => array('class' => array('purge-value')),
  );

  $form['blocked_user']['purge_users_user_blocked_period'] = array(
    '#title' => t('Period'),
    '#type' => 'select',
    '#options' => array(
      'days' => t('Days'),
      'month' => t('Months'),
      'year' => t('Year'),
    ),
    '#default_value' => variable_get('purge_users_user_blocked_period', ''),
    '#attributes' => array('class' => array('purge-period')),
    '#suffix' => '</div>',
  );

  $form['blocked_user']['purge_users_enabled_blocked_users'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enabled'),
    '#default_value' => variable_get('purge_users_enabled_blocked_users', ''),
  );

  $form['purge_users_roles'] = array(
    '#title' => t('Limit purge to the following roles'),
    '#description' => t('Limit users to a particular role.'),
    '#type' => 'checkboxes',
    '#required' => TRUE,
    '#options' => $roles_array,
    '#default_value' => variable_get('purge_users_roles', array()),
  );
  module_load_include('inc', 'user', 'user.pages');
  $form['purge_users_cancel_method'] = array(
    '#type' => 'radios',
    '#title' => t('When cancelling these accounts'),
    '#options' => array(
      'user_cancel_reassign' => t('Delete the account and make its content belong to the %anonymous-name user.', array('%anonymous-name' => variable_get('anonymous', t('Anonymous')))),
      'user_cancel_delete' => t('Delete the account and its content.'),
    ),
    '#default_value' => variable_get('purge_users_cancel_method', 'user_cancel_reassign'),
  );

  $form['purge_users_purge_on_cron'] = array(
    '#type' => 'checkbox',
    '#title' => t('Purge on cron'),
    '#default_value' => variable_get('purge_users_purge_on_cron', ''),
  );

  $form['user_notification'] = array(
    '#type' => 'fieldset',
    '#title' => t('User Deletion Notification'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['user_notification']['purge_users_inactive_user_notify_text'] = array(
    '#type' => 'textarea',
    '#title' => t('Body of user notification e-mail'),
    '#default_value' => variable_get('purge_users_inactive_user_notify_text', $notification_text),
    '#cols' => 70,
    '#rows' => 10,
    '#description' => t('Customize the body of the notification e-mail sent to the user.'),
    '#required' => TRUE,
  );

  // Display the list of available placeholders if token module is installed.
  if (module_exists('token')) {
    $form['user_notification']['token_help'] = array(
      '#theme' => 'token_tree',
      '#token_types' => array('user'),
      '#show_restricted' => TRUE,
      '#dialog' => TRUE,
    );
  }

  $form['user_notification']['purge_users_send_email_notification'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable'),
    '#description' => t('Check to send email notification to purged users.'),
    '#default_value' => variable_get('purge_users_send_email_notification', ''),
  );

  $form['purge_users_now'] = array(
    '#type' => 'submit',
    '#value' => t('Purge users now'),
    '#name' => 'purge_users_now',
    '#attributes' => array('class' => array('purge-now')),
    '#submit' => array('purge_users_now_form_submit'),
  );
  $form['#submit'][] = 'purge_users_now_form_submit';
  return system_settings_form($form);
}

/**
 * Validation function for purge user config form.
 */
function purge_users_config_form_validate($form, &$form_state) {
  if (!isset($form_state['storage']['confirm'])) {
    $never_login_value = $form_state['values']['purge_users_user_never_lastlogin_value'];
    $never_login_period = $form_state['values']['purge_users_user_never_lastlogin_period'];
    $login_value = $form_state['values']['purge_users_user_lastlogin_value'];
    $login_period = $form_state['values']['purge_users_user_lastlogin_period'];
    $inactive_value = $form_state['values']['purge_users_user_inactive_value'];
    $inactive_period = $form_state['values']['purge_users_user_inactive_period'];
    $block_value = $form_state['values']['purge_users_user_blocked_value'];
    $block_period = $form_state['values']['purge_users_user_blocked_period'];
    $enable_blocked = $form_state['values']['purge_users_enabled_blocked_users'];
    $never_enable_loggedin = $form_state['values']['purge_users_enabled_never_loggedin_users'];
    $enable_loggedin = $form_state['values']['purge_users_enabled_loggedin_users'];
    $enable_inactive = $form_state['values']['purge_users_enabled_inactive_users'];

    // Validate text field to only contain numeric values.
    if ($never_login_value != '' && !is_numeric($never_login_value)) {
      form_set_error('purge_users_user_never_lastlogin_value', t('Interval must be a number.'));
    }
    if ($login_value != '' && !is_numeric($login_value)) {
      form_set_error('purge_users_user_lastlogin_value', t('Interval must be a number.'));
    }
    if ($inactive_value != '' && !is_numeric($inactive_value)) {
      form_set_error('purge_users_user_inactive_value', t('Interval must be a number.'));
    }
    if ($block_value != '' && !is_numeric($block_value)) {
      form_set_error('purge_users_user_blocked_value', t('Interval must be a number.'));
    }
    // Validate to set purge period more than 1 days.
    if ($never_login_period == 'days' && !empty($never_login_value) && $never_login_value < 1) {
      form_set_error('purge_users_user_never_lastlogin_value', t('Purge period should not be less than 1 day.'));
    }
    if ($login_period == 'days' && !empty($login_value) && $login_value < 1) {
      form_set_error('purge_users_user_lastlogin_value', t('Purge period should not be less than 1 day.'));
    }
    if ($inactive_period == 'days' && !empty($inactive_value) && $inactive_value < 1) {
      form_set_error('purge_users_user_inactive_value', t('Purge period should not be less than 1 day.'));
    }
    if ($block_period == 'days' && !empty($block_value) && $block_value < 1) {
      form_set_error('purge_users_user_blocked_value', t('Purge period should not be less than 1 day.'));
    }
    // Make sure one of the fieldset is checked.
    if ($never_enable_loggedin == 0 && $enable_loggedin == 0 && $enable_inactive == 0 && $enable_blocked == 0) {
      form_set_error('Enable fieldset', t('Please enable one of the Conditions:  Not logged in users, Inactive users or Blocked users.'));
    }
    // Check if value field is not empty.
    if ($never_enable_loggedin == 1 && empty($never_login_value)) {
      form_set_error('purge_users_user_never_lastlogin_value', t('Interval should not be empty.'));
    }
    if ($enable_loggedin == 1 && empty($login_value)) {
      form_set_error('purge_users_user_lastlogin_value', t('Interval should not be empty.'));
    }
    if ($enable_inactive == 1 && empty($inactive_value)) {
      form_set_error('purge_users_user_inactive_value', t('Interval should not be empty.'));
    }
    if ($enable_blocked == 1 && empty($block_value)) {
      form_set_error('purge_users_user_blocked_value', t('Interval should not be empty.'));
    }
  }
}

/**
 * Submit handler for mass-account cancellation form.
 *
 * @see purge_users_config_form()
 */
function purge_users_now_form_submit($form, &$form_state) {
  // Call purge user function to perform operation.
  if ($form_state['triggering_element']['#name'] == 'purge_users_now') {
    variable_set('purge_users_send_email_notification', $form_state['values']['purge_users_send_email_notification']);
    $form_state['storage']['confirm'] = TRUE;
    $form_state['storage']['original_form'] = $form_state['values'];
    $form_state['rebuild'] = TRUE;
  }

  else {
    // Add items to cron queue.
    if ($form_state['values']['purge_users_purge_on_cron'] == 1) {
      drupal_set_message(t('Purge user operation is scheduled for next cron.'));
    }
  }
}

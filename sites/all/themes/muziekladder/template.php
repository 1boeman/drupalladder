<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */


/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function muziekladder_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  muziekladder_preprocess_html($variables, $hook);
  muziekladder_preprocess_page($variables, $hook);
}
// */

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
/* -- Delete this line if you want to use this function
function muziekladder_preprocess_html(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function muziekladder_preprocess_page(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function muziekladder_preprocess_node(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // muziekladder_preprocess_node_page() or muziekladder_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function muziekladder_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */
/* -- Delete this line if you want to use this function
function muziekladder_preprocess_region(&$variables, $hook) {
  // Don't use Zen's region--sidebar.tpl.php template for sidebars.
  //if (strpos($variables['region'], 'sidebar_') === 0) {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
  //}
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function muziekladder_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}



}
// */

/**
* Implements hook_js_alter()
*/
/*
function muziekladder_js_alter(&$javascript) {
  // Collect the scripts we want in to remain in the header scope.
  $header_scripts = array(
    'settings',
  );
  var_dump ($javascript); exit;
  // Change the default scope of all other scripts to footer.
  // We assume if the script is scoped to header it was done so by default.
  foreach ($javascript as $key => &$script) {
    if ($script['scope'] == 'header' && !in_array($key, $header_scripts)) {
      $script['scope'] = 'footer';
    }
  }
}
*/


function get_lang() {
  global $language;
  return $language->language;
}

/***
 ** prevent wysiwyg in comment form
 **/
function muziekladder_form_comment_form_alter(&$form, &$form_state, &$form_id) {
  $form['comment_body']['#after_build'][] = 'configure_comment_form';
}

function configure_comment_form(&$form) {
  unset($form[LANGUAGE_NONE][0]['format']);
  return $form;
}


function muziekladder_html_head_alter(&$head_elements) {
  unset($head_elements['system_meta_generator']);
  foreach($head_elements as $key => $value){
    if (stristr($key,'shortlink')){
      unset($head_elements[$key])  ;
    }
  }

 // do not index node urls
  if (preg_match('/\/node/',$_SERVER["REQUEST_URI"])){
    $head_elements['noindex'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array('name'=>'robots','content'=>'noindex'));
  }
}

function muziekladder_preprocess_node(&$variables, $hook) {
  // Add $unpublished variable.
  $variables['unpublished'] = (!$variables['status']) ? TRUE : FALSE;

  // Add pubdate to submitted variable.
  $variables['pubdate'] = '<span>'.t('Published on').':</span> <time pubdate datetime="' . format_date($variables['node']->created, 'custom', 'c') . '">' . $variables['date'] . '</time>';
  if ($variables['display_submitted']) {
    $variables['submitted'] = $variables['pubdate'];
  }
}

function muziekladder_preprocess_html(&$variables) {
  global $user;
  $lang_prefix =  Muziek_util::lang_url();
  $variables['lang_prefix'] = $lang_prefix;

}

function muziekladder_preprocess_page(&$variables) {
  global $user;
  $lang_prefix =  Muziek_util::lang_url();
  $variables['lang_prefix'] = $lang_prefix;

  if (drupal_is_front_page()){
    unset($variables['page']['content']['system_main']['pager']);
  }

  if (!empty($variables['node']) && !empty($variables['node']->type)) {
    // provide specific page--node--type based templates
    $variables['theme_hook_suggestions'][] = 'page__node__' . $variables['node']->type;
   
    // provide base_path for edit links 
    $path = base_path();
    $path .= get_lang().'/';
    $variables['path'] = $path;
    // provide edit/delete links to node owner
    if (in_array($variables['node']->type, array('article', 'artist'))){
      if ($user->uid && $user->uid == $variables['node']->uid){
        if (isset($variables['node']->field_file_id['und'][0]['value'])){
          $variables['editable'] = $variables['node']->field_file_id['und'][0]['value'];
        } else {
          $variables['editable'] = 'n_'.$variables['node']->nid;
        }
      }
    }
  }

  if (stristr(current_path(),'muziekformulier') && $user->uid){
    $themepath = drupal_get_path('theme','muziekladder');
    drupal_add_css($themepath.'/bootstrapdatepicker/css/bootstrap-datepicker.min.css');
    drupal_add_js($themepath.'/bootstrapdatepicker/js/bootstrap-datepicker.min.js');
  }

  // draw crumb trail for view pages
  $view = views_get_page_view();
  if(isset($view) && $view->name == 'evenement_archief') {
    $crumb_items = array(
      array('text'=>t('Tips'), 'link'=>$lang_prefix.'muziekformulier'));
      if (count($view->args)){
        // date filtered page
        $crumb_items[]= array(
          'text'=>t('Archive'),
          'link'=>$lang_prefix.'archief'
        );
        $crumb_items[]= array(
          'text'=>$view->build_info['substitutions']['%1']
        );
      } else {
        // month overview page
        $crumb_items[]= array(
          'text'=>t('Archive')
        );
      }
      $variables['crumbs'] = theme('crumb_trail',array('items'=>$crumb_items));
  }

  //check / adjust for user profile page
  if (in_array('page__user__%',$variables['theme_hook_suggestions'])){
    if (user_is_logged_in()){
      $tips = Muziek_util::showTips();
      drupal_add_js(array('rows'=>$tips), 'setting');
    }
  }
}


function muziekladder_preprocess_views_view(&$vars,$d) {
  if ($vars['view']->name == 'evenement_archief'){
    $view = $vars['view'];
    if (isset($view->build_info['substitutions']['%1']) && strstr($view->build_info['substitutions']['%1'],'20')){
      $view->build_info['substitutions']['%1'] = t('Tips posted in ').$view->build_info['substitutions']['%1'];
    }
  }
}

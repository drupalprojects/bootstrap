<?php

include_once(dirname(__FILE__) . '/includes/twitter_bootstrap.inc');

function twitter_bootstrap_form_system_theme_settings_alter(&$form, &$form_state) {
  $form['twitter_bootstrap_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Twitter bootstrap Settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#weight' => -1
  );
  $form['twitter_bootstrap_settings']['twitter_bootstrap_css_files'] = array(
    '#type' => 'textarea',
    '#title' => t('CSS includes'),
    '#default_value' => twitter_bootstrap_theme_get_setting('twitter_bootstrap_css_files'),
    '#description' => t('Enter the path and filename, relative to Drupal root, where the CSS file is located, seperated by an new line.'),
  );
  $form['twitter_bootstrap_settings']['twitter_bootstrap_js_files'] = array(
    '#type' => 'textarea',
    '#title' => t('JS includes'),
    '#default_value' => twitter_bootstrap_theme_get_setting('twitter_bootstrap_js_files'),
    '#description' => t('Enter the path and filename, relative to Drupal root, where the JavaScript file is located, seperated by an new line.'),
  );
}


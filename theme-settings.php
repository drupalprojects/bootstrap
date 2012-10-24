<?php

include_once(dirname(__FILE__) . '/includes/twitter_bootstrap.inc');

function twitter_bootstrap_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['theme_settings']['toggle_search'] = array(
    '#type' => 'checkbox', 
    '#title' => t('Search box'), 
    '#default_value' => theme_get_setting('toggle_search'), 
  );
 
  $form['themedev'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Theme development settings'),
  );
  $form['themedev']['twitter_bootstrap_rebuild_registry'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Rebuild theme registry on every page.'),
    '#default_value' => theme_get_setting('twitter_bootstrap_rebuild_registry'),
    '#description'   => t('During theme development, it can be very useful to continuously <a href="!link">rebuild the theme registry</a>. WARNING: this is a huge performance penalty and must be turned off on production websites.', array('!link' => 'http://drupal.org/node/173880#theme-registry')),
  );
}


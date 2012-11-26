<?php

include_once(dirname(__FILE__) . '/includes/bootstrap.inc');

function bootstrap_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {
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

  $form['themedev']['bootstrap_rebuild_registry'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Rebuild theme registry on every page.'),
    '#default_value' => theme_get_setting('bootstrap_rebuild_registry'),
    '#description'   => t('During theme development, it can be very useful to continuously <a href="!link">rebuild the theme registry</a>. WARNING: this is a huge performance penalty and must be turned off on production websites.', array('!link' => 'http://drupal.org/node/173880#theme-registry')),
  );

  $form['themedev']['cdn_bootstrap'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use cdn to load in the bootstrap files'),
    '#default_value' => theme_get_setting('cdn_bootstrap'),
    '#description'   => t('If you dont want to add add the bootstrap files yourself you can always use cdn, but be warned this is a third party hosting')
  );

  $form['themedev']['cdn_jquery'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use cdn to load in the bootstrap files'),
    '#default_value' => theme_get_setting('cdn_jquery'),
    '#description'   => t('If you dont want to add add the jqyery files yourself you can always use cdn, but be warned this is a third party hosting and uses the noconflict solution. This means that 2 versions of jquery are loaded, what is a suboptimal solution')
  );
}


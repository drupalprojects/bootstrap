<?php
/**
 * @file
 * theme-settings.php
 *
 * Contains the form settings for this theme.
 */

include_once dirname(__FILE__) . '/includes/bootstrap.inc';

/**
 * Implements THEME_form_system_theme_settings_alter().
 */
function bootstrap_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes.
  // @see https://drupal.org/node/943212
  if (isset($form_id)) {
    return;
  }

  $form['bootstrap'] = array(
    '#type' => 'fieldset',
    '#title' => t('Bootstrap 3'),
  );

  $form['bootstrap']['bootstrap_cdn'] = array(
    '#type' => 'select',
    '#title' => t('BootstrapCDN'),
    '#options' => drupal_map_assoc(array(
      '3.0.0',
    )),
    '#default_value' => theme_get_setting('bootstrap_cdn'),
    '#empty_option' => t('Disabled'),
    '#empty_value' => NULL,
    '#description' => t('Use !bootstrapcdn to serve the Bootstrap framework files. Enabling this setting will prevent this theme from attempting to load any Bootstrap framework files locally. !warning', array(
      '!bootstrapcdn' => l(t('BootstrapCDN'), 'http://bootstrapcdn.com', array(
        'external' => TRUE,
      )),
      '!warning' => '<div class="alert alert-danger"><strong>' . t('WARNING') . ':</strong> ' . t('Using this content distribution network will give you a performance boost but will also make you dependant on a third party who has no obligations towards you concerning uptime and service quality.') . '</div>',
    )),
  );

  $bootswatch_request = drupal_http_request('http://api.bootswatch.com/3/');
  $bootswatch_api = drupal_json_decode($bootswatch_request->data);
  $bootswatch_themes = array();
  foreach ($bootswatch_api['themes'] AS $val) {
    $bootswatch_themes[strtolower($val['name'])] = $val['name'];
  }

  $form['bootstrap']['bootstrap_bootswatch'] = array(
    '#type' => 'select',
    '#title' => t('Bootswatch Theme'),
    '#options' => $bootswatch_themes,
    '#empty_option' => t('Bootstrap'),
    '#empty_value' => NULL,
    '#default_value' => theme_get_setting('bootstrap_bootswatch'),
    '#description' => t('Use !bootstrapcdn to serve a Bootswatch Theme. Choose Bootswatch Theme here.', array('!bootstrapcdn' => l('BootstrapCDN', 'http://bootstrapcdn.com', array('external' => TRUE)))) . '<div id="bootswatch-previews"></div>',
  );
  $form['#attached']['js'] = array(drupal_get_path('theme', 'bootstrap') . '/js/bootswatch.admin.js');

  $form['bootstrap']['bootstrap_rebuild_registry'] = array(
    '#type' => 'checkbox',
    '#title' => t('Rebuild theme registry on every page.'),
    '#default_value' => theme_get_setting('bootstrap_rebuild_registry'),
    '#description' => t('During theme development, it can be very useful to continuously !rebuild. !warning', array(
      '!rebuild' => l(t('rebuild the theme registry'), 'http://drupal.org/node/173880#theme-registry'),
      '!warning' => '<div class="alert alert-danger"><strong>' . t('WARNING') . ':</strong> ' . t('This is a huge performance penalty and must be turned off on production websites. ') . '</div>',
    )),
  );

}

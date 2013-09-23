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

  $theme_path = drupal_get_path('theme', 'bootstrap');

  $form['bootstrap'] = array(
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'js'  => array($theme_path . '/js/bootstrap.admin.js'),
    ),
  );

  $form['bootstrap_cdn'] = array(
    '#type' => 'fieldset',
    '#title' => t('BootstrapCDN'),
    '#group' => 'bootstrap',
    '#description' => t('Use !bootstrapcdn to serve the Bootstrap framework files. Enabling this setting will prevent this theme from attempting to load any Bootstrap framework files locally. !warning', array(
      '!bootstrapcdn' => l(t('BootstrapCDN'), 'http://bootstrapcdn.com', array(
        'external' => TRUE,
      )),
      '!warning' => '<div class="alert alert-danger"><strong>' . t('WARNING') . ':</strong> ' . t('Using this content distribution network will give you a performance boost but will also make you dependant on a third party who has no obligations towards you concerning uptime and service quality.') . '</div>',
    )),
  );
  $form['bootstrap_cdn']['bootstrap_cdn'] = array(
    '#type' => 'select',
    '#options' => drupal_map_assoc(array(
      '3.0.0',
    )),
    '#default_value' => theme_get_setting('bootstrap_cdn'),
    '#empty_option' => t('Disabled'),
    '#empty_value' => NULL,
  );

  $bootswatch_themes = array();
  $request = drupal_http_request('http://api.bootswatch.com/3/');
  $api = drupal_json_decode($request->data);
  foreach ($api['themes'] as $bootswatch_theme) {
    $bootswatch_themes[strtolower($bootswatch_theme['name'])] = $bootswatch_theme['name'];
  }

  $form['bootswatch'] = array(
    '#type' => 'fieldset',
    '#title' => t('Bootswatch'),
    '#group' => 'bootstrap',
    '#description' => t('Use !bootstrapcdn to serve a Bootswatch Theme. Choose Bootswatch theme here.', array(
      '!bootstrapcdn' => l(t('BootstrapCDN'), 'http://bootstrapcdn.com', array(
        'external' => TRUE,
      )),
    )),
  );
  $form['bootswatch']['bootstrap_bootswatch'] = array(
    '#type' => 'select',
    '#default_value' => theme_get_setting('bootstrap_bootswatch'),
    '#options' => $bootswatch_themes,
    '#empty_option' => t('Disabled'),
    '#empty_value' => NULL,
    '#suffix' => '<div id="bootswatch-preview"></div>',
  );

  $form['advanced'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced'),
    '#group' => 'bootstrap',
  );
  $form['advanced']['bootstrap_rebuild_registry'] = array(
    '#type' => 'checkbox',
    '#title' => t('Rebuild theme registry on every page.'),
    '#default_value' => theme_get_setting('bootstrap_rebuild_registry'),
    '#description' => t('During theme development, it can be very useful to continuously !rebuild. !warning', array(
      '!rebuild' => l(t('rebuild the theme registry'), 'http://drupal.org/node/173880#theme-registry'),
      '!warning' => '<div class="alert alert-danger"><strong>' . t('WARNING') . ':</strong> ' . t('This is a huge performance penalty and must be turned off on production websites. ') . '</div>',
    )),
  );

  $form['bootstrap']['bootstrap_breadcrumb'] = array(
    '#type' => 'select',
    '#title' => t('Display breadcrumb'),
    '#default_value' => theme_get_setting('bootstrap_breadcrumb'),
    '#options' => array(
      0 => t('No'),
      1 => t('Yes'),
      2 => t('Only in admin section'),
    ),
  );
  $form['bootstrap']['bootstrap_breadcrumb_home'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show home page link in breadcrumb'),
    '#default_value' => theme_get_setting('bootstrap_breadcrumb_home'),
    '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is <strong>enabled</strong>.'),
    '#states' => array(
      'invisible' => array(
        ':input[name="bootstrap_breadcrumb"]' => array('value' => 0),
      ),
    ),
  );
  $form['bootstrap']['bootstrap_breadcrumb_title'] = array(
    '#type' => 'checkbox',
    '#title' => t('Append the content title to the end of the breadcrumb'),
    '#default_value' => theme_get_setting('bootstrap_breadcrumb_title'),
    '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is <strong>disabled</strong>.'),
    '#states' => array(
      'invisible' => array(
        ':input[name="bootstrap_breadcrumb"]' => array('value' => 0),
      ),
    ),
  );
}

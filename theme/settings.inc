<?php
/**
 * @file
 * settings.inc
 */

/**
 * Contains the form for the theme settings.
 *
 * @param array $form
 *   The form array, passed by reference.
 * @param array $form_state
 *   The form state array, passed by reference.
 */
function _bootstrap_settings_form(&$form, $form_state) {
  // Wrap global setting fieldsets in vertical tabs.
  $form['general'] = array(
    '#type' => 'vertical_tabs',
    '#prefix' => '<h2><small>' . t('Override Global Settings') . '</small></h2>',
    '#weight' => -9,
  );
  $form['theme_settings']['#group'] = 'general';
  $form['logo']['#group'] = 'general';
  $form['favicon']['#group'] = 'general';

  // Do not add Bootstrap specific settings to global settings.
  if (empty($form_state['build_info']['args'][0])) {
    unset($form['general']['#prefix']);
    return;
  }

  // Do not add Bootstrap specific settings to non-bootstrap based themes.
  $theme = $form_state['build_info']['args'][0];
  if ($theme !== 'bootstrap') {
    $base_theme = bootstrap_get_theme_info($theme, 'base theme');
    if (!$base_theme || (!is_array($base_theme) && $base_theme !== 'bootstrap') || (is_array($base_theme) && !in_array('bootstrap', $base_theme))) {
      return;
    }
  }

  $form['bootstrap'] = array(
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'js'  => array(drupal_get_path('theme', 'bootstrap') . '/js/bootstrap.admin.js'),
    ),
    '#prefix' => '<h2><small>' . t('Bootstrap Settings') . '</small></h2>',
    '#weight' => -10,
  );

  // BootstrapCDN.
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

  // Bootswatch.
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

  // Breadcrumbs.
  $form['breadcrumbs'] = array(
    '#type' => 'fieldset',
    '#title' => t('Breadcrumbs'),
    '#group' => 'bootstrap',
  );
  $form['breadcrumbs']['bootstrap_breadcrumb'] = array(
    '#type' => 'select',
    '#title' => t('Breadcrumb visibility'),
    '#default_value' => theme_get_setting('bootstrap_breadcrumb'),
    '#options' => array(
      0 => t('Hidden'),
      1 => t('Visible'),
      2 => t('Only in admin areas'),
    ),
  );
  $form['breadcrumbs']['bootstrap_breadcrumb_home'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show "Home" breadcrumb link'),
    '#default_value' => theme_get_setting('bootstrap_breadcrumb_home'),
    '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is <strong>enabled</strong>.'),
    '#states' => array(
      'invisible' => array(
        ':input[name="bootstrap_breadcrumb"]' => array('value' => 0),
      ),
    ),
  );
  $form['breadcrumbs']['bootstrap_breadcrumb_title'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show current page title at end'),
    '#default_value' => theme_get_setting('bootstrap_breadcrumb_title'),
    '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is <strong>disabled</strong>.'),
    '#states' => array(
      'invisible' => array(
        ':input[name="bootstrap_breadcrumb"]' => array('value' => 0),
      ),
    ),
  );

  // Advanced settings.
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

}

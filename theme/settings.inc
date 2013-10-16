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
  // Do not add Bootstrap specific settings to non-bootstrap based themes.
  $theme = !empty($form_state['build_info']['args'][0]) ? $form_state['build_info']['args'][0] : FALSE;
  // Global settings.
  if ($theme === FALSE) {
    return;
  }
  // Other theme settings.
  elseif ($theme !== 'bootstrap') {
    $base_theme = bootstrap_get_theme_info($theme, 'base theme');
    if (!$base_theme || (!is_array($base_theme) && $base_theme !== 'bootstrap') || (is_array($base_theme) && !in_array('bootstrap', $base_theme))) {
      return;
    }
  }

  // Display a warning if jquery_update isn't enabled.
  if (!module_exists('jquery_update') || !version_compare(variable_get('jquery_update_jquery_version', 0), 1.7, '>=')) {
    drupal_set_message(t('jQuery Update is not enabled, Bootstrap requires a minimum jQuery version of 1.7 or higher. <br>Please enable <a href="https://drupal.org/project/jquery_update">jQuery Update module</a> 7.x-2.3 or higher, you must manually set this in the configuration after it is installed.'), 'error');
  }

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
      '!warning' => '<div class="alert alert-warning messages warning"><strong>' . t('WARNING') . ':</strong> ' . t('Using this content distribution network will give you a performance boost but will also make you dependant on a third party who has no obligations towards you concerning uptime and service quality.') . '</div>',
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
  if ($request && $request->code === '200' && !empty($request->data)) {
    if (($api = drupal_json_decode($request->data)) && is_array($api) && !empty($api['themes'])) {
      foreach ($api['themes'] as $bootswatch_theme) {
        $bootswatch_themes[strtolower($bootswatch_theme['name'])] = $bootswatch_theme['name'];
      }
    }
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

  if (empty($bootswatch_themes)) {
    $form['bootswatch']['bootstrap_bootswatch']['#prefix'] = '<div class="alert alert-danger messages error"><strong>' . t('ERROR') . ':</strong> ' . t('Unable to reach Bootswatch API. Please ensure the server your website is hosted on is able to initiate HTTP requests.') . '</div>';
  }

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

  // Navbar.
  $form['navbar'] = array(
    '#type' => 'fieldset',
    '#title' => t('Navbar'),
    '#group' => 'bootstrap',
  );
  $form['navbar']['bootstrap_navbar_position'] = array(
    '#type' => 'select',
    '#title' => t('Navbar Position'),
    '#description' => t('Select your Navbar position.'),
    '#default_value' => theme_get_setting('bootstrap_navbar_position'),
    '#options' => array(
      'static-top' => t('Static Top'),
      'fixed-top' => t('Fixed Top'),
      'fixed-bottom' => t('Fixed Bottom'),
    ),
    '#empty_option' => t('Normal'),
  );
  $form['navbar']['bootstrap_navbar_inverse'] = array(
    '#type' => 'checkbox',
    '#title' => t('Inverse navbar style'),
    '#description' => t('Select if you want the inverse navbar style.'),
    '#default_value' => theme_get_setting('bootstrap_navbar_inverse'),
  );

  // Region wells.
  $wells = array(
    '' => t('None'),
    'well' => t('.well (normal)'),
    'well-sm' => t('.well-sm (small)'),
    'well-lg' => t('.well-lg (large)'),
  );
  $form['region_wells'] = array(
    '#type' => 'fieldset',
    '#title' => t('Region wells'),
    '#description' => t('Enable the <code>.well</code>, <code>.well-sm</code> or <code>.well-lg</code> classes for specified regions. See: documentation on !wells.', array(
      '!wells' => l(t('Bootstrap Wells'), 'http://getbootstrap.com/components/#wells'),
    )),
    '#group' => 'bootstrap',
  );
  foreach (system_region_list($theme) as $name => $title) {
    // Skip core's "dashboard" regions.
    if (preg_match('/^dashboard/', $name)) {
      continue;
    }
    $form['region_wells']['bootstrap_region_well-' . $name] = array(
      '#title' => $title,
      '#type' => 'select',
      '#attributes' => array(
        'class' => array('input-sm'),
      ),
      '#options' => $wells,
      '#default_value' => theme_get_setting('bootstrap_region_well-' . $name),
    );
  }

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
      '!warning' => '<div class="alert alert-warning messages warning"><strong>' . t('WARNING') . ':</strong> ' . t('This is a huge performance penalty and must be turned off on production websites. ') . '</div>',
    )),
  );

}

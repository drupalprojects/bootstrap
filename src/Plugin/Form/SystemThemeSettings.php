<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SystemThemeSettings.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\ProviderManager;
use Drupal\bootstrap\Plugin\SettingManager;
use Drupal\bootstrap\Plugin\Setting\SettingInterface;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Theme;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 *
 * @BootstrapForm(
 *   id = "system_theme_settings",
 * )
 */
class SystemThemeSettings extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function alter(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $theme = $this->getTheme($form, $form_state, $form_id);
    if (!$theme) {
      return;
    }

    // Creates the necessary groups (vertical tabs) for a Bootstrap based theme.
    $this->createGroups($form, $form_state, $form_id);

    // Iterate over all setting plugins and add them to the form.
    $setting_manager = new SettingManager($theme);
    foreach (array_keys($setting_manager->getDefinitions()) as $plugin_id) {
      /** @var SettingInterface $setting */
      $setting = $setting_manager->createInstance($plugin_id, ['theme' => $theme]);

      // Construct the setting element.
      $setting->getSettingElement($form, $form_state);

      // Allow settings to alter the form further if they need to.
      $setting->alter($form, $form_state);
    }

    // Add in CDN providers.
    $this->cdnProviders($form, $form_state, $theme);
  }

  /**
   * Adds in CDN provider settings.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param \Drupal\bootstrap\Theme $theme
   *   The current theme object.
   */
  protected function cdnProviders(array &$form, FormStateInterface &$form_state, Theme $theme) {
    // Retrieve the provider from form values or the setting.
    $default_provider = $form_state->getValue('cdn_provider', $theme->getSetting('cdn_provider'));

    $provider_manager = new ProviderManager($theme);

    // Intercept possible manual import of API data via AJAX callback.
    if ($form_state->getValue('clicked_button') === t('Save provider data')->render()) {
      $provider_path = ProviderManager::FILE_PATH;
      file_prepare_directory($provider_path, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);
      $provider = isset($form_state['values']['bootstrap_cdn_provider_import_data']) ? $form_state['values']['bootstrap_cdn_provider_import_data'] : FALSE;
      $file = "$provider_path/$default_provider.json";
      if ($provider) {
        file_unmanaged_save_data($provider, $file, FILE_EXISTS_REPLACE);
      }
      elseif ($file && file_exists($file)) {
        file_unmanaged_delete($file);
      }
      $provider_manager->clearCachedDefinitions();
    }

    $form['advanced']['cdn'] = [
      '#type' => 'fieldset',
      '#title' => t('CDN (Content Delivery Network)'),
      '#description' => '<div class="alert alert-info messages warning"><strong>' . t('NOTE') . ':</strong> ' . t('Using one of the "CDN Provider" options below is the preferred method for loading Bootstrap CSS and JS on simpler sites that do not use a site-wide CDN. Using a "CDN Provider" for loading Bootstrap, however, does mean that it depends on a third-party service. There is no obligation or commitment by these third-parties that guarantees any up-time or service quality. If you need to customize Bootstrap and have chosen to compile the source code locally (served from this site), you must disable the "CDN Provider" option below by choosing "- None -" and alternatively enable a site-wide CDN implementation. All local (served from this site) versions of Bootstrap will be superseded by any enabled "CDN Provider" below. <strong>Do not do both</strong>.') . '</div>',
      '#collapsible' => TRUE,
      '#collapsed' => !$default_provider,
    ];

    $providers = $theme->getProviders();

    $options = [];
    foreach ($providers as $plugin_id => $provider) {
      $options[$plugin_id] = $provider->getLabel();
    }
    $form['advanced']['cdn']['cdn_provider'] = [
      '#type' => 'select',
      '#title' => t('CDN Provider'),
      '#default_value' => $default_provider,
      '#empty_value' => '',
      '#options' => $options,
    ];

    // Render each provider.
    foreach ($providers as $plugin_id => $provider) {
      $form['advanced']['cdn']['provider'][$plugin_id] = [
        '#type' => 'container',
        '#prefix' => '<div id="bootstrap-cdn-provider-' . $plugin_id . '">',
        '#suffix' => '</div>',
        '#states' => [
          'visible' => [
            ':input[name="cdn_provider"]' => ['value' => $plugin_id],
          ],
        ],
      ];

      $settings = new Element($form['advanced']['cdn']['provider'][$plugin_id]);

      if ($description = $provider->getDescription()) {
        $settings->description = ['#markup' => '<div class="lead">' . $description . '</div>'];
      }

      // Indicate there was an error retrieving the provider's API data.
      if ($provider->hasError() || $provider->isImported()) {
        if ($provider->hasError()) {
          $prefix = $settings->getProperty('prefix') ?: '';
          $prefix .= '<div class="alert alert-danger messages error"><strong>' . t('ERROR') . ':</strong> ' . t('Unable to reach or parse the data provided by the @title API. Ensure the server this website is hosted on is able to initiate HTTP requests via <a href=":drupal_http_request" target="_blank">drupal_http_request()</a>. If the request consistently fails, it is likely that there are certain PHP functions that have been disabled by the hosting provider for security reasons. It is possible to manually copy and paste the contents of the following URL into the "Imported @title data" section below.<br /><br /><a href=":provider_api" target="_blank">:provider_api</a>.', [
              '@title' => $provider['title'],
              ':provider_api' => $provider['api'],
              ':drupal_http_request' => 'https://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_http_request/7',
            ]) . '</div>';
          $settings->setProperty('prefix', $prefix);
        }
        $settings->import = [
          '#type' => 'fieldset',
          '#title' => t('Imported @title data', [
            '@title' => $provider->getLabel(),
          ]),
          '#description' => t('The provider will attempt to parse the data entered here each time it is saved. If no data has been entered, any saved files associated with this provider will be removed and the provider will again attempt to request the API data normally through the following URL: <a href=":provider_api" target="_blank">:provider_api</a>.', [
            ':provider_api' => $provider->getPluginDefinition()['api'],
          ]),
          '#weight' => 10,
          '#collapsible' => TRUE,
          '#collapsed' => TRUE,
        ];
        $settings->import->cdn_provider_import_data = [
          '#type' => 'textarea',
          '#default_value' => file_exists(ProviderManager::FILE_PATH . '/' . $plugin_id . '.json') ? file_get_contents(ProviderManager::FILE_PATH . '/' . $plugin_id . '.json') : NULL,
        ];
        $settings->import->submit = [
          '#type' => 'submit',
          '#value' => t('Save provider data'),
          '#executes_submit_callback' => FALSE,
          '#ajax' => [
            'callback' => [$this, 'cdnProviderAjax'],
            'wrapper' => 'bootstrap-cdn-provider-' . $plugin_id,
          ],
        ];
      }

      // Let the provider create it's settings form.
      $provider->settingsForm($settings, $form_state);
    }
  }

  /**
   * AJAX callback for reloading CDN provider elements.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function cdnProviderAjax(array $form, FormStateInterface $form_state) {
    return $form['advanced']['cdn']['provider'][$form_state->getValue('cdn_provider')];
  }

  /**
   * Sets up the vertical tab groupings.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   String representing the name of the form itself. Typically this is the
   *   name of the function that generated the form.
   */
  protected function createGroups(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $f = new Element($form);

    // Vertical tabs for global settings provided by core or contrib modules.
    if (!isset($form['global'])) {
      $form['global'] = [
        '#type' => 'vertical_tabs',
        '#weight' => -9,
        '#prefix' => '<h2><small>' . t('Override Global Settings') . '</small></h2>',
      ];
    }

    // Iterate over existing children and move appropriate ones to global group.
    foreach ($f->children() as $child) {
      if ($child->isType(['details', 'fieldset']) && !$child->hasProperty('group')) {
        $child->setProperty('group', 'global');
      }
    }

    // Provide the necessary default groups.
    $form['bootstrap'] = [
      '#type' => 'vertical_tabs',
      '#attached' => ['library' => ['bootstrap/adminscript']],
      '#prefix' => '<h2><small>' . t('Bootstrap Settings') . '</small></h2>',
      '#weight' => -10,
    ];
    $groups = [
      'general' => t('General'),
      'components' => t('Components'),
      'javascript' => t('JavaScript'),
      'advanced' => t('Advanced'),
    ];
    foreach ($groups as $group => $title) {
      $form[$group] = [
        '#type' => 'details',
        '#title' => $title,
        '#group' => 'bootstrap',
      ];
    }
  }

  /**
   * Retrieves the currently selected theme on the settings form.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   String representing the name of the form itself. Typically this is the
   *   name of the function that generated the form.
   *
   * @return \Drupal\bootstrap\Theme|FALSE
   *   The currently selected theme object or FALSE if not a Bootstrap theme.
   */
  protected function getTheme(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    // Work-around for a core bug affecting admin themes.
    // @see https://drupal.org/node/943212
    $build_info = $form_state->getBuildInfo();
    $theme = isset($build_info['args'][0]) ? $build_info['args'][0] : NULL;
    if (isset($form_id) || !$theme) {
      return FALSE;
    }

    // Do not continue if the theme is not Bootstrap specific.
    $theme = Bootstrap::getTheme($theme);
    if (!$theme->subthemeOf('bootstrap')) {
      return FALSE;
    }

    return $theme;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state, $form_id = NULL) {
    $theme = $this->getTheme($form, $form_state, $form_id);
    if (!$theme) {
      return;
    }

    // Iterate over all setting plugins and allow them to participate.
    $setting_manager = new SettingManager($theme);
    foreach (array_keys($setting_manager->getDefinitions()) as $plugin_id) {
      /** @var SettingInterface $setting */
      $setting = $setting_manager->createInstance($plugin_id, ['theme' => $theme]);
      $setting->submit($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $theme = $this->getTheme($form, $form_state, $form_id);
    if (!$theme) {
      return;
    }

    // Iterate over all setting plugins and allow them to participate.
    $setting_manager = new SettingManager($theme);
    foreach (array_keys($setting_manager->getDefinitions()) as $plugin_id) {
      /** @var SettingInterface $setting */
      $setting = $setting_manager->createInstance($plugin_id, ['theme' => $theme]);
      $setting->validate($form, $form_state);
    }
  }

}

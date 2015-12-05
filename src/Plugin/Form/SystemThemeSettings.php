<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SystemThemeSettings.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\SettingManager;
use Drupal\bootstrap\Plugin\Setting\SettingInterface;
use Drupal\bootstrap\Utility\Element;
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
    $settings = $setting_manager->getDefinitions();
    foreach (array_keys($settings) as $plugin_id) {
      /** @var SettingInterface $setting */
      $setting = $setting_manager->createInstance($plugin_id, ['theme' => $theme]);

      // Construct the setting element.
      $setting->getSettingElement($form, $form_state);

      // Allow settings to alter the form further if they need to.
      $setting->alter($form, $form_state);
    }
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

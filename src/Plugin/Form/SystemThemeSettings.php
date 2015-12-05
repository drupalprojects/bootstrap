<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SystemThemeSettings.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 *
 * @BootstrapForm(
 *   id = "system_theme_settings",
 * )
 */
class SystemThemeSettings extends FormBase implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $theme = $this->getTheme($form, $form_state);
    if (!$theme) {
      return;
    }

    // Creates the necessary groups (vertical tabs) for a Bootstrap based theme.
    $this->createGroups($form, $form_state);

    // Iterate over all setting plugins and add them to the form.
    foreach ($theme->getSettingInstances() as $setting) {
      // Construct the setting element.
      $form = $setting->buildForm($form, $form_state);

      // Allow settings to alter the form further if they need to.
      $setting->alterForm($form, $form_state);
    }
  }

  /**
   * Sets up the vertical tab groupings.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function createGroups(array &$form, FormStateInterface $form_state) {
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
   *
   * @return \Drupal\bootstrap\Theme|FALSE
   *   The currently selected theme object or FALSE if not a Bootstrap theme.
   */
  protected function getTheme(array &$form, FormStateInterface $form_state) {
    $build_info = $form_state->getBuildInfo();
    $theme = isset($build_info['args'][0]) ? Bootstrap::getTheme($build_info['args'][0]) : FALSE;

    // Do not continue if the theme is not Bootstrap specific.
    if (!$theme || !$theme->subthemeOf('bootstrap')) {
      unset($form['#submit'][0]);
      unset($form['#validate'][0]);
    }

    return $theme;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $theme = $this->getTheme($form, $form_state);
    if (!$theme) {
      return;
    }

    $form_state->cleanValues();

    // Iterate over all setting plugins and allow them to participate.
    foreach ($theme->getSettingInstances() as $name => $setting) {
      $setting->submitForm($form, $form_state);

      // Remove values that didn't change so they don't get saved to config.
      // @todo This should ultimately iterate through the theme ancestry and
      // determine which settings are truly overridden from config.
      $old = serialize($theme->getSetting($name, TRUE));
      $new = serialize($form_state->getValue($name));
      if ($old === $new) {
        $form_state->unsetValue($name);
      }
    }

    // Clear out cache so it can get rebuilt.
    $theme->getCache('settings')->deleteAll();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $theme = $this->getTheme($form, $form_state);
    if (!$theme) {
      return;
    }

    // Iterate over all setting plugins and allow them to participate.
    foreach ($theme->getSettingInstances() as $setting) {
      $setting->validateForm($form, $form_state);
    }
  }

}

<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Bootstrap.
 */

namespace Drupal\bootstrap;

use Drupal\bootstrap\Alter\AlterInterface;
use Drupal\bootstrap\Form\FormInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * The primary class for the Drupal Bootstrap base theme.
 *
 * Provides many helper methods.
 */
class Bootstrap {

  /**
   * The current supported Bootstrap Framework version.
   */
  const FRAMEWORK_VERSION = '3.3.5';

  /**
   * The Bootstrap Framework documentation site.
   */
  const FRAMEWORK_HOMEPAGE = 'http://getbootstrap.com';

  /**
   * The Bootstrap Framework repository.
   */
  const FRAMEWORK_REPOSITORY = 'https://github.com/twbs/bootstrap';

  /**
   * The project branch.
   */
  const PROJECT_BRANCH = '8.x-3.x';

  /**
   * The Drupal Bootstrap documentation site.
   */
  const PROJECT_DOCUMENTATION = 'http://drupal-bootstrap.org';

  /**
   * The Drupal Bootstrap project page.
   */
  const PROJECT_PAGE = 'https://www.drupal.org/project/bootstrap';

  /**
   * Manages theme alter hooks as classes and allows sub-themes to sub-class.
   *
   * @param string $function
   *   The procedural function name of the alter (e.g. __FUNCTION__).
   * @param mixed $data
   *   The variable that was passed to the hook_TYPE_alter() implementation to
   *   be altered. The type of this variable depends on the value of the $type
   *   argument. For example, when altering a 'form', $data will be a structured
   *   array. When altering a 'profile', $data will be an object.
   * @param mixed $context1
   *   (optional) An additional variable that is passed by reference.
   * @param mixed $context2
   *   (optional) An additional variable that is passed by reference. If more
   *   context needs to be provided to implementations, then this should be an
   *   associative array as described above.
   */
  public static function alter($function, &$data, &$context1 = NULL, &$context2 = NULL) {
    $theme = Bootstrap::getTheme();

    // Immediately return if the active theme is not Bootstrap based.
    if (!$theme->subthemeOf('bootstrap')) {
      return;
    }

    // Extract the alter hook name.
    $hook = Utility::extractHook($function, 'alter');

    // Handle form alters separately.
    if (strpos($hook, 'form') === 0) {
      $form_id = $context2;
      if (!$form_id) {
        $form_id = Utility::extractHook($function, 'alter', 'form');
      }

      // Due to a core bug that affects admin themes, we should not double
      // process the "system_theme_settings" form twice in the global
      // hook_form_alter() invocation.
      // @see https://drupal.org/node/943212
      if ($context2 === 'system_theme_settings') {
        return;
      }

      // Retrieve a list of form definitions.
      $form_manager = new FormManager($theme);

      /** @var FormInterface $form */
      if ($form_manager->hasDefinition($form_id) && ($form = $form_manager->createInstance($form_id))) {
        $data['#submit'][] = [$form, 'submit'];
        $data['#validate'][] = [$form, 'validate'];
        $form->alter($data, $context1, $context2);
      }
    }
    // Process hook alter normally.
    else {
      // Retrieve a list of alter definitions.
      $alter_manager = new AlterManager($theme);

      /** @var AlterInterface $class */
      if ($alter_manager->hasDefinition($hook) && ($class = $alter_manager->createInstance($hook))) {
        $class->alter($data, $context2, $context2);
      }
    }
  }

  /**
   * Returns a documentation search URL for a given query.
   *
   * @param string $query
   *   The query to search for.
   *
   * @return string
   *   The complete URL to the documentation site.
   */
  public static function apiSearchUrl($query = '') {
    return static::PROJECT_DOCUMENTATION . '/api/bootstrap/' . static::PROJECT_BRANCH . '/search/' . Html::escape($query);
  }

  /**
   * Logs and displays a warning about a deprecated function/method being used.
   */
  public static function deprecated() {
    // Log backtrace.
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    \Drupal::logger('bootstrap')->warning('<pre><code>' . print_r($backtrace, TRUE) . '</code></pre>');

    if (!static::getTheme()->getSetting('suppress_deprecated_warnings')) {
      return;
    }

    // Extrapolate the caller.
    $caller = $backtrace[1];
    $class = '';
    if (isset($caller['class'])) {
      $parts = explode('\\', $caller['class']);
      $class = array_pop($parts) . '::';
    }
    drupal_set_message(t('The following function(s) or method(s) have been deprecated, please check the logs for a more detailed backtrace on where these are being invoked. Click on the function or method link to search the documentation site for a possible replacement or solution.'), 'warning');
    drupal_set_message(t('<a href=":url" target="_blank">@title</a>.', [
      ':url' => static::apiSearchUrl($class . $caller['function']),
      '@title' => ($class ? $caller['class'] . $caller['type'] : '') . $caller['function'] . '()',
    ]), 'warning');
  }

  /**
   * Returns the theme hook definition information.
   *
   * This base-theme's custom theme hook implementations. Never define "path"
   * or "template" as these are detected and automatically added.
   *
   * @see bootstrap_theme_registry_alter()
   * @see \Drupal\bootstrap\Registry
   * @see hook_theme()
   */
  public static function getInfo() {
    $hooks['bootstrap_carousel'] = [
      'variables' => [
        'attributes' => [],
        'items' => [],
        'start_index' => 0,
        'controls' => TRUE,
        'indicators' => TRUE,
        'interval' => 5000,
        'pause' => 'hover',
        'wrap' => TRUE,
      ],
    ];

    $hooks['bootstrap_dropdown'] = [
      'render element' => 'element',
    ];

    $hooks['bootstrap_modal'] = [
      'variables' => [
        'heading' => '',
        'body' => '',
        'footer' => '',
        'dialog_attributes' => [],
        'attributes' => [],
        'size' => '',
        'html_heading' => FALSE,
      ],
    ];

    $hooks['bootstrap_panel'] = [
      'render element' => 'element',
    ];
    return $hooks;
  }

  /**
   * Retrieves a theme instance of \Drupal\bootstrap\Theme.
   *
   * @param string|\Drupal\Core\Extension\Extension $theme
   *   The machine name or \Drupal\Core\Extension\Extension object. If
   *   omitted, the active theme will be used.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler object.
   *
   * @return \Drupal\bootstrap\Theme
   *   A theme object.
   */
  public static function getTheme($theme = NULL, ThemeHandlerInterface $theme_handler = NULL) {
    // Immediately return if theme passed is already instantiated.
    if ($theme instanceof Theme) {
      return $theme;
    }

    static $themes = [];

    if (!isset($theme_handler)) {
      $theme_handler = \Drupal::service('theme_handler');
    }
    if (!isset($theme)) {
      $theme = \Drupal::theme()->getActiveTheme()->getName();
    }
    if (is_string($theme)) {
      $theme = $theme_handler->getTheme($theme);
    }

    if (!($theme instanceof Extension)) {
      throw new \InvalidArgumentException(sprintf('The $theme argument provided is not of the class \Drupal\Core\Extension\Extension: %s.', $theme));
    }

    if (!isset($themes[$theme->getName()])) {
      $themes[$theme->getName()] = new Theme($theme, $theme_handler);
    }

    return $themes[$theme->getName()];
  }

}

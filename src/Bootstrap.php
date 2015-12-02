<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Bootstrap.
 */

namespace Drupal\bootstrap;

use Drupal\Component\Utility\Html;

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
   * The Drupal Bootstrap project rep.
   */
  const PROJECT_PAGE = 'https://www.drupal.org/project/bootstrap';

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

    if (!BaseTheme::getTheme()->getSetting('suppress_deprecated_warnings')) {
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


}

<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\PrerenderManager.
 */

namespace Drupal\bootstrap\Plugin;

use Drupal\bootstrap\Theme;
use Drupal\bootstrap\Utility\Element;

/**
 * Manages discovery and instantiation of Bootstrap pre-render callbacks.
 */
class PrerenderManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\Plugin\PrerenderManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Prerender', 'Drupal\bootstrap\Plugin\Prerender\PrerenderInterface', 'Drupal\bootstrap\Annotation\BootstrapPrerender');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':prerender', $this->getCacheTags());
  }

  /**
   * Pre-render render array element callback.
   *
   * @param array $element
   *   The render array element.
   *
   * @return array
   *   The modified render array element.
   */
  public static function preRender(array $element) {
    if (!empty($element['#bootstrap_ignore_pre_render'])) {
      return $element;
    }

    // Only add the "form-control" class for specific element input types.
    $types = [
      // Core.
      'password',
      'password_confirm',
      'select',
      'textarea',
      'textfield',

      // Elements module (HTML5).
      'date',
      'datefield',
      'email',
      'emailfield',
      'number',
      'numberfield',
      'range',
      'rangefield',
      'search',
      'searchfield',
      'tel',
      'telfield',
      'url',
      'urlfield',

      // Webform module.
      'webform_email',
      'webform_number',
    ];

    $e = new Element($element);

    // Add necessary classes for specific types.
    if ($e->isType($types) || ($e->isType('file') && !$e->getProperty('managed_file'))) {
      $element['#attributes']['class'][] = 'form-control';
    }
    elseif ($e->isType('machine_name')) {
      $element['#wrapper_attributes']['class'][] = 'form-inline';
    }

    // Add smart descriptions to the element, if necessary.
    $e->smartDescription();

    return $element;
  }

}

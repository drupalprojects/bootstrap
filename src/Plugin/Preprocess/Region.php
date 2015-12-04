<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Region.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;

/**
 * Pre-processes variables for the "region" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "region"
 * )
 */
class Region implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables) {
    $region = $variables['elements']['#region'];
    $variables['region'] = $region;
    $variables['content'] = $variables['elements']['#children'];

    $theme = Bootstrap::getTheme();

    // Content region.
    if ($region === 'content') {
      // @todo is this actually used properly?
      $variables['theme_hook_suggestions'][] = 'region__no_wrapper';
    }
    // Help region.
    elseif ($region === 'help' && !empty($variables['content'])) {
      $content = $variables['content'];
      $variables['content'] = [
        'icon' => Bootstrap::glyphicon('question-sign'),
        'content' => ['#markup' => $content],
      ];
      $variables['attributes']['class'][] = 'alert';
      $variables['attributes']['class'][] = 'alert-info';
      $variables['attributes']['class'][] = 'messages';
      $variables['attributes']['class'][] = 'info';
    }

    // Support for "well" classes in regions.
    static $wells;
    if (!isset($wells)) {
      foreach (system_region_list($theme->getName()) as $name => $title) {
        $wells[$name] = $theme->getSetting('region_well-' . $name);
      }
    }
    if (!empty($wells[$region])) {
      $variables['attributes']['class'][] = $wells[$region];
    }
  }

}

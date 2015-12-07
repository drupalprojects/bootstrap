<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Breadcrumb.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "breadcrumb" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "breadcrumb"
 * )
 */
class Breadcrumb implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $theme = Bootstrap::getTheme();
    $breadcrumb = &$variables['breadcrumb'];

    // Optionally get rid of the homepage link.
    $show_breadcrumb_home = $theme->getSetting('breadcrumb_home');
    if (!$show_breadcrumb_home) {
      array_shift($breadcrumb);
    }

    if ($theme->getSetting('breadcrumb_title') && !empty($breadcrumb)) {
      $request = \Drupal::request();
      $route_match = \Drupal::routeMatch();
      $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());

      if (!empty($page_title)) {
        $breadcrumb[] = [
          'text' => $page_title,
          'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
    }
  }

}

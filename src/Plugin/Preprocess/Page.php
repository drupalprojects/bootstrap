<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Page.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "page"
 * )
 */
class Page extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    // Add information about the number of sidebars.
    $variables['content_column_attributes'] = new Attribute();
    $variables['content_column_attributes']['class'] = [];
    if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
      $variables['content_column_attributes']['class'][] = 'col-sm-6';
    }
    elseif (!empty($variables['page']['sidebar_first']) || !empty($variables['page']['sidebar_second'])) {
      $variables['content_column_attributes']['class'][] = 'col-sm-9';
    }
    else {
      $variables['content_column_attributes']['class'][] = 'col-sm-12';
    }

    $variables['navbar_attributes'] = new Attribute();
    $variables['navbar_attributes']['class'] = ['navbar'];
    if ($this->theme->getSetting('navbar_position') !== '') {
      $variables['navbar_attributes']['class'][] = 'navbar-' . $this->theme->getSetting('navbar_position');
    }
    else {
      $variables['navbar_attributes']['class'][] = 'container';
    }
    if ($this->theme->getSetting('navbar_inverse')) {
      $variables['navbar_attributes']['class'][] = 'navbar-inverse';
    }
    else {
      $variables['navbar_attributes']['class'][] = 'navbar-default';
    }
  }

}

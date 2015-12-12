<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Page.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess("page")
 */
class Page extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   *
   * @todo Move all of this into the "page.html.twig" template.
   */
  public function preprocessVariables(Variables $variables, $hook, array $info) {
    // Columns.
    if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
      $variables->addClass('col-sm-6', 'content_column_attributes');
    }
    elseif (!empty($variables['page']['sidebar_first']) || !empty($variables['page']['sidebar_second'])) {
      $variables->addClass('col-sm-9', 'content_column_attributes');
    }
    else {
      $variables->addClass('col-sm-12', 'content_column_attributes');
    }

    // Navbar.
    $position = $this->theme->getSetting('navbar_position');
    $variables->addClass(($position ? "navbar-$position" : 'container'), 'navbar_attributes');
    $variables->addClass(($this->theme->getSetting('navbar_inverse') ? 'navbar-inverse' : 'navbar-default'), 'navbar_attributes');

    // Ensure attributes are proper objects.
    $this->preprocessAttributes($variables, $hook, $info);
  }

}

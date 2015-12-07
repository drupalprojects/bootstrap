<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\ViewsViewTable.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;

/**
 * Pre-processes variables for the "views_view_table" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "views_view_table"
 * )
 */
class ViewsViewTable implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    Table::addClasses($variables['attributes']['class'], $variables);
  }

}

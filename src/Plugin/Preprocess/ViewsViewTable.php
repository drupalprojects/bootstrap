<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\ViewsViewTable.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

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
  public function preprocess(array &$variables) {
    Table::addClasses($variables['attributes']['class'], $variables);
  }

}

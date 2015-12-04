<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\MenuLocalTasks.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;

/**
 * Implements hook_menu_local_tasks_alter().
 *
 * @BootstrapAlter(
 *   id = "menu_local_tasks"
 * )
 */
class MenuLocalTasks implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$data, &$route_name = NULL, &$context2 = NULL) {
    if (!empty($data['actions']['output'])) {
      $items = [];
      foreach ($data['actions']['output'] as $item) {
        $items[] = [
          'data' => $item,
        ];
      }
      $data['actions']['output'] = [
        '#theme' => 'item_list__action_links',
        '#items' => $items,
        '#attributes' => [
          'class' => ['action-links'],
        ],
      ];
    }
  }

}

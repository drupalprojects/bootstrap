<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\MenuLocalTasks.
 */

namespace Drupal\bootstrap\Alter;

/**
 * Implements hook_menu_local_tasks_alter().
 */
class MenuLocalTasks implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$data, &$route_name = NULL, &$context2 = NULL) {
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

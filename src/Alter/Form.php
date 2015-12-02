<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\Form.
 */

namespace Drupal\bootstrap\Alter;

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Utility;

/**
 * Implements hook_form_alter().
 */
class Form implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$form, &$form_state = NULL, &$form_id = NULL) {
    if ($form_id) {
      // Due to a core bug that affects admin themes, we should not double
      // process the "system_theme_settings" form twice.
      // @see https://drupal.org/node/943212
      if ($form_id === 'system_theme_settings') {
        return;
      }

      $theme = Bootstrap::getTheme();

      // Retrieve cache.
      $form_alter = $theme->getCache('form_alter', []);
      if (!$form_alter->has($form_id)) {
        $class = FALSE;

        // Convert the form ID to a proper class name.
        $name = Utility::snakeToCamelCase($form_id);

        // Determine if the function has a valid class counterpart.
        if ($reflection = Utility::findClass($name, 'Form')) {
          if ($reflection->implementsInterface('\\Drupal\\bootstrap\\Form\\FormAlterInterface')) {
            $class = $reflection->getName();
          }
        }

        $form_alter->set($form_id, $class);
      }

      // Only continue if there is a valid callback.
      if ($class = $form_alter->get($form_id)) {
        $callback = [$class, 'alter'];
        $form['#submit'][] = [$class, 'submit'];
        $form['#validate'][] = [$class, 'validate'];
        call_user_func_array($callback, array(&$form, &$form_state, $form_id));
      }
    }
  }

}

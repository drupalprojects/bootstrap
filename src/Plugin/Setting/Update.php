<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Setting\Update.
 */

namespace Drupal\bootstrap\Plugin\Setting;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\Core\Form\FormStateInterface;

/**
 * The "schema" theme setting.
 *
 * @BootstrapSetting(
 *   id = "schema",
 *   type = "hidden",
 *   weight = -1,
 *   groups = false,
 * )
 */
class Update extends SettingBase {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {

  }

}

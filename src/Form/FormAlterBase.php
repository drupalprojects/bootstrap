<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Form\FormAlterBase.
 */

namespace Drupal\bootstrap\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Base form alter class.
 */
class FormAlterBase implements FormAlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(array &$form, FormStateInterface &$form_state, $form_id = NULL) {}

  /**
   * {@inheritdoc}
   */
  public static function submit(array $form, FormStateInterface &$form_state, $form_id = NULL) {}

  /**
   * {@inheritdoc}
   */
  public static function validate(array &$form, FormStateInterface &$form_state, $form_id = NULL) {}

}

<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\FormBase.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Base form alter class.
 */
class FormBase implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(array &$form, FormStateInterface $form_state, $form_id = NULL) {}

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state, $form_id = NULL) {}

  /**
   * {@inheritdoc}
   */
  public function validate(array &$form, FormStateInterface $form_state, $form_id = NULL) {}

}

<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Setting\JavaScript\Anchors\AnchorsSmoothScrolling.
 */

namespace Drupal\bootstrap\Plugin\Setting\JavaScript\Anchors;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;

/**
 * The "anchors_smooth_scrolling" theme setting.
 *
 * @BootstrapSetting(
 *   id = "anchors_smooth_scrolling",
 *   type = "checkbox",
 *   title = @Translation("Enable smooth scrolling"),
 *   description = @Translation("Animates page by scrolling to an anchor link target smoothly when clicked."),
 *   defaultValue = 0,
 *   groups = {
 *     "javascript" = @Translation("JavaScript"),
 *     "anchors" = @Translation("Anchors"),
 *   },
 *   disabled = true,
 * )
 */
class AnchorsSmoothScrolling extends SettingBase {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $element = $this->getElement($form, $form_state);
    $element->setProperty('states', [
      'invisible' => [
        ':input[name="anchors_fix"]' => ['checked' => FALSE],
      ],
    ]);
  }

}

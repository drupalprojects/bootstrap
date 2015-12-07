<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Setting\JavaScript\Anchors\AnchorsFix.
 */

namespace Drupal\bootstrap\Plugin\Setting\JavaScript\Anchors;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;

/**
 * The "anchors_fix" theme setting.
 *
 * @BootstrapSetting(
 *   id = "anchors_fix",
 *   type = "checkbox",
 *   title = @Translation("Fix anchor positions"),
 *   description = @Translation("Ensures anchors are correctly positioned only when there is margin or padding detected on the BODY element. This is useful when fixed navbar or administration menus are used."),
 *   defaultValue = 0,
 *   groups = {
 *     "javascript" = @Translation("JavaScript"),
 *     "anchors" = @Translation("Anchors"),
 *   },
 *   disabled = true,
 * )
 */
class AnchorsFix extends SettingBase {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    parent::alterForm($form, $form_state, $form_id);

    $group = $this->getGroup($form, $form_state);
    $group->setProperty('description', t('This plugin cannot be configured from the UI as it is severely broken. In an effort to balance not break backwards compatibility and to prevent new users from running into unforeseen issues, you must manually opt-in a sub-theme\'s setting configuration file. Please see the following issue for more details: <a href=":url" target="_blank">Replace custom JS with the bootstrap-anchor plugin</a>', [
      ':url' => 'https://www.drupal.org/node/2462645',
    ]));
  }

}

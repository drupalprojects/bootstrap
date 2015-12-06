<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Setting\JavaScript\Forms\FormsHasErrorValueToggle.
 */

namespace Drupal\bootstrap\Plugin\Setting\JavaScript\Forms;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "forms_has_error_value_toggle" theme setting.
 *
 * @BootstrapSetting(
 *   id = "forms_has_error_value_toggle",
 *   type = "checkbox",
 *   title = @Translation("Automatically remove error classes when values have been entered"),
 *   description = @Translation("If an element has a <code>.has-error</code> class attached to it, enabling this will automatically remove that class when a value is entered. This feature compliments the <code>General > Forms > Make required elements display as an error</code> feature."),
 *   defaultValue = 1,
 *   groups = {
 *     "javascript" = @Translation("JavaScript"),
 *     "forms" = @Translation("Forms"),
 *   },
 * )
 */
class FormsHasErrorValueToggle extends SettingBase {}

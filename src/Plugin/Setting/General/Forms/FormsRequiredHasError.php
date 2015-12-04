<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Setting\General\Forms\FormsRequiredHasError.
 */

namespace Drupal\bootstrap\Plugin\Setting\General\Forms;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "forms_required_has_error" theme setting.
 *
 * @BootstrapSetting(
 *   id = "forms_required_has_error",
 *   type = "checkbox",
 *   title = @Translation("Make required elements display as an error"),
 *   defaultValue = 1,
 *   description = @Translation("If an element in a form is required, enabling this will always display the element with a <code>.has-error</code> class. This turns the element red and helps in usability for determining which form elements are required to submit the form.  This feature compliments the <code>JavaScript > Forms > Automatically remove error classes when values have been entered</code> feature."),
 *   groups = {
 *     "general" = @Translation("General"),
 *     "forms" = @Translation("Forms"),
 *   },
 * )
 */
class FormsRequiredHasError extends SettingBase {}

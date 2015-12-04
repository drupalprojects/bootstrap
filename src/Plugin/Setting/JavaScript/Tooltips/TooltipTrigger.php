<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Setting\JavaScript\Tooltips\TooltipTrigger.
 */

namespace Drupal\bootstrap\Plugin\Setting\JavaScript\Tooltips;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;

/**
 * The "tooltip_trigger" theme setting.
 *
 * @BootstrapSetting(
 *   id = "tooltip_trigger",
 *   type = "checkboxes",
 *   title = @Translation("trigger"),
 *   description = @Translation("How a tooltip is triggered."),
 *   defaultValue = {
 *     "click" = 0,
 *     "hover" = "hover",
 *     "focus" = "focus",
 *     "manual" = 0,
 *   },
 *   options = {
 *     "click" = @Translation("click"),
 *     "hover" = @Translation("hover"),
 *     "focus" = @Translation("focus"),
 *     "manual" = @Translation("manual"),
 *   },
 *   groups = {
 *     "javascript" = @Translation("JavaScript"),
 *     "tooltips" = @Translation("Tooltips"),
 *     "options" = @Translation("Options"),
 *   },
 * )
 */
class TooltipTrigger extends SettingBase {}

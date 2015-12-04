<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\PageAttachments.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Bootstrap;

/**
 * Implements hook_page_attachments_alter().
 *
 * @BootstrapAlter(
 *   id = "page_attachments"
 * )
 */
class PageAttachments implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$attachments, &$context1 = NULL, &$context2 = NULL) {
    $theme = Bootstrap::getTheme();
    $attachments['#attached']['drupalSettings']['bootstrap'] = [
      'anchorsFix' => $theme->getSetting('anchors_fix'),
      'anchorsSmoothScrolling' => $theme->getSetting('anchors_smooth_scrolling'),
      'formHasError' => (int) $theme->getSetting('forms_has_error_value_toggle'),
      'popoverEnabled' => $theme->getSetting('popover_enabled'),
      'popoverOptions' => [
        'animation' => (int) $theme->getSetting('popover_animation'),
        'html' => (int) $theme->getSetting('popover_html'),
        'placement' => $theme->getSetting('popover_placement'),
        'selector' => $theme->getSetting('popover_selector'),
        'trigger' => implode(' ', array_filter(array_values((array) $theme->getSetting('popover_trigger')))),
        'triggerAutoclose' => (int) $theme->getSetting('popover_trigger_autoclose'),
        'title' => $theme->getSetting('popover_title'),
        'content' => $theme->getSetting('popover_content'),
        'delay' => (int) $theme->getSetting('popover_delay'),
        'container' => $theme->getSetting('popover_container'),
      ],
      'tooltipEnabled' => $theme->getSetting('tooltip_enabled'),
      'tooltipOptions' => [
        'animation' => (int) $theme->getSetting('tooltip_animation'),
        'html' => (int) $theme->getSetting('tooltip_html'),
        'placement' => $theme->getSetting('tooltip_placement'),
        'selector' => $theme->getSetting('tooltip_selector'),
        'trigger' => implode(' ', array_filter(array_values((array) $theme->getSetting('tooltip_trigger')))),
        'delay' => (int) $theme->getSetting('tooltip_delay'),
        'container' => $theme->getSetting('tooltip_container'),
      ],
    ];
  }

}

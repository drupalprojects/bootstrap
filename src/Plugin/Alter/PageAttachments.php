<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\PageAttachments.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\PluginBase;

/**
 * Implements hook_page_attachments_alter().
 *
 * @BootstrapAlter(
 *   id = "page_attachments"
 * )
 */
class PageAttachments extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$attachments, &$context1 = NULL, &$context2 = NULL) {
    $attachments['#attached']['drupalSettings']['bootstrap'] = [
      'anchorsFix' => $this->theme->getSetting('anchors_fix'),
      'anchorsSmoothScrolling' => $this->theme->getSetting('anchors_smooth_scrolling'),
      'formHasError' => (int) $this->theme->getSetting('forms_has_error_value_toggle'),
      'popoverEnabled' => $this->theme->getSetting('popover_enabled'),
      'popoverOptions' => [
        'animation' => (int) $this->theme->getSetting('popover_animation'),
        'html' => (int) $this->theme->getSetting('popover_html'),
        'placement' => $this->theme->getSetting('popover_placement'),
        'selector' => $this->theme->getSetting('popover_selector'),
        'trigger' => implode(' ', array_filter(array_values((array) $this->theme->getSetting('popover_trigger')))),
        'triggerAutoclose' => (int) $this->theme->getSetting('popover_trigger_autoclose'),
        'title' => $this->theme->getSetting('popover_title'),
        'content' => $this->theme->getSetting('popover_content'),
        'delay' => (int) $this->theme->getSetting('popover_delay'),
        'container' => $this->theme->getSetting('popover_container'),
      ],
      'tooltipEnabled' => $this->theme->getSetting('tooltip_enabled'),
      'tooltipOptions' => [
        'animation' => (int) $this->theme->getSetting('tooltip_animation'),
        'html' => (int) $this->theme->getSetting('tooltip_html'),
        'placement' => $this->theme->getSetting('tooltip_placement'),
        'selector' => $this->theme->getSetting('tooltip_selector'),
        'trigger' => implode(' ', array_filter(array_values((array) $this->theme->getSetting('tooltip_trigger')))),
        'delay' => (int) $this->theme->getSetting('tooltip_delay'),
        'container' => $this->theme->getSetting('tooltip_container'),
      ],
    ];
  }

}

<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Provider\Custom.
 */

namespace Drupal\bootstrap\Plugin\Provider;

use Drupal\bootstrap\Utility\Element;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Form\FormStateInterface;

/**
 * The "custom" CDN provider plugin.
 *
 * @BootstrapProvider(
 *   id = "custom",
 *   label = @Translation("Custom"),
 * )
 */
class Custom extends ProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getAssets($types = NULL) {
    $this->assets = [];
    foreach ($types as $type) {
      if ($setting = $this->theme->getSetting('cdn_custom_' . $type)) {
        $this->assets[$type][] = $setting;
      }
      if ($setting = $this->theme->getSetting('cdn_custom_' . $type . '_min')) {
        $this->assets['min'][$type][] = $setting;
      }
    }
    return parent::getAssets($types);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(Element $settings, FormStateInterface $form_state) {
    foreach (['css', 'js'] as $type) {
      $setting = $this->theme->getSetting('cdn_custom_' . $type);
      $setting_min = $this->theme->getSetting('cdn_custom_' . $type . '_min');
      $settings->{'cdn_custom_' . $type} = [
        '#type' => 'textfield',
        '#title' => t('Bootstrap @type URL', [
          '@type' => Unicode::strtoupper($type),
        ]),
        '#description' => t('It is best to use protocol relative URLs (i.e. without http: or https:) here as it will allow more flexibility if the need ever arises.'),
        '#default_value' => $setting,
      ];
      $settings->{'cdn_custom_' . $type . '_min'} = [
        '#type' => 'textfield',
        '#title' => t('Minified Bootstrap @type URL', [
          '@type' => Unicode::strtoupper($type),
        ]),
        '#description' => t('Additionally, you can provide the minimized version of the file. It will be used instead if site aggregation is enabled.'),
        '#default_value' => $setting_min,
      ];
    }
  }

}

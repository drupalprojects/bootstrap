<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Provider\ProviderInterface.
 */

namespace Drupal\bootstrap\Plugin\Provider;

use Drupal\bootstrap\Utility\Element;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * ProviderInterface.
 */
interface ProviderInterface extends PluginInspectionInterface, DerivativeInspectionInterface {

  /**
   * Retrieves Provider assets for the active provider, if any.
   *
   * @param string|array $types
   *   The type of asset to retrieve: "css" or "js", defaults to an array
   *   array containing both if not set.
   *
   * @return array
   *   If $type is a string or an array with only one (1) item in it, the
   *   assets are returned as an indexed array of files. Otherwise, an
   *   associative array is returned keyed by the type.
   */
  public function getAssets($types = NULL);

  /**
   * Retrieves the provider description.
   *
   * @return string
   *   The provider description.
   */
  public function getDescription();

  /**
   * Retrieves the provider human-readable label.
   *
   * @return string
   *   The provider human-readable label.
   */
  public function getLabel();

  /**
   * Flag indicating that the API data parsing failed.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function hasError();

  /**
   * Flag indicating that the API data was manually imported.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function isImported();

  /**
   * Processes the provider plugin definition upon discovery.
   *
   * @param array $definition
   *   The provider plugin definition.
   * @param string $plugin_id
   *   The plugin identifier.
   */
  public function processDefinition(array &$definition, $plugin_id);

  /**
   * Processes the provider plugin definition upon discovery.
   *
   * @param array $json
   *   The JSON data retrieved from the API request.
   * @param array $definition
   *   The provider plugin definition.
   */
  public function processApi(array $json, array &$definition);

  /**
   * Provides settings for a Provider provider.
   *
   * @param \Drupal\bootstrap\Utility\Element $settings
   *   The settings render array element designated for the provider.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function settingsForm(Element $settings, FormStateInterface $form_state);

}

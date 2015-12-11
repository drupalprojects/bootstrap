<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\PreprocessBase.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Utility\Variables;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Template\Attribute;

/**
 * Base preprocess class used to build the necessary variables for templates.
 *
 * @ingroup theme_preprocess
 */
class PreprocessBase extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $vars = new Variables($variables);

    // Preprocess variables.
    $this->preProcessVariables($vars, $hook, $info);

    // Post-process variables.
    $this->postProcessVariables($vars, $hook, $info);
  }

  /**
   * Preprocess the variables array.
   *
   * @param \Drupal\bootstrap\Utility\Variables $variables
   *   A variables object.
   * @param string $hook
   *   The name of the theme hook.
   * @param array $info
   *   The theme hook info array.
   */
  protected function preProcessVariables(Variables $variables, $hook, array $info) {}

  /**
   * Post-process the variables array.
   *
   * @param \Drupal\bootstrap\Utility\Variables $variables
   *   A variables object.
   * @param string $hook
   *   The name of the theme hook.
   * @param array $info
   *   The theme hook info array.
   */
  protected function postProcessVariables(Variables $variables, $hook, array $info) {
    // Convert descriptions into a traversable array.
    // @see https://www.drupal.org/node/2324025
    if ($variables->offsetGet('description')) {
      // Retrieve the description attributes.
      $description_attributes = $variables->offsetGet('description_attributes', []);

      // Remove standalone description attributes.
      $variables->offsetUnset('description_attributes');

      // Build the description attributes.
      if ($id = $variables->getAttribute('id')) {
        $variables->setAttribute('aria-describedby', "$id--description");
        $description_attributes['id'] = "$id--description";
      }

      // Replace the description variable.
      $variables->offsetSet('description', [
        'attributes' => new Attribute($description_attributes),
        'content' => $variables['description'],
        'position' => $variables->offsetGet('description_display', 'after'),
      ]);
    }

    // Ensure all attributes have been converted to an Attribute object.
    foreach ($variables as $name => $value) {
      if (strpos($name, 'attributes') !== FALSE && is_array($value)) {
        $variables[$name] = new Attribute($value);
      }
    }
  }

}

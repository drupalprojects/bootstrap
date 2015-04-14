<?php

/**
 * @file
 *  pager.vars.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Adds wrapper Bootstrap classes to pager.html.twig.
 * Default template: pager.html.twig.
 */
function bootstrap_preprocess_pager(&$variables) {
  $wrapper_class = new Attribute();
  $wrapper_class->setAttribute('class', 'text-center');

  $attributes = new Attribute($variables['attributes']);
  $attributes->setAttribute('class', 'pagination');

  $variables['wrapper_classes'] = $wrapper_class;
  $variables['attributes'] = $attributes;
}

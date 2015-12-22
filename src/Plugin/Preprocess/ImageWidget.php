<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\ImageWidget.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "image_widget" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @see image-widget.html.twig
 *
 * @BootstrapPreprocess("image_widget",
 *   replace = "template_preprocess_image_widget"
 * )
 */
class ImageWidget extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Variables $variables, $hook, array $info) {
    $variables->addClass(['image-widget', 'js-form-managed-file', 'form-managed-file', 'clearfix']);

    /** @var \Drupal\file\Entity\File $file */
    foreach ($variables->element->getProperty('files') as $file) {
      $variables->element->{'file_' . $file->id()}->filename->setProperty('suffix', ' <span class="file-size badge">' . format_size($file->getSize()) . '</span>');
    }

    $data = &$variables->offsetGet('data', []);
    foreach ($variables->element->children() as $key => $child) {
      $data[$key] = $child->getArray();
    }
  }

}

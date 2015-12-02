<?php
/**
 * @file
 * Contains \Drupal\bootstrap\BaseTheme.
 */

namespace Drupal\bootstrap;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * Manages classes to be used in Drupal Bootstrap based themes.
 */
class BaseTheme {

  /**
   * Manages theme alter hooks as classes and allows sub-themes to sub-class.
   *
   * @param string $function
   *   The procedural function name of the alter (e.g. __FUNCTION__).
   * @param mixed $data
   *   The variable that will be passed to hook_TYPE_alter() implementations to
   *   be altered. The type of this variable depends on the value of the $type
   *   argument. For example, when altering a 'form', $data will be a structured
   *   array. When altering a 'profile', $data will be an object.
   * @param mixed $context1
   *   (optional) An additional variable that is passed by reference.
   * @param mixed $context2
   *   (optional) An additional variable that is passed by reference. If more
   *   context needs to be provided to implementations, then this should be an
   *   associative array as described above.
   * @param string|array $namespace
   *   (optional) Allows a different namespace prefix to be used. By default
   *   the active theme's name space is used with "Alter" appended:
   *   e.g. \Drupal\bootstrap\Alter.
   */
  public static function alter($function, &$data, &$context1 = NULL, &$context2 = NULL, $namespace = NULL) {
    $theme = static::getTheme();

    // Immediately return if the active theme is not Bootstrap based.
    if (!$theme->subthemeOf('bootstrap')) {
      return;
    }

    // Retrieve cache.
    $alter = $theme->getCache('alter', []);

    // Determine if the function has a valid class counterpart.
    if (!$alter->has($function)) {
      $callback = FALSE;

      // Iterate over all potential class names per theme.
      $classes = [];

      /** @var Theme $ancestor */
      foreach (array_reverse($theme->getAncestry()) as $ancestor) {
        $namespace = isset($namespace) ? explode('\\', $namespace) : ['Drupal', $ancestor->getName(), 'Alter'];
        $namespace[] = static::stringToClassName(preg_replace('/^' . preg_quote($ancestor->getName()) . '_|_alter$/', '', $function));
        $classes[] = implode('\\', $namespace);
      }

      foreach ($classes as $name) {
        $class = BaseTheme::reflectionClass($name);
        if ($class && $class->hasMethod('alter') && (
            $class->implementsInterface('\\Drupal\\bootstrap\\Alter\\AlterInterface') ||
            $class->implementsInterface('\\Drupal\\bootstrap\\Alter\\FormInterface'))
        ) {
          $callback = $name;
          break;
        }
      }

      $alter->set($function, $callback);
    }

    // Only continue if class is valid.
    if (($class = $alter->get($function)) && ($reflection = BaseTheme::reflectionClass($class))) {
      $class = $reflection->newInstanceWithoutConstructor();
      $class::alter($data, $context1, $context2);
    }
  }

  public function findClass($class) {

  }

  /**
   * Retrieves a theme instance of \Drupal\bootstrap\Theme.
   *
   * @param string|\Drupal\Core\Extension\Extension $theme
   *   The machine name or \Drupal\Core\Extension\Extension object. If
   *   omitted, the active theme will be used.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler object.
   *
   * @return \Drupal\bootstrap\Theme
   *   A theme object.
   */
  public static function getTheme($theme = NULL, ThemeHandlerInterface $theme_handler = NULL) {
    // Immediately return if theme passed is already instantiated.
    if ($theme instanceof Theme) {
      return $theme;
    }

    static $themes = [];

    if (!isset($theme_handler)) {
      $theme_handler = \Drupal::service('theme_handler');
    }
    if (!isset($theme)) {
      $theme = \Drupal::theme()->getActiveTheme()->getName();
    }
    if (is_string($theme)) {
      $theme = $theme_handler->getTheme($theme);
    }

    if (!($theme instanceof Extension)) {
      throw new \InvalidArgumentException(sprintf('The $theme argument provided is not of the class \Drupal\Core\Extension\Extension: %s.', $theme));
    }

    if (!isset($themes[$theme->getName()])) {
      $themes[$theme->getName()] = new Theme($theme, $theme_handler);
    }

    return $themes[$theme->getName()];
  }

  /**
   * Retrieves a class reflection object.
   *
   * @param string $name
   *   The name of the class.
   *
   * @return \ReflectionClass
   *   The reflection class object.
   */
  public static function reflectionClass($name) {
    try {
      return new \ReflectionClass($name);
    }
    catch (\Exception $e) {
    }
  }

  /**
   * Converts a string into a class name.
   *
   * @param string $string
   *   The string to convert.
   *
   * @return string
   *   The converted string to class name.
   */
  public static function stringToClassName($string) {
    static $strings = [];
    if (!isset($strings[$string])) {
      $name = preg_replace('/(_|-)+/', '_', Unicode::strtolower($string));
      $name = preg_replace('/[^a-z0-9_]+/', '', $name);
      $name = explode('_', $name);
      foreach ($name as &$word) {
        $word = Unicode::ucfirst($word);
      }
      $strings[$string] = implode('', $name);
    }
    return $strings[$string];
  }

}

<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Cache.
 */

// Name of the base theme must be lowercase for it to be autoload discoverable.
namespace Drupal\bootstrap;

use Drupal\Core\KeyValueStore\MemoryStorage;

/**
 * Wraps the database theme cache entry in memory to reduce multiple calls.
 */
class Cache extends MemoryStorage {

  protected $cid;

  /**
   * {@inheritdoc}
   */
  public function __construct($cid = NULL, array $data = []) {
    if (!isset($cid)) {
      $cid = 'theme_registry:cache:' . Bootstrap::getTheme()->getName();
    }
    $this->cid = $cid;
    $this->data = $data;
    if (($cache = \Drupal::cache()->get($this->cid)) && !empty($cache->data)) {
      $this->data = $cache->data + $data;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $value) {
    parent::set($key, $value);
    static::save();
  }

  /**
   * {@inheritdoc}
   */
  public function setIfNotExists($key, $value) {
    if (!isset($this->data[$key])) {
      $this->data[$key] = $value;
      static::save();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $data) {
    parent::setMultiple($data);
    static::save();
  }

  /**
   * {@inheritdoc}
   */
  public function rename($key, $new_key) {
    parent::rename($key, $new_key);
    static::save();
  }

  /**
   * {@inheritdoc}
   */
  public function delete($key) {
    parent::delete($key);
    static::save();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $keys) {
    parent::deleteMultiple($keys);
    static::save();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    parent::deleteAll();
    static::save();
  }

  /**
   * Saves the cache values to the database.
   */
  protected function save() {
    \Drupal::cache()->set($this->cid, $this->data, \Drupal\Core\Cache\Cache::PERMANENT, ['theme_registry']);
  }

}

<?php

namespace Drupal\lite\Plugin\CKEditorPlugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginInterface;

/**
 * Defines the "Drupal lite" plugin.
 *
 * @CKEditorPlugin(
 *   id = "drupallite",
 *   label = @Translation("Drupal lite extension plugin."),
 *   module = "lite"
 * )
 */
class DrupalLite extends PluginBase implements CKEditorPluginInterface {

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getFile().
   */
  public function getFile() {
    return drupal_get_path('module', 'lite') . '/js/plugins/drupallite/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $config = [];

    // Load current user permissions.
    $config['drupallite'] = [
      'permissions' => lite_get_permissions(),
    ];

    return $config;
  }

}

<?php

namespace Drupal\lite\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginContextualInterface;

/**
 * Defines the "Drupal lite" plugin.
 *
 * @CKEditorPlugin(
 *   id = "drupallite",
 *   label = @Translation("Lite"),
 *   module = "lite"
 * )
 */
class DrupalLite extends CKEditorPluginBase implements CKEditorPluginContextualInterface {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'lite') . '/js/plugins/drupallite/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    // Buttons are managed by lite.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    // Config is managed by lite.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(Editor $editor) {
    if (!$editor->hasAssociatedFilterFormat()) {
      return FALSE;
    }

    // Automatically enable this plugin if the text format associated with this
    // text editor uses any lite button.
    $enabled = FALSE;
    $settings = $editor->getSettings();

    foreach ($settings['toolbar']['rows'] as $row) {
      foreach ($row as $group) {
        foreach ($group['items'] as $button) {
          if (strpos('lite-', $button) !== FALSE) {
            $enabled = TRUE;
          }
        }
      }
    }
    return $enabled;
  }

}

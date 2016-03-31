<?php

/**
 * @file
 * Contains \Drupal\lite\Plugin\CKEditorPlugin\lite.
 */

namespace Drupal\lite\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "lite" plugin.
 *
 * @CKEditorPlugin(
 *   id = "lite",
 *   label = @Translation("Lite"),
 *   module = "lite"
 * )
 */
class Lite extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    $path = '/libraries/lite';

    // Optionally use the Libraries module to determine the library path.
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('lite');
    }

    return $path . '/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $user = \Drupal::currentUser();

    return array(
      'lite' => array(
        'userId' => $user->id(),
        'userName' => $user->getDisplayName(),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = '/libraries/lite';

    // Optionally use the Libraries module to determine the library path.
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('lite');
    }

    return array(
      'lite-acceptall' => array(
        'label' => t('Accept all changes'),
        'image' => $path . '/icons/lite-acceptall.png',
      ),
      'lite-rejectall' => array(
        'label' => t('Reject all changes'),
        'image' => $path . '/icons/lite-rejectall.png',
      ),
      'lite-acceptone' => array(
        'label' => t('Accept change'),
        'image' => $path . '/icons/lite-acceptone.png',
      ),
      'lite-rejectone' => array(
        'label' => t('Reject change'),
        'image' => $path . '/icons/lite-rejectone.png',
      ),
      'lite-toggleshow' => array(
        'label' => t('Show/hide tracked changes'),
        'image' => $path . '/icons/lite-toggleshow.png',
      ),
      'lite-toggletracking' => array(
        'label' => t('Start/stop tracking changes'),
        'image' => $path . '/icons/lite-toggletracking.png',
      ),
    );
  }

}

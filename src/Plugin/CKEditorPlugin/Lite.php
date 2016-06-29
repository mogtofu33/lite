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
    $library = libraries_detect('lite');

    return $library['library path'] . '/plugin.js';
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
    $library = libraries_detect('lite');

    return array(
      'lite-acceptall' => array(
        'label' => t('Accept all changes'),
        'image' => $library['library path'] . '/icons/lite-acceptall.png',
      ),
      'lite-rejectall' => array(
        'label' => t('Reject all changes'),
        'image' => $library['library path'] . '/icons/lite-rejectall.png',
      ),
      'lite-acceptone' => array(
        'label' => t('Accept change'),
        'image' => $library['library path'] . '/icons/lite-acceptone.png',
      ),
      'lite-rejectone' => array(
        'label' => t('Reject change'),
        'image' => $library['library path'] . '/icons/lite-rejectone.png',
      ),
      'lite-toggleshow' => array(
        'label' => t('Show/hide tracked changes'),
        'image' => $library['library path'] . '/icons/lite-toggleshow.png',
      ),
      'lite-toggletracking' => array(
        'label' => t('Start/stop tracking changes'),
        'image' => $library['library path'] . '/icons/lite-toggletracking.png',
      ),
    );
  }

}

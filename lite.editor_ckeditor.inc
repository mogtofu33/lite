<?php

/**
 * @file
 * Editor CKEditor integration for the lite module.
 */

/**
 * Implements hook_editor_ckeditor_plugins().
 */
function lite_editor_ckeditor_plugins() {
  $plugins = array();

  // Only declare the CKEditor plugin if the LITE library is available.
  if ($library = libraries_detect('lite')) {
    $path = $library['library path'] . '/' . $library['path'];

    // Plugin for tracking changes within CKEditor.
    $plugins['lite'] = array(
      'path' => $path,
      'file' => 'plugin.js',
      'buttons' => array(
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
          'required_html' => array(array('tags' => array('ins'))),
        ),
      ),
    );
  }

  return $plugins;
}

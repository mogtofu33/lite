<?php

namespace Drupal\lite\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the "lite" plugin.
 *
 * @CKEditorPlugin(
 *   id = "lite",
 *   label = @Translation("Lite"),
 *   module = "lite"
 * )
 */
class Lite extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface {

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
  public function getDependencies(Editor $editor) {
    return [
      'drupallite',
    ];
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
  public function getConfig(Editor $editor) {
    $config = [];
    $user = \Drupal::currentUser();
    $settings = $editor->getSettings();
    $lite_settings = \Drupal::config('lite.settings');
    $tooltipTemplate = $lite_settings->get('tooltipTemplate');

    $config['lite'] = [
      'userId' => $user->id(),
      'userName' => $user->getDisplayName(),
      'tooltipTemplate' => $tooltipTemplate,
      'auto_start' => 1,
      'auto_show' => 1,
      'disable_new' => 1,
    ];

    if (!empty($settings['plugins']['lite']['auto_start'])) {
      $config['lite']['auto_start'] = $settings['plugins']['lite']['auto_start'];
    }
    else {
      // Force disable.
      $config['lite']['auto_start'] = 0;
    }
    if (!empty($settings['plugins']['lite']['auto_show'])) {
      $config['lite']['auto_show'] = $settings['plugins']['lite']['auto_show'];
    }
    else {
      // Force disable.
      $config['lite']['auto_show'] = 0;
    }
    if (!empty($settings['plugins']['lite']['disable_new'])) {
      $config['lite']['disable_new'] = $settings['plugins']['lite']['disable_new'];
    }
    else {
      // Force disable.
      $config['lite']['disable_new'] = 0;
    }

    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $library = libraries_detect('lite');

    return [
      'lite-acceptall' => [
        'label' => t('Accept all changes'),
        'image' => $library['library path'] . '/icons/lite-acceptall.png',
      ],
      'lite-rejectall' => [
        'label' => t('Reject all changes'),
        'image' => $library['library path'] . '/icons/lite-rejectall.png',
      ],
      'lite-acceptone' => [
        'label' => t('Accept change'),
        'image' => $library['library path'] . '/icons/lite-acceptone.png',
      ],
      'lite-rejectone' => [
        'label' => t('Reject change'),
        'image' => $library['library path'] . '/icons/lite-rejectone.png',
      ],
      'lite-toggleshow' => [
        'label' => t('Show/hide tracked changes'),
        'image' => $library['library path'] . '/icons/lite-toggleshow.png',
      ],
      'lite-toggletracking' => [
        'label' => t('Start/stop tracking changes'),
        'image' => $library['library path'] . '/icons/lite-toggletracking.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    // Defaults.
    $config = [
      'auto_start' => 1,
      'auto_show' => 1,
      'disable_new' => 1,
    ];
    $settings = $editor->getSettings();

    if (isset($settings['plugins']['lite'])) {
      $config = $settings['plugins']['lite'];
    }

    $form['auto_start'] = [
      '#title' => t('Enable tracking changes by default'),
      '#description' => t('Enable Lite tracking when the editor is loaded with this text format.'),
      '#type' => 'checkbox',
      '#default_value' => $config['auto_start'],
      '#attributes' => [
        'data-editor-lite' => 'auto_start',
      ],
    ];

    $form['auto_show'] = [
      '#title' => t('Enable show changes by default'),
      '#description' => t('enable Lite <em>show changes</em> when the editor is loaded with this text format.<br>If the <em>show changes</em> button is not in the toolbar, users will not be able to disable the show changes.'),
      '#type' => 'checkbox',
      '#default_value' => $config['auto_show'],
      '#states' => [
        'visible' => [
          ':input[data-editor-lite="auto_start"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['disable_new'] = [
      '#title' => t('Do not start tracking on <em>New</em> entity'),
      '#description' => t('Prevent users from becoming confused if their initial content does not show up after saving the entity without accepting any change.'),
      '#type' => 'checkbox',
      '#default_value' => $config['disable_new'],
      '#states' => [
        'visible' => [
          ':input[data-editor-lite="auto_start"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

}

<?php

namespace Drupal\lite\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;
use Drupal\workflows\WorkflowInterface;
use Drupal\workflows\Entity\Workflow;
use Drupal\content_moderation\Plugin\WorkflowType\ContentModeration;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\PermissionHandler;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the "lite" plugin.
 *
 * @CKEditorPlugin(
 *   id = "lite",
 *   label = @Translation("Lite"),
 *   module = "lite"
 * )
 */
class Lite extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface, ContainerFactoryPluginInterface {

  /**
   * The current user account service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The permission handler service.
   *
   * @var \Drupal\user\PermissionHandler
   */
  protected $permissionsHandler;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a \Drupal\ckeditor\Plugin\CKEditorPlugin\DrupalLite object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\user\PermissionHandler $permissions_handler
   *   The permissions handler service.
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The module handler.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $current_user, PermissionHandler $permissions_handler, ModuleHandler $module_handler, UrlGeneratorInterface $url_generator, EntityTypeManagerInterface $entity_manager) {
    $this->currentUser = $current_user;
    $this->permissionsHandler = $permissions_handler;
    $this->moduleHandler = $module_handler;
    $this->urlGenerator = $url_generator;
    $this->entityTypeManager = $entity_manager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('user.permissions'),
      $container->get('module_handler'),
      $container->get('url_generator'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return base_path() . 'libraries/lite/plugin.js';
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
  public function getConfig(Editor $editor) {
    $lite_settings = \Drupal::config('lite.settings');
    $settings = $editor->getSettings();

    $config['lite'] = [
      // Lite settings, see http://www.loopindex.com/lite/docs/
      'userId' => $this->currentUser->id(),
      'userName' => $this->currentUser->getDisplayName(),
      'tooltipTemplate' => $lite_settings->get('tooltipTemplate'),
      // 'tooltips' => FALSE,
      // Custom settings for this plugin.
      'permissions' => $this->getUserPermissions(),
      'extra_permissions' => $lite_settings->get('extra_permissions'),
      'options' => isset($settings['plugins']['lite']) ? $settings['plugins']['lite'] : [],
    ];

    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    // $library = libraries_detect('lite');
    // $libraryDiscovery = \Drupal::service('library.discovery');
    // $library = $libraryDiscovery->getLibrariesByExtension('lite'));
    // $library = $library['lite'];.
    $path = base_path() . 'libraries/lite';

    return [
      'lite-acceptall' => [
        'label' => t('Accept all changes'),
        'image' => $path . '/icons/lite-acceptall.png',
      ],
      'lite-rejectall' => [
        'label' => t('Reject all changes'),
        'image' => $path . '/icons/lite-rejectall.png',
      ],
      'lite-acceptone' => [
        'label' => t('Accept change'),
        'image' => $path . '/icons/lite-acceptone.png',
      ],
      'lite-rejectone' => [
        'label' => t('Reject change'),
        'image' => $path . '/icons/lite-rejectone.png',
      ],
      'lite-toggleshow' => [
        'label' => t('Show/hide tracked changes'),
        'image' => $path . '/icons/lite-toggleshow.png',
      ],
      'lite-toggletracking' => [
        'label' => t('Start/stop tracking changes'),
        'image' => $path . '/icons/lite-toggletracking.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();
    $config = [];

    if (isset($settings['plugins']['lite'])) {
      $config = $settings['plugins']['lite'];
    }

    if ($this->moduleHandler->moduleExists('help')) {
      $params = [
        ':help' => $this->urlGenerator->generateFromRoute('help.page', ['name' => 'lite']),
      ];
      $form['help'] = [
        '#markup' => $this->t('<br>See the <a href=":help">help</a> for more information on these options.', $params),
      ];
    }

    $form['auto_start'] = [
      '#title' => t('Enable tracking changes by default'),
      '#description' => t('Enable Lite tracking when the editor is loaded with this text format.'),
      '#type' => 'checkbox',
      '#default_value' => isset($config['auto_start']) ? $config['auto_start'] : 1,
      '#attributes' => [
        'data-editor-lite' => 'auto_start',
      ],
    ];

    $form['disable_new'] = [
      '#title' => t('Do not start tracking on <em>New</em> entity'),
      '#description' => t('Prevent users from becoming confused if their initial content does not show up after saving the entity without accepting any change.'),
      '#type' => 'checkbox',
      '#default_value' => isset($config['disable_new']) ? $config['disable_new'] : 0,
    ];

    if ($this->moduleHandler->moduleExists('content_moderation')) {
      $params = [
        ':url' => $this->urlGenerator->generateFromRoute('entity.workflow.collection'),
        ':url_lite' => $this->urlGenerator->generateFromRoute('lite.lite_settings_form'),
      ];
      $form['moderation'] = [
        '#title' => t('Enable content moderation support'),
        '#description' => $this->t('Extend Lite options by <a href=":url">Workflows</a> states for this text format. Can be extended using the <a href=":url_lite">permissions by states</a>.', $params),
        '#type' => 'checkbox',
        '#default_value' => isset($config['moderation']) ? $config['moderation'] : 0,
        '#attributes' => [
          'data-editor-lite-moderation' => 'enable',
        ],
      ];
      $form['moderation_options'] = [
        '#type' => 'container',
        '#states' => [
          'visible' => [
            ':input[data-editor-lite-moderation="enable"]' => ['checked' => TRUE],
          ],
        ],
      ];

      /* @var \Drupal\workflows\WorkflowInterface[] $workflows */
      $workflows = $this->entityTypeManager->getStorage('workflow')->loadMultiple();
      $options = array_map(function (WorkflowInterface $workflow) {
        return $workflow->label();
      }, array_filter($workflows, function (WorkflowInterface $workflow) {
        return $workflow->status() && $workflow->getTypePlugin() instanceof ContentModeration;
      }));

      if (count($options)) {
        foreach ($options as $worflow_id => $label) {

          $form['moderation_options'][$worflow_id] = [
            '#title' => t(':label', [':label' => $label]),
            '#type' => 'details',
          ];

          $form['moderation_options'][$worflow_id]['enable'] = [
            '#title' => t('Override options for this Workflow'),
            '#type' => 'checkbox',
            '#default_value' => isset($config['moderation_options'][$worflow_id]['enable']) ? $config['moderation_options'][$worflow_id]['enable'] : 0,
            '#attributes' => [
              'data-editor-lite-moderation' => $worflow_id,
            ],
          ];

          $workflow = Workflow::load($worflow_id);
          $states = $workflow->getStates();
          foreach ($states as $state) {
            $state_id = $state->id();
            if (isset($config['moderation_options'][$worflow_id][$state_id]['auto_start'])) {
              $default = $config['moderation_options'][$worflow_id][$state_id]['auto_start'];
            }
            else {
              $default = 0;
            }
            $form['moderation_options'][$worflow_id][$state_id]['auto_start'] = [
              '#title' => t('%state: enable tracking changes by default', ['%state' => $state->label()]),
              '#type' => 'checkbox',
              '#default_value' => $default,
              '#states' => [
                'visible' => [
                  ':input[data-editor-lite-moderation="' . $worflow_id . '"]' => ['checked' => TRUE],
                ],
              ],
            ];

          }
          // Open or close if we have this workflow enabled.
          if (isset($config['moderation_options'][$worflow_id]['enable']) && $config['moderation_options'][$worflow_id]['enable'] == 1) {
            $form['moderation_options'][$worflow_id]['#open'] = TRUE;
          }
        }
      }
    }

    return $form;
  }

  /**
   * Return user permissions filtered to this module.
   *
   * @return array
   *   List of lite permissions without spaces.
   */
  private function getUserPermissions() {
    $permissions = $user_permissions = [];

    $roles = $this->currentUser->getRoles();

    // Specific Admin case, because Drupal return an empty array we need all
    // permissions if one of the role is admin.
    foreach ($roles as $role) {
      $role = Role::load($role);
      if ($role->isAdmin()) {
        $user_permissions = array_keys($this->permissionsHandler->getPermissions());
        continue;
      }
    }

    // For a regular user we load permissions.
    if (!count($user_permissions)) {
      $all_user_permissions = user_role_permissions($roles);
      if (count($all_user_permissions)) {
        $user_permissions = array_merge($all_user_permissions, $all_user_permissions);
        $user_permissions = end($user_permissions);
      }
    }

    // We filter permissions to get only lite related to be used by our plugin.
    foreach ($user_permissions as $permission) {
      if (strpos($permission, 'lite') !== FALSE) {
        $permissions[] = str_replace(['lite ', ' '], ['', '_'], $permission);
      }
    }
    return $permissions;
  }

}

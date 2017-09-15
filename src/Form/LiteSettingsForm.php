<?php

namespace Drupal\lite\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LiteSettingsForm.
 *
 * @package Drupal\lite\Form
 */
class LiteSettingsForm extends ConfigFormBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The module handler.
   */
  public function __construct(ConfigFactoryInterface $config_factory, UrlGeneratorInterface $url_generator, ModuleHandler $module_handler) {
    parent::__construct($config_factory);

    $this->urlGenerator = $url_generator;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('url_generator'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['lite.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lite_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('lite.settings');

    $options = [
      '' => $this->t('- Select -'),
      'permissions_by_formats' => $this->t('Add permissions by text formats'),
    ];
    if ($this->moduleHandler->moduleExists('content_moderation')) {
      $options['permissions_by_states'] = $this->t('Add permissions by Workflow states');
    }
    $params = [
      ':url' => $this->urlGenerator->generateFromRoute('user.admin_permissions',
      [],
      ['fragment' => 'module-lite']),
    ];
    $form['extra_permissions'] = [
      '#type' => 'select',
      '#title' => $this->t('Add more permissions'),
      '#description' => $this->t('Before changing or removing extra permission here, be sure to uncheck existing permissions on the <a href=":url">permissions</a>  administration.<br>This option bring more complexity to the configuration of this module.', $params),
      '#options' => $options,
      '#default_value' => $config->get('extra_permissions'),
    ];

    $form['tooltipTemplate'] = [
      '#title' => $this->t('Tooltip template'),
      '#type' => 'textarea',
      '#description' => $this->t('Allow a custom template to use with Lite plugin, allow some HTML tags.<br>Available variables:<br><small>
      <strong>%a</strong>  The action, "added" or "deleted"<br>
      <strong>%t</strong>  Timestamp of the first edit action in this change span (e.g. "now", "3 minutes ago", "August 15 1972")<br>
      <strong>%u</strong>  the name of the user who made the change<br>
      <strong>%dd</strong> double digit date of change, e.g. 02<br>
      <strong>%d</strong>  date of change, e.g. 2<br>
      <strong>%mm</strong> double digit month of change, e.g. 09<br>
      <strong>%m</strong>  month of change, e.g. 9<br>
      <strong>%yy</strong> double digit year of change, e.g. 11<br>
      <strong>%y</strong>  full month of change, e.g. 2011<br>
      <strong>%nn</strong> double digit minutes of change, e.g. 09<br>
      <strong>%n</strong>  minutes of change, e.g. 9<br>
      <strong>%hh</strong> double digit hour of change, e.g. 05<br>
      <strong>%h</strong>  hour of change, e.g. 5<br>
    	</small>'),
      '#default_value' => $config->get('tooltipTemplate'),
    ];

    $form['tLocale'] = [
      '#title' => $this->t('Tooltip %t format'),
      '#type' => 'textfield',
      '#description' => $this->t('If using %t wildcard on tooltip template, when displaying full date you can localize date format.<br>See jQuery<a href=":url"> Datepicker formatDate</a> for available formats.', [':url' => 'http://api.jqueryui.com/datepicker/#utility-formatDate']),
      '#default_value' => $config->get('tLocale'),
    ];

    $form['debug'] = [
      '#title' => $this->t('Enable debug'),
      '#description' => $this->t('Display information on the javascript console of the browser when using the Wysiwyg with Lite to ease configuration.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('debug'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('lite.settings')
      ->set('tooltipTemplate', $form_state->getValue('tooltipTemplate'))
      ->set('tLocale', $form_state->getValue('tLocale'))
      ->set('extra_permissions', $form_state->getValue('extra_permissions'))
      ->set('debug', $form_state->getValue('debug'))
      ->save();
  }

}

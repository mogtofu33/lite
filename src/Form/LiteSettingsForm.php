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

    $form['tooltipTemplate'] = [
      '#title' => $this->t('Tooltip template'),
      '#type' => 'textarea',
      '#description' => $this->t('Allow a custom template to use with Opentip library used by Lite plugin.<br>Available variables:<br><small>
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

    $params = [
      ':url' => $this->urlGenerator->generateFromRoute('user.admin_permissions',
      [],
      ['fragment' => 'module-lite']),
    ];
    $form['permissions_by_formats'] = [
      '#title' => $this->t('Enable permissions by text formats'),
      '#description' => $this->t('This option create the <em>toggle</em> and <em>resolve</em> <a href=":url">permissions</a> for each text format with the Lite filter enabled.', $params),
      '#type' => 'checkbox',
      '#default_value' => $config->get('permissions_by_formats'),
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
      ->set('permissions_by_formats', $form_state->getValue('permissions_by_formats'))
      ->save();
  }

}

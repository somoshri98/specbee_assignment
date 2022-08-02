<?php

namespace Drupal\dynamic_date_time\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\dynamic_date_time\DateTimeService;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Block which renders location and time based on the loctaion.
 *
 * @Block(
 *   id = "locationtime_block",
 *   admin_label = @Translation("Location and Time Block"),
 *   category = @Translation("Dynamic Date & Time")
 * )
 */
class LocationTimeBlock extends BlockBase implements ContainerFactoryPluginInterface, TrustedCallbackInterface {
  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;
  /**
   * The date time service.
   *
   * @var \Drupal\dynamic_date_time\DateTimeService
   */
  protected $datetime;
  /**
   * Constructs a new WorkspaceSwitcherBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   * @param \Drupal\dynamic_date_time\DateTimeService $datetime
   *   The Date Time service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactory $config, DateTimeService $datetime) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config;
    $this->datetime = $datetime;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('dynamic_date_time.date_time'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];
    $country = $this->config->getEditable('location.admin_settings')->get('country');
    $city = $this->config->getEditable('location.admin_settings')->get('city');
    $build['static'] = [
      '#theme' => 'datetime_show',
      '#city' => $city,
      '#country' => $country,
      '#datetime' => $this->datetime->getDateTime(),
    ];
    // $build['#attached']['library'][] = 'dynamic_date_time/dynamic_date_time';
    // $build['#attached']['drupalSettings']['dateTime'] = $datetime;
    $build['dynamic'] = [
      '#lazy_builder' => [
        static::class . '::lazyBuilder',
        [],
        // [$this->datetime]
      ],
      '#create_placeholder' => TRUE,
    ];
    return $build;
  }
  /*
   * LazyBuilder code for loading time
   */

  public static function lazyBuilder() {
    $build = [];
    $time = \Drupal::service('dynamic_date_time.date_time')->getDateTime();
    $build = [
      '#markup' => $time,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return [
      'lazyBuilder',
    ];
  }

}

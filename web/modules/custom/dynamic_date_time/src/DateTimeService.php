<?php

namespace Drupal\dynamic_date_time;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatter;


class DateTimeService {
  public $timestamp;
  public $timeformater;
  public $config;

  /**
   * Custom Redirect constructor.
   */
  public function __construct(TimeInterface $timestamp, DateFormatter $timeformater, ConfigFactory $config) {
    $this->timestamp = $timestamp;
    $this->timeformater = $timeformater;
    $this->config = $config;
  }

  /**
   * Fetch Date & Time based on selected Timezone.
   */
  public function getDateTime() {
    $config = $this->config->getEditable('location.admin_settings');
    $timezone = $config->get('timezone');
    $currenttimestamp = $this->timestamp->getRequestTime();
    $time = $this->timeformater->format(time(), $type = 'custom', $format = 'dS M Y - h : i a', $timezone);
    return $time;
  }
}

<?php

namespace Drupal\datetime\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Default' formatter for 'datetime' fields.
 *
 * @FieldFormatter(
 *   id = "datetime_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateTimeDefaultFormatter extends DateTimeFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'format_type' => 'medium',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  protected function formatDate($date) {
    $format_type = $this->getSetting('format_type');
    $timezone = $this->getSetting('timezone_override') ?: $date->getTimezone()->getName();
    return $this->dateFormatter->format($date->getTimestamp(), $format_type, '', $timezone != '' ? $timezone : NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $time = new DrupalDateTime();
    $format_types = $this->dateFormatStorage->loadMultiple();
    $options = [];
    foreach ($format_types as $type => $type_info) {
      $format = $this->dateFormatter->format($time->getTimestamp(), $type);
      $options[$type] = $type_info->label() . ' (' . $format . ')';
    }

    $form['format_type'] = array(
      '#type' => 'select',
      '#title' => t('Date format'),
      '#description' => t("Choose a format for displaying the date. Be sure to set a format appropriate for the field, i.e. omitting time for a field that only has a date."),
      '#options' => $options,
      '#default_value' => $this->getSetting('format_type'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $date = new DrupalDateTime();
    $summary[] = t('Format: @display', array('@display' => $this->formatDate($date, $this->getFormatSettings())));

    return $summary;
  }

}

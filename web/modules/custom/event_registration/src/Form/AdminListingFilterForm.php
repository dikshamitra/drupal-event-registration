<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class AdminListingFilterForm extends FormBase {

  public function getFormId() {
    return 'admin_listing_filter_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    // Event Dates
    $dates = Database::getConnection()
      ->select('event_config', 'e')
      ->fields('e', ['event_date'])
      ->distinct()
      ->execute()
      ->fetchCol();

    $date_options = array_combine($dates, $dates);

    $form['event_date'] = [
      '#type' => 'select',
      '#title' => 'Event Date',
      '#options' => ['' => '- All -'] + $date_options,
      '#ajax' => [
        'callback' => '::updateEventNames',
        'wrapper' => 'event-name-wrapper',
      ],
    ];

    // Event Name wrapper
    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $event_names = [];

    if ($form_state->getValue('event_date')) {
      $query = Database::getConnection()->select('event_config', 'e');
      $query->fields('e', ['id', 'event_name']);
      $query->condition('event_date', $form_state->getValue('event_date'));
      $event_names = $query->execute()->fetchAllKeyed();
    }

    $form['event_name_wrapper']['event_id'] = [
      '#type' => 'select',
      '#title' => 'Event Name',
      '#options' => ['' => '- All -'] + $event_names,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Filter',
    ];

    return $form;
  }

  public function updateEventNames(array &$form, FormStateInterface $form_state) {
    return $form['event_name_wrapper'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }
}

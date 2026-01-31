<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class EventConfigForm extends FormBase {

  public function getFormId() {
    return 'event_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    // Event Name
    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
    ];

    // Category
    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [
        'technical' => 'Technical',
        'cultural' => 'Cultural',
        'sports' => 'Sports',
      ],
      '#required' => TRUE,
    ];

    // Event Date
    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    // Registration Start Date
    $form['reg_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration Start Date'),
      '#required' => TRUE,
    ];

    // Registration End Date
    $form['reg_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration End Date'),
      '#required' => TRUE,
    ];

    // Submit
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Event'),
    ];

    return $form;
  }

  /**
   * Validation
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $start = strtotime($form_state->getValue('reg_start'));
    $end = strtotime($form_state->getValue('reg_end'));

    if ($start >= $end) {
      $form_state->setErrorByName(
        'reg_end',
        $this->t('Registration end date must be after start date.')
      );
    }
  }

  /**
   * Submit
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    Database::getConnection()->insert('event_config')
      ->fields([
        'event_name' => $form_state->getValue('event_name'),
        'category' => $form_state->getValue('category'),
        'event_date' => $form_state->getValue('event_date'),
        'reg_start' => $form_state->getValue('reg_start'),
        'reg_end' => $form_state->getValue('reg_end'),
      ])
      ->execute();

    $this->messenger()->addStatus($this->t('Event saved successfully.'));
  }
}

<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\event_registration\Repository\EventRepository;
use Drupal\Core\Database\Connection;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class EventRegistrationForm extends FormBase {

  protected $eventRepository;
  protected $database;
  protected $time;
  protected $mailManager;
  protected $configFactory;

  public function __construct(
    EventRepository $eventRepository,
    Connection $database,
    TimeInterface $time,
    MailManagerInterface $mailManager,
    ConfigFactoryInterface $configFactory
  ) {
    $this->eventRepository = $eventRepository;
    $this->database = $database;
    $this->time = $time;
    $this->mailManager = $mailManager;
    $this->configFactory = $configFactory;
  }

  public static function create(ContainerInterface $container) {
  return new static(
    $container->get('event_registration.repository'),
    $container->get('database'),
    $container->get('datetime.time'),
    $container->get('plugin.manager.mail'),
    $container->get('config.factory')
  );
}


  public function getFormId() {
    return 'event_registration_form';
  }

  /* ================= BUILD FORM ================= */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $selected_category = $form_state->getValue('category');
    $selected_date = $form_state->getValue('event_date');

    /* -------- CATEGORY -------- */
    $categories = array_combine(
      $this->eventRepository->getCategories(),
      $this->eventRepository->getCategories()
    );

    $form['category'] = [
      '#type' => 'select',
      '#title' => 'Category',
      '#options' => ['' => '- Select -'] + $categories,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventDates',
        'wrapper' => 'event-date-wrapper',
      ],
    ];

    /* -------- EVENT DATE -------- */
    $dates = [];
    if ($selected_category) {
      $dates = array_combine(
        $this->eventRepository->getEventDatesByCategory($selected_category),
        $this->eventRepository->getEventDatesByCategory($selected_category)
      );
    }

    $form['event_date_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-date-wrapper'],
    ];

    $form['event_date_wrapper']['event_date'] = [
      '#type' => 'select',
      '#title' => 'Event Date',
      '#options' => ['' => '- Select -'] + $dates,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventNames',
        'wrapper' => 'event-name-wrapper',
      ],
    ];

    /* -------- EVENT NAME -------- */
    $events = [];
    if ($selected_category && $selected_date) {
      $events = $this->eventRepository->getEventNames($selected_category, $selected_date);
    }

    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $form['event_name_wrapper']['event_id'] = [
      '#type' => 'select',
      '#title' => 'Event Name',
      '#options' => ['' => '- Select -'] + $events,
      '#required' => TRUE,
    ];

    /* -------- USER FIELDS -------- */
    $form['full_name'] = ['#type' => 'textfield', '#title' => 'Full Name', '#required' => TRUE];
    $form['email'] = ['#type' => 'email', '#title' => 'Email', '#required' => TRUE];
    $form['college'] = ['#type' => 'textfield', '#title' => 'College', '#required' => TRUE];
    $form['department'] = ['#type' => 'textfield', '#title' => 'Department', '#required' => TRUE];

    $form['submit'] = ['#type' => 'submit', '#value' => 'Register'];

    return $form;
  }

  /* ================= VALIDATION ================= */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $event_id = $form_state->getValue('event_id');

    // Registration window validation
    if ($event_id) {
      $event = $this->eventRepository->getEventById($event_id);
      $today = date('Y-m-d');

      if (!$event || $today < $event['reg_start'] || $today > $event['reg_end']) {
        $form_state->setErrorByName('event_id', 'Registration for this event is currently closed.');
      }
    }

    // Duplicate check
    $exists = $this->database->select('event_registration', 'r')
      ->condition('email', $form_state->getValue('email'))
      ->condition('event_id', $event_id)
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($exists) {
      $form_state->setErrorByName('email', 'You have already registered for this event.');
    }

    // Special character validation
    $pattern = '/^[A-Za-z ]+$/';
    foreach (['full_name', 'college', 'department'] as $field) {
      if (!preg_match($pattern, $form_state->getValue($field))) {
        $form_state->setErrorByName($field, 'Special characters are not allowed.');
      }
    }
  }

  /* ================= AJAX ================= */
  public function updateEventDates(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
    return $form['event_date_wrapper'];
  }

  public function updateEventNames(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
    return $form['event_name_wrapper'];
  }

  /* ================= SUBMIT ================= */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  // Save registration
  $this->database->insert('event_registration')->fields([
    'full_name' => $form_state->getValue('full_name'),
    'email' => $form_state->getValue('email'),
    'college' => $form_state->getValue('college'),
    'department' => $form_state->getValue('department'),
    'event_id' => $form_state->getValue('event_id'),
    'created' => $this->time->getRequestTime(),
  ])->execute();

  // Load event safely
  $event = $this->database->select('event_config', 'e')
    ->fields('e')
    ->condition('id', $form_state->getValue('event_id'))
    ->execute()
    ->fetchObject();

  if ($event) {
    $mail_body =
      "Name: {$form_state->getValue('full_name')}\n" .
      "Category: {$event->category}\n" .
      "Event Name: {$event->event_name}\n" .
      "Event Date: {$event->event_date}";

    try {
      // User mail
      $this->mailManager->mail(
        'event_registration',
        'user_registration',
        $form_state->getValue('email'),
        \Drupal::languageManager()->getDefaultLanguage()->getId(),
        ['body' => $mail_body]
      );

      // Admin mail (optional)
      $config = $this->configFactory->get('event_registration.settings');
      if ($config->get('enable_admin_notification')) {
        $this->mailManager->mail(
          'event_registration',
          'admin_notification',
          $config->get('admin_email'),
          \Drupal::languageManager()->getDefaultLanguage()->getId(),
          ['body' => $mail_body]
        );
      }
    }
    catch (\Exception $e) {
      $this->messenger()->addWarning('Registration saved, email could not be sent.');
    }
  }

  $this->messenger()->addStatus('Registration successful!');
}

}

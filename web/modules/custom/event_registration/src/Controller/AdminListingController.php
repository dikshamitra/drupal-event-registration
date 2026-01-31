<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Component\Datetime\TimeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;

class AdminListingController extends ControllerBase {

  protected $database;
  protected $time;

  public function __construct(Connection $database, TimeInterface $time) {
    $this->database = $database;
    $this->time = $time;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('datetime.time')
    );
  }

  /* ================= LISTING PAGE ================= */
  public function listing() {

    // Count participants
    $total = $this->database->select('event_registration', 'er')
      ->countQuery()
      ->execute()
      ->fetchField();

    // Fetch registrations
    $query = $this->database->select('event_registration', 'er');
    $query->join('event_config', 'ec', 'er.event_id = ec.id');

    $query->fields('er', [
      'full_name',
      'email',
      'college',
      'department',
      'created',
    ]);

    $query->fields('ec', [
      'event_date',
    ]);

    $results = $query->execute()->fetchAll();

    $rows = [];
    foreach ($results as $row) {
      $rows[] = [
        $row->full_name,
        $row->email,
        $row->event_date,
        $row->college,
        $row->department,
        date('d-m-Y', $row->created ?? $this->time->getRequestTime()),
      ];
    }

    // Export button
    $export_link = [
      '#type' => 'link',
      '#title' => 'Export CSV',
      '#url' => Url::fromRoute('event_registration.export_csv'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
        'style' => 'margin-bottom:15px; display:inline-block;',
      ],
    ];

    return [
      '#type' => 'container',

      'export' => $export_link,

      'count' => [
        '#markup' => '<h3>Total Participants: ' . $total . '</h3>',
      ],

      'table' => [
        '#type' => 'table',
        '#header' => [
          'Name',
          'Email',
          'Event Date',
          'College',
          'Department',
          'Submission Date',
        ],
        '#rows' => $rows,
        '#empty' => 'No registrations found.',
      ],
    ];
  }

  /* ================= CSV EXPORT ================= */
  public function exportCsv() {

    $query = $this->database->select('event_registration', 'er');
    $query->join('event_config', 'ec', 'er.event_id = ec.id');

    $query->fields('er', [
      'full_name',
      'email',
      'college',
      'department',
      'created',
    ]);

    $query->fields('ec', [
      'event_date',
      'event_name',
      'category',
    ]);

    $results = $query->execute()->fetchAll();

    $csv = "Name,Email,College,Department,Event Date,Event Name,Category,Submitted On\n";

    foreach ($results as $row) {
      $csv .= '"' . implode('","', [
        $row->full_name,
        $row->email,
        $row->college,
        $row->department,
        $row->event_date,
        $row->event_name,
        $row->category,
        date('d-m-Y', $row->created ?? $this->time->getRequestTime()),
      ]) . '"' . "\n";
    }

    $response = new Response($csv);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set(
      'Content-Disposition',
      'attachment; filename="event_registrations.csv"'
    );

    return $response;
  }

}

<?php

namespace Drupal\event_registration\Repository;

use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles all event-related database queries.
 */
class EventRepository {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructor.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Get distinct event categories.
   */
  public function getCategories(): array {
    $query = $this->database->select('event_config', 'e')
      ->fields('e', ['category'])
      ->distinct();

    return $query->execute()->fetchCol();
  }

  /**
   * Get event dates by category.
   */
  public function getEventDatesByCategory(string $category): array {
    $query = $this->database->select('event_config', 'e')
      ->fields('e', ['event_date'])
      ->condition('category', $category)
      ->distinct()
      ->orderBy('event_date', 'ASC');

    return $query->execute()->fetchCol();
  }

  /**
   * Get event names by category and date.
   *
   * Returns: [event_id => event_name]
   */
  public function getEventNames(string $category, string $event_date): array {
    $query = $this->database->select('event_config', 'e')
      ->fields('e', ['id', 'event_name'])
      ->condition('category', $category)
      ->condition('event_date', $event_date)
      ->orderBy('event_name', 'ASC');

    return $query->execute()->fetchAllKeyed();
  }

  /**
   * Check duplicate registration (Email + Event Date).
   */
  public function isDuplicateRegistration(string $email, string $event_date): bool {
    $query = $this->database->select('event_registration', 'r')
      ->join('event_config', 'e', 'r.event_id = e.id')
      ->condition('r.email', $email)
      ->condition('e.event_date', $event_date)
      ->countQuery();

    return (bool) $query->execute()->fetchField();
  }

  public function getEventById($id) {
  return \Drupal::database()->select('event_config', 'e')
    ->fields('e')
    ->condition('id', $id)
    ->execute()
    ->fetchAssoc();
}


}

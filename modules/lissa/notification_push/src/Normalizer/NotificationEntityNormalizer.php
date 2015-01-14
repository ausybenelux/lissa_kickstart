<?php

/**
 * @file
 * Contains \Drupal\notification_push\Normalizer\NotificationEntityNormalizer.
 */

namespace Drupal\notification_push\Normalizer;

use Drupal\ext\Normalizer\ContentEntityNormalizer;


/**
 * Converts notification entities to a serializable data structure.
 *
 * Adds the following serialize context options:
 * - excluded_fields: an array of field names to excluded.
 * - merge_data: an array of data to merge with the serialized data.
 */
class NotificationEntityNormalizer extends ContentEntityNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\notification_entity\Entity\NotificationEntity';

  /**
   * Implements \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize()
   */
  public function normalize($entity, $format = NULL, array $context = array()) {
    $context['merge_data']['api_meta']['event_uuid'] = $entity->getHost()->uuid();
    $context['merge_data']['api_meta']['type'] = 'create';
    $context['excluded_fields'][] = 'host_id';
    // Allow other modules to alter the pushed data.
    \Drupal::moduleHandler()->alter('notification_push_context', $context, $entity);
    return parent::normalize($entity, $format, $context);
  }
}

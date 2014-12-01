<?php

/**
 * @file
 * Contains \Drupal\ext\Normalizer\NotificationEntityNormalizer.
 */

namespace Drupal\ext\Normalizer;

use Drupal\ext\Normalizer\ContentEntityNormalizer;


/**
 * Converts the Drupal entity object structure to a EXT array structure.
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
    $context['excluded_fields'][] = 'host_id';
    return parent::normalize($entity, $format, $context);
  }
}

<?php

/**
 * @file
 * Contains \Drupal\ext\Normalizer\ContentEntityNormalizer.
 */

namespace Drupal\ext\Normalizer;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\rest\LinkManager\LinkManagerInterface;

/**
 * Converts the Drupal entity object structure to a EXT array structure.
 *
 * Adds the following serialize context options:
 * - excluded_fields: an array of field names to excluded.
 * - merge_data: an array of data to merge with the serialized data.
 */
class ContentEntityNormalizer extends NormalizerBase {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\Core\Entity\ContentEntityInterface';

  /**
   * The hypermedia link manager.
   *
   * @var \Drupal\rest\LinkManager\LinkManagerInterface
   */
  protected $linkManager;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;


  /**
   * Constructs an ContentEntityNormalizer object.
   *
   * @param \Drupal\rest\LinkManager\LinkManagerInterface $link_manager
   *   The hypermedia link manager.
   */
  public function __construct(LinkManagerInterface $link_manager, EntityManagerInterface $entity_manager, ModuleHandlerInterface $module_handler) {
    $this->linkManager = $link_manager;
    $this->entityManager = $entity_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * Implements \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize()
   */
  public function normalize($entity, $format = NULL, array $context = array()) {
    $normalized = array();
    // If the fields to use were specified, only output those field values.
    // Otherwise, output all field values except internal ID.
    if (isset($context['included_fields'])) {
      $fields = array();
      foreach ($context['included_fields'] as $field_name) {
        $fields[] = $entity->get($field_name);
      }
    }
    else {
      $fields = $entity->getFields();
    }
    // Ignore the entity ID and revision ID.
    $exclude = array(
      $entity->getEntityType()->getKey('id'),
      $entity->getEntityType()->getKey('revision'),
      'uid',
      'revision_uid',
    );
    // Ignore fields based on context.
    if (isset($context['excluded_fields'])) {
      $exclude = array_merge($exclude, $context['excluded_fields']);
    }
    foreach ($fields as $field) {
      if (in_array($field->getFieldDefinition()->getName(), $exclude)) {
        continue;
      }

      $normalized_property = $this->serializer->normalize($field, $format, $context);
      $normalized = NestedArray::mergeDeep($normalized, $normalized_property);
    }

    if (isset($context['merge_data'])) {
      $normalized = NestedArray::mergeDeep($normalized, $context['merge_data']);
    }

    return $normalized;
  }

  /**
   * Denormalizes data back into an object of the given class
   *
   * @param mixed $data data to restore
   * @param string $class the expected class to instantiate
   * @param string $format format the given data was extracted from
   * @param array $context options available to the denormalizer
   *
   * @return object
   */
  public function denormalize($data, $class, $format = NULL, array $context = array()) {
    // TODO: Implement denormalize() method.
  }

  /**
   * Constructs the entity URI.
   *
   * @param $entity
   *   The entity.
   *
   * @return string
   *   The entity URI.
   */
  protected function getEntityUri($entity) {
    return $entity->url('canonical', array('absolute' => TRUE));
  }
}

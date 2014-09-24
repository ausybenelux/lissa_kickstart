<?php

/**
 * @file
 * Entity export module drush integration.
 */


use Drupal\Core\Entity\Entity;


/**
 * Implements hook_drush_command().
 */
function entity_export_drush_command() {
  $items = array();

  $description = "Export entities in JSON." . "\n";

  $items['export-entity'] = array(
    'description' => $description,
    'arguments' => array(
      'type' => 'Entity type.',
    ),
    'options' => array(
      'bundle' => 'Entity bundle.',
      'ids' => 'One or more entity IDs.',
    ),
    'examples' => array(
      'drush entity-export node > nodes.json' => 'Export all nodes to a nodes.json file.',
      'drush entity-export node --bundle=article' => 'Export all nodes of type article.',
      'drush entity-export node --ids=8,9' => 'Export the nodes with node id 8 and 9.',
    ),
    'aliases' => array('exe'),
  );

  $description = "Import entities from a JSON file." . "\n";

  $items['import-entity'] = array(
    'description' => $description,
    'arguments' => array(
      'type' => 'Entity type.',
      'file' => 'Filepath of the file containing the JSON data.'
    ),
    'examples' => array(
      'drush entity-import node /path/to/nodes.json' => 'Import all nodes.',
    ),
    'aliases' => array('ime'),
  );

  return $items;
}


/**
 * Drush command callback.
 */
function drush_entity_export_export_entity($type = NULL) {

  if (!empty($type) && \Drupal::entityManager()->getDefinition($type)) {
    $bundle = drush_get_option('bundle');
    $ids = drush_get_option_list('ids');

    if (!empty($bundle)) {
      $filter = array('bundle' => $bundle);
      $entities = entity_load_multiple_by_properties($type, $filter);
    }
    else {
      // If no IDs are specified explicitely let entity_load_multiple load all
      // entities by settings IDs to NULL.
      if (empty($ids)) {
        $ids = NULL;
      }
      $entities = entity_load_multiple($type, $ids, TRUE);
    }

    // Normalize the entity objects.
    if (!empty($entities)) {
      $serializer = \Drupal::service('serializer');
      $output = $serializer->serialize($entities, 'json');
      drush_print($output);
    }
  }
}

/**
 * Drush command callback.
 */
function drush_entity_export_import_entity($type = NULL, $filepath = NULL) {
  $definition = \Drupal::entityManager()->getDefinition($type);

  if ($definition && file_exists($filepath)) {
    $contents = @file_get_contents($filepath);

    if (!empty($contents)) {
      $serializer = \Drupal::service('serializer');
      $data = $serializer->decode($contents, 'json');

      if (is_array($data)) {
        foreach ($data as $entity_values) {

          // The entity denormalizer assumes value instead of target_id for the
          // bundle property.
          $keys = $definition->getKeys();
          if ($keys['bundle'] && isset($entity_values[$keys['bundle']][0]['target_id'])) {
            $entity_values[$keys['bundle']][0]['value'] = $entity_values[$keys['bundle']][0]['target_id'];
          }
          $entity = $serializer->denormalize($entity_values, 'Drupal\Core\Entity\Entity', NULL, array('entity_type' => $type));
          if ($entity) {
            // Delete existing items so we can update them later.
            $uuid = $entity->uuid();
            if ($existing = \Drupal::entityManager()->loadEntityByUuid($type, $uuid)) {
              $existing->delete();
            }

            $entity->save();
            drush_print(t('Created entity: !label', array('!label' => $entity->label())));
          }
          else {
            drush_set_error(t('Failed creating entity'));
          }
        }
      }
    }
  }
}

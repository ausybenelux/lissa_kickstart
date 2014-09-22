<?php

/**
 * @file
 * Entity export module drush integration.
 */


use Drupal\Component\Serialization\Yaml;

/**
 * Implements hook_drush_command().
 */
function entity_export_drush_command() {
  $items = array();

  $description = "Export entities in a given format." . "\n";

  $items['export-entity'] = array(
    'description' => $description,
    'arguments' => array(
      'type' => 'Entity type.',
    ),
    'options' => array(
      'bundle' => 'Entity bundle.',
      'ids' => 'One or more entity IDs.',
      'format' => 'Format to print the data in, defaults to YAML',
    ),
    'examples' => array(
      'drush entity-export node' => 'Export all nodes.',
      'drush entity-export node --bundle=article' => 'Export all nodes of type article.',
      'drush entity-export node --ids=8,9' => 'Export the nodes with node id 8 and 9.',
    ),
    'aliases' => array('exe'),
  );

  $description = "Import entities in YAMLt." . "\n";

  $items['import-entity'] = array(
    'description' => $description,
    'arguments' => array(
      'type' => 'Entity type.',
      'file' => 'Filepath of the file containing the YAML data.'
    ),
    'examples' => array(
      'drush entity-import node /path/to/nodes.yml' => 'Import all nodes.',
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
      $result = array();
      foreach ($entities as $id => $entity) {
        $result[$id] = $entity->toArray();
      }
      drush_print_format($result, 'yaml');
    }
  }
}

/**
 * Drush command callback.
 */
function drush_entity_export_import_entity($type = NULL, $filepath = NULL) {

  if (!empty($type) && \Drupal::entityManager()->getDefinition($type)) {
    $contents = @file_get_contents($filepath);

    if (!empty($contents)) {
      $data = Yaml::decode($contents);;
      drush_print(var_export($data));
    }
  }
}

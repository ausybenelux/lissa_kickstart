<?php

/**
 * @file
 * LISSA deploy module drush integration.
 */


use Drupal\lissa_deploy\Config\StorageComparer;
use Drupal\Core\Config\ConfigImporter;
use Drupal\Core\Config\FileStorage;
use Symfony\Component\Yaml\Parser;


/**
 * Implements hook_drush_command().
 */
function lissa_deploy_drush_command() {
  $items = array();

  $description = "Sync exported configurations." . "\n";

  $items['config-sync'] = array(
    'description' => $description,
    'arguments' => array(
      'label' => "A config directory label (i.e. a key in \$config_directories array in settings.php). Defaults to 'staging'",
    ),
    'options' => array(
      'preview' => array(
        'description' => 'Format for displaying proposed changes. Recognized values: list, diff. Defaults to list',
        'example-value' => 'list',
      ),
      'source' => 'An arbitrary directory that holds the configuration files. An alternative to label argument',
    ),
    'core' => array('8+'),
    'aliases' => array('csy'),
  );

  return $items;
}

function drush_lissa_deploy_config_sync_validate() {
  if ($source = drush_get_option('source')) {
    if (!file_exists($source)) {
      return drush_set_error('config_sync_target', 'The source directory does not exist.');
    }
    if (!is_dir($source)) {
      return drush_set_error('config_sync_target', 'The source is not a directory.');
    }
  }
}


/**
 * Drush command callback.
 */
/**
 * Command callback. Sync from specified config directory (defaults to staging).
 */
function drush_lissa_deploy_config_sync($source = NULL) {
  global $config_directories;

  if ($target = drush_get_option('source')) {
    $source_dir = $target;
  }
  else {
    $choices = drush_map_assoc(array_keys($config_directories));
    unset($choices[CONFIG_ACTIVE_DIRECTORY]);
    if (!isset($source) && count($choices) >= 2) {
      $source= drush_choice($choices, 'Choose a source.');
      if (empty($source)) {
        return drush_user_abort();
      }
    }
    elseif (!isset($source)) {
      $source = CONFIG_STAGING_DIRECTORY;
    }
    $source_dir = config_get_config_directory($source);
  }

  // Retrieve a list of differences between the active and source configuration (if any).
  $active_storage = Drupal::service('config.storage');
  $source_storage = new FileStorage($source_dir);
  $config_comparer = new StorageComparer($source_storage, $active_storage, Drupal::service('config.manager'));
  if (!$config_comparer->createChangelist()->hasChanges()) {
    return drush_log(dt('There are no changes to import.'), 'ok');
  }

  if (drush_get_option('preview', 'list') == 'list') {
    $change_list = array();
    foreach ($config_comparer->getAllCollectionNames() as $collection) {
      $change_list[$collection] = $config_comparer->getChangelist(NULL, $collection);
    }
    _drush_print_config_changes_table($change_list);
  }
  else {
    $destination_dir = drush_tempdir();
    drush_invoke_process('@self', 'config-export', array(), array('destination' => $destination_dir));
    // @todo Can DiffFormatter produce a CLI pretty diff?
    drush_shell_exec('diff -x %s -u %s %s', '*.git', $destination_dir, $source_dir);
    $output = drush_shell_exec_output();
    drush_print(implode("\n", $output));
  }

  if (drush_confirm(dt('Import the listed configuration changes?'))) {
    return drush_op('_drush_lissa_deploy_config_sync', $config_comparer);
  }
}

// Copied from submitForm() at /core/modules/config/src/Form/ConfigSync.php
function _drush_lissa_deploy_config_sync(StorageComparer $storage_comparer) {
  $config_importer = new ConfigImporter(
    $storage_comparer,
    Drupal::service('event_dispatcher'),
    Drupal::service('config.manager'),
    Drupal::lock(),
    Drupal::service('config.typed'),
    Drupal::moduleHandler(),
    Drupal::service('module_installer'),
    Drupal::service('theme_handler'),
    Drupal::service('string_translation')
  );
  if ($config_importer->alreadyImporting()) {
    drush_log('Another request may be synchronizing configuration already.', 'warning');
  }
  else{
    try {
      $config_importer->import();
      drupal_flush_all_caches();
      drush_log('The configuration was imported successfully.', 'success');
    }
    catch (ConfigException $e) {
      // Return a negative result for UI purposes. We do not differentiate
      // between an actual synchronization error and a failed lock, because
      // concurrent synchronizations are an edge-case happening only when
      // multiple developers or site builders attempt to do it without
      // coordinating.
      watchdog_exception('config_import', $e);
      return drush_set_error('config_import_fail', 'The import failed due to an error. Any errors have been logged.');
    }
  }
}
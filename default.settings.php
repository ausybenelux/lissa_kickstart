<?php

// The active and stage directories need to be defined here because the install
// phase will attempt to search for them if there are one or more custom config
// directories.
$config_base_directory = DRUPAL_ROOT . '/profiles/lissa_kickstart/config';

/*$config_directories = array(
  CONFIG_ACTIVE_DIRECTORY => $config_base_directory . '/active',
  CONFIG_STAGING_DIRECTORY => $config_base_directory . '/staging',
);*/

if (file_exists($config_base_directory . '/deploy')) {
  $config_directories['deploy'] = $config_base_directory . '/deploy';
}

$settings['hash_salt'] = 'NVPgQ-GudJktovJTq5v7x-3FveMR9dxdjsf6OOFxCl2Omd7iS2tFG4Ldt68dZ3yBpEyVdCgyGA';
$settings['update_free_access'] = FALSE;
$config['system.site.uuid'] = '65119fd0-68f7-45b7-ae4e-784208b9632c';

ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);


if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}

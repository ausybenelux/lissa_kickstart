<?php

$config_directories= array();

if (file_exists(DRUPAL_ROOT . '/profiles/lissa_kickstart/config/deploy')) {
  $config_directories['deploy'] = 'profiles/lissa_kickstart/config/deploy';
}

$settings['hash_salt'] = 'NVPgQ-GudJktovJTq5v7x-3FveMR9dxdjsf6OOFxCl2Omd7iS2tFG4Ldt68dZ3yBpEyVdCgyGA';
$settings['update_free_access'] = FALSE;

ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);


if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}

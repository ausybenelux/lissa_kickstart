<?php

/**
 * @file
 * Drupal hook implementation for the LISSA Kickstart profile.
 */

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function lissa_kickstart_form_install_configure_form_alter(&$form, $form_state) {
  // Pre-populate the site name with the server name.
  $form['site_information']['site_name']['#default_value'] = \Drupal::request()->server->get('SERVER_NAME');
}

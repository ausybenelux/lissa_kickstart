<?php

/**
 * @file
 * Contains \Drupal\composer_manager\Form\SettingsForm.
 */

namespace Drupal\notification_push\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;


/**
 * Provides administrative settings for the Composer Manager module.
 *
 * @ingroup forms
 */
class SettingsForm extends ConfigFormBase implements FormInterface, ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'notification_push_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('notification_push.settings');

    $form['host'] = array(
      '#title' => 'Host',
      '#type' => 'textfield',
      '#default_value' => $config->get('host'),
    );

    $form['port'] = array(
      '#title' => 'Port',
      '#type' => 'textfield',
      '#default_value' => $config->get('port'),
    );

    $form['user'] = array(
      '#title' => 'User',
      '#type' => 'textfield',
      '#default_value' => $config->get('user'),
    );

    $form['password'] = array(
      '#title' => 'Password',
      '#type' => 'textfield',
      '#default_value' => $config->get('password'),
    );

    return $form;
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $form_state_values = $form_state->getValues();
    $this->config('notification_push.settings')
      ->set('host', $form_state_values['host'])
      ->set('port', $form_state_values['port'])
      ->set('user', $form_state_values['user'])
      ->set('password', $form_state_values['password'])
    ->save();
  }
}

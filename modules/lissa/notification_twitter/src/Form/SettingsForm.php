<?php

/**
 * @file
 * Contains \Drupal\notification_twitter\Form\SettingsForm.
 */

namespace Drupal\notification_twitter\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;


/**
 * Provides administrative settings for the Notification twitter module.
 *
 * @ingroup forms
 */
class SettingsForm extends ConfigFormBase implements FormInterface, ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array('notification_twitter.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'notification_twitter_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('notification_twitter.settings');

    $form['access_token'] = array(
      '#title' => 'Access Token',
      '#type' => 'textfield',
      '#default_value' => $config->get('access_token'),
    );

    // Secret tokens should not be shown again
    $form['access_token_secret'] = array(
      '#title' => 'Access Token Secret',
      '#type' => 'textfield',
      '#default_value' => $config->get('access_token_secret'),
    );

    $form['consumer_key'] = array(
      '#title' => 'Consumer Key',
      '#type' => 'textfield',
      '#default_value' => $config->get('consumer_key'),
    );

    // Secret tokens should not be shown again
    $form['consumer_secret'] = array(
      '#title' => 'Consumer Secret',
      '#type' => 'textfield',
      '#default_value' => $config->get('consumer_secret'),
    );

    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $form_state_values = $form_state->getValues();
    $this->config('notification_twitter.settings')
      ->set('access_token', $form_state_values['access_token'])
      ->set('access_token_secret', $form_state_values['access_token_secret'])
      ->set('consumer_key', $form_state_values['consumer_key'])
      ->set('consumer_secret', $form_state_values['consumer_secret'])
      ->save();
  }
}

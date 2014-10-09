<?php

/**
 * @file
 * Definition of Drupal\notification_entity\NotificationEntityForm.
 */

namespace Drupal\notification_entity;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Adds the image file field to the NotificationEntity form
 */
class NotificationEntityForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state)
  {
    $form = parent::form($form, $form_state);

    // Let the user upload an image
    $form['image_id'] = [
      '#type' => 'managed_file',
      '#name' => 'image',
      '#title' => $this->t('Image'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submit()
  {
    if (isset($form_state['values']['custom_content_block_image'])) {
      file_save_data($form_state['values']['custom_content_block_image']);
    }
  }
}

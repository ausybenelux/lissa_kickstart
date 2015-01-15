<?php

/**
 * @file
 * Contains \Drupal\notification_twitter\Controller\NotificationTwitterController.
 */

namespace Drupal\notification_twitter\Controller;

use Codebird\Codebird;
use Drupal\notification_entity\Entity\NotificationEntity;
use Drupal\system\Controller\FormAjaxController;
use Symfony\Component\HttpFoundation\Request;


/**
 * Implements functionality for the notification twitter module
 */
class NotificationTwitterController extends FormAjaxController {

  public function ajaxTweetNotification(Request $request) {
    list($form, $form_state) = $this->getForm($request);
    drupal_process_form($form['#form_id'], $form, $form_state);

    /** @var \Drupal\notification_entity\Entity\NotificationEntity $notification_entity */
    $notification_entity = $form_state->getFormObject()->getEntity();

    $this->tweetNotification($notification_entity);
  }

  /**
   * @param NotificationEntity $notificationEntity
   * @return string
   */
  public function tweetNotification(NotificationEntity $notificationEntity){
    $config = \Drupal::config('notification_twitter.settings');

    $cb = new Codebird();

    $cb->setToken(
      $config->get('access_token'),
      $config->get('access_token_secret')
    );

    $cb->setConsumerKey(
      $config->get('consumer_key'),
      $config->get('consumer_secret')
    );

    $event = $notificationEntity->getHost();
    $fields = $event->getFields();
    $field = $fields['field_event_twitter'];
    $hashtag = $field->getValue()[0]['value'];

    $params = [
      'status' => $notificationEntity->getTitle() . ' ' . $hashtag
    ];

    $reply = $cb->statuses_update($params);

    // There are more possible status codes which specify the potential problem better
    if (!$reply->httpstatus == 200) {
      drupal_set_message([t('There was an error sending your tweet.')]);
    }
  }
  
}
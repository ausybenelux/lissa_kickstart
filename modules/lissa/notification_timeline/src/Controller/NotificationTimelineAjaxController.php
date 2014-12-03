<?php

/**
 * @file
 * Contains \Drupal\notification_timeline\Controller\NotificationTimelineAjaxController.
 */

namespace Drupal\notification_timeline\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\system\Controller\FormAjaxController;
use Symfony\Component\HttpFoundation\Request;
use Drupal\notification_entity\Entity\NotificationEntity;


/**
 * Returns responses for devel module routes.
 */
class NotificationTimelineAjaxController extends FormAjaxController {

  /**
   * Route page callback: handles form requests.
   *
   * @param NodeInterface $node
   *   The node to show the timeline for.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return mixed
   *   Whatever is returned by the triggering element's #ajax['callback']
   *   function. One of:
   *   - A render array containing the new or updated content to return to the
   *     browser. This is commonly an element within the rebuilt form.
   *   - A \Drupal\Core\Ajax\AjaxResponse object containing commands for the
   *     browser to process.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
   */
  public function ajaxAddNotification(NodeInterface $node, Request $request) {
    list($form, $form_state) = $this->getForm($request);
    drupal_process_form($form['#form_id'], $form, $form_state);
    return $this->ajaxSubmitNotificationEntity($form, $form_state);
  }

  /**
   * Ajax callback to submit a notification entity.
   *
   * @param array $form
   *   Form API array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state information.
   *
   * @return AjaxResponse
   *   Ajax response with the html code for the new notification. A list of commands is given with the response
   *   to reset the form and the page to its original state.
   */
  public static function ajaxSubmitNotificationEntity(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\notification_entity\Entity\NotificationEntity $notification_entity */
    $notification_entity = $form_state->getFormObject()->getEntity();
    $type = $notification_entity->bundle();

    $notification_form = '#' . $type . '-notification-entity-form';

    if ($form_state->hasAnyErrors()) {

      $array = [
        'attributes' => ['id' => 'notification-form-errors'],
        '#theme' => 'status_messages',
      ];
      $errors = drupal_render($array);

      $response = new AjaxResponse();
      $response->addCommand(new RemoveCommand('.messages--error'));
      $response->addCommand(new PrependCommand($notification_form, $errors));

      return $response;
    }

    // Create the html for the notification entity
    $view_builder = \Drupal::entityManager()->getViewBuilder('notification_entity');
    $build = $view_builder->view($notification_entity);
    $view = drupal_render($build);

    $forms = NotificationTimelineController::buildNotificationForms();
    $rendered_forms = drupal_render($forms);
    drupal_process_attached($forms);

    $response = new AjaxResponse();
    $response->addCommand(new PrependCommand('#js-notification-list', $view));
    $response->addCommand(new ReplaceCommand('#notification-forms', $rendered_forms));

    return $response;
  }
}

<?php

/**
 * @file
 * Contains \Drupal\notification_timeline\Controller\NotificationTimelineController.
 */

  namespace Drupal\notification_timeline\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;


/**
 * Returns responses for devel module routes.
 */
class NotificationTimelineController extends ControllerBase {

  /**
   * Route page callback: returns the HTML for the default timeline page.
   *
   * @param NodeInterface $node
   *   The node to show the timeline for.
   *
   * @return array
   *   A render array containing the data to display.
   */
  public function nodeLoad(NodeInterface $node) {
    $build = array();
    $build['notification_form'] = $this->formBuilder()->getForm('Drupal\notification_timeline\Form\NotificationTimelineNotificationForm');
    $build['notification_type_forms'] = array(
      '#attributes' => array('id' => 'notification-forms'),
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attached' => array(
        'js' => array(
          drupal_get_path('module', 'notification_timeline') . '/js/plugins/Sticky/jquery.sticky.js',
          drupal_get_path('module', 'notification_timeline') . '/js/notification-timeline.js',
          drupal_get_path('module', 'notification_timeline') . '/js/plugins/Waypoints/waypoints.min.js',
        ),
      ),
    );
    $form_builder = \Drupal::service('entity.form_builder');
    // Add all type forms and hide them.
    foreach (\Drupal::entityManager()->getStorage('notification_type')->loadMultiple() as $type) {
      $entity = \Drupal::entityManager()->getStorage('notification_entity')->create(array('type' => $type->id()));
      $build['notification_type_forms'][$type->id()] = $form_builder->getForm($entity);
      $build['notification_type_forms'][$type->id()]['form_label'] = array(
        '#markup' => '<h2>' . t('Add !notification_type', array('!notification_type' => $type->label())) . '</h2>',
        '#weight' => -100,
      );
      $build['notification_type_forms'][$type->id()]['#attributes']['class'][] = 'js-hide';
    }

    $build['timeline'] = $this->getTimeline($node);
    return $build;
  }

  /**
   * Route page callback: returns the HTML for adding a notification
   *
   * @param NodeInterface $node
   *   The node to add a notification to.
   * @param string $notification_type
   *   The id of the notification type.
   *
   * @return array
   *   A render array containing the data to display.
   */
  public function addNotification(NodeInterface $node, $notification_type) {
    $build = array();
    $form_builder = \Drupal::service('entity.form_builder');
    $entity = \Drupal::entityManager()->getStorage('notification_entity')->create(array('type' => $notification_type));
    $build['notification_form'] = $form_builder->getForm($entity);
    $build['timeline'] = self::getTimeline($node);
    return $build;
  }

  /**
   * Returns a render array with the timeline of the specified node.
   */
  protected static function getTimeline(NodeInterface $node) {
    $entities = self::getNotifications($node);
    $output = array(
      '#theme' => 'notification_timeline',
      '#node' => $node,
      '#notifications' => $entities,
    );
    return $output;
  }

  /**
   * Returns an array of notifications linked to the specified node.
   */
  protected static function getNotifications(NodeInterface $node) {
    $entity_query = \Drupal::entityQuery('notification_entity', 'AND');
    $entity_query->condition('host_id', $node->id());
    $entity_query->sort('timeline', 'DESC');
    $result = $entity_query->execute();
    return $result ? \Drupal::entityManager()->getStorage('notification_entity')->loadMultiple($result) : array();
  }

  /**
   * Checks access to the node timeline.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The current node.
   *
   * @return string
   *   A \Drupal\Core\Access\AccessInterface constant value.
   */
  public function checkTimelineAccess(NodeInterface $node) {
    $node_type = $node->type->entity;
    $enabled = $node_type->getThirdPartySetting('notification_timeline', 'enabled', FALSE);
    return AccessResult::allowedIf($enabled);
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
    $response = new AjaxResponse();

    /** @var \Drupal\notification_entity\Entity\NotificationEntity $notification_entity */
    $notification_entity = $form_state->getFormObject()->getEntity();

    // Create the html for the notification entity
    $view_builder = \Drupal::entityManager()->getViewBuilder('notification_entity');
    $build = $view_builder->view($notification_entity);
    $view = render($build);

    $response->addCommand(new PrependCommand('#js-notification-list', $view));
    $response->addCommand(new InvokeCommand('#notification-timeline-notification-form', 'removeClass', ['js-hide']));
    $response->addCommand(new InvokeCommand('#' . $notification_entity->bundle() . '-notification-entity-form', 'addClass', ['js-hide']));
    $response->addCommand(new InvokeCommand('#' . $notification_entity->bundle() . '-notification-entity-form', 'trigger', ['reset']));

    // Rebuild the timeline navigation.
    $node = $notification_entity->getHost();
    $timeline = self::getTimeline($node);
    $timeline_output = render($timeline);
    $response->addCommand(new ReplaceCommand('.timeline', $timeline_output));

    return $response;
  }
}

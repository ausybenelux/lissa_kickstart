<?php

/**
 * @file
 * Definition of views_handler_field_node_link_timeline.
 */

namespace Drupal\notification_timeline\Plugin\views\field;

use Drupal\node\Plugin\views\field\Link;
use Drupal\views\ResultRow;

/**
 * Field handler to present a node timeline link.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("node_link_timeline")
 */
class LinkTimeline extends Link {

  /**
   * Prepares the link to the node.
   *
   * @param \Drupal\Core\Entity\EntityInterface $node
   *   The node entity this field belongs to.
   * @param ResultRow $values
   *   The values retrieved from the view's result set.
   *
   * @return string
   *   Returns a string for the link text.
   */
  protected function renderLink($node, ResultRow $values) {
    // Ensure user has access to manage the node timeline.
    $parameters = ['node' => $node->id()];
    $access = \Drupal::service('access_manager')->checkNamedRoute('notification_timeline.node_timeline', $parameters, \Drupal::currentUser());
    if (!$access) {
      return;
    }

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = "node/" . $node->id() . "/timeline";
    $this->options['alter']['query'] = drupal_get_destination();

    $text = !empty($this->options['text']) ? $this->options['text'] : $this->t('Timeline');
    return $text;
  }

}

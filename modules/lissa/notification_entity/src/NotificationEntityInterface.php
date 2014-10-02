<?php
/**
 * @file
 * Contains \Drupal\notification_entity\NotificationEntityInterface.
 */
namespace Drupal\notification_entity;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\node\NodeInterface;
use \Drupal\file\FileInterface;

/**
 * Provides an interface defining a Notification Entity.
 */
interface NotificationEntityInterface extends ContentEntityInterface {
  /**
   * Returns the notification type.
   *
   * @return string
   *   The notification type.
   */
  public function getType();

  /**
   * Returns the notification title.
   *
   * @return string
   *   Title of the notification.
   */
  public function getTitle();

  /**
   * Sets the notification title.
   *
   * @param string $title
   *   The notification title.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setTitle($title);

  /**
   * Returns the notification host node.
   *
   * @return \Drupal\node\NodeInterface
   *   The host node.
   */
  public function getHost();

  /**
   * Sets the notification host node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node object.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setHost(NodeInterface $node);

  /**
   * Returns the notification host node id.
   *
   * @return int
   *   The host node id.
   */
  public function getHostId();

  /**
   * Sets the notification host node id.
   *
   * @param int $hostid
   *   The node id.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setHostId($hostid);

  /**
   * Returns the notification creation timestamp.
   *
   * @return int
   *   Creation timestamp of the notification.
   */
  public function getCreatedTime();

  /**
   * Sets the notification creation timestamp.
   *
   * @param int $timestamp
   *   The notification creation timestamp.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the notification timeline timestamp.
   *
   * @return int
   *   Timeline timestamp of the notification.
   */
  public function getTimelineTime();

  /**
   * Sets the notification timeline timestamp.
   *
   * @param int $timestamp
   *   The notification timeline timestamp.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setTimelineTime($timestamp);

  /**
   * Returns the notification rich content.
   *
   * @return string
   *   The notification rich content.
   */
  public function getRichContent();

  /**
   * Sets the notification rich content.
   *
   * @param string $content
   *   The content.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setRichContent($content);

  /**
   * Returns the notification image entity.
   *
   * @return \Drupal\file\FileInterface
   *   The notification image entity.
   */
  public function getImage();

  /**
   * Sets the notification image entity.
   *
   * @param FileInterface $image
   *   The image entity object.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setImage(FileInterface $image);
}
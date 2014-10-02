<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Entity\NotificationEntity.
 */

namespace Drupal\notification_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\file\FileInterface;
use Drupal\node\NodeInterface;
use Drupal\notification_entity\NotificationEntityInterface;

/**
 * Defines the notification entity class.
 *
 * @ContentEntityType(
 *   id = "notification",
 *   label = @Translation("Notification"),
 *   bundle_label = @Translation("Notification type"),
 *   handlers = {
 *     "storage" = "Drupal\notification_entity\NotificationEntityStorage",
 *     "storage_schema" = "Drupal\notification_entity\NotificationEntityStorageSchema",
 *     "view_builder" = "Drupal\notification_entity\NotificationEntityViewBuilder",
 *     "access" = "Drupal\notification_entity\NotificationEntityAccessControlHandler",
 *     "views_data" = "Drupal\notification_entity\NotificationEntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\notification_entity\NotificationEntityForm",
 *       "delete" = "Drupal\notification_entity\Form\NotificationEntityDeleteForm",
 *       "edit" = "Drupal\notification_entity\NotificationEntityForm"
 *     },
 *     "list_builder" = "Drupal\notification_entity\NotificationEntityListBuilder"
 *   },
 *   base_table = "notification",
 *   data_table = "notification_field_data",
 *   translatable = FALSE,
 *   entity_keys = {
 *     "id" = "nid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "notification_type",
 *   field_ui_base_route = "entity.notification_type.edit_form",
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "entity.notification.canonical",
 *     "delete-form" = "entity.notification.delete_form",
 *     "edit-form" = "entity.notification.edit_form"
 *   }
 * )
 */
class NotificationEntity extends ContentEntityBase implements NotificationEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHost() {
    return $this->get('hostid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setHost(NodeInterface $node) {
    $this->set('hostid', $node->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHostId() {
    return $this->get('hostid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setHostId($hostId) {
    $this->set('hostid', $hostId);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * Returns the notification timeline timestamp.
   *
   * @return int
   *   Timeline timestamp of the notification.
   */
  public function getTimelineTime() {
    // TODO: Implement getTimelineTime() method.
  }

  /**
   * Sets the notification timeline timestamp.
   *
   * @param int $timestamp
   *   The notification timeline timestamp.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setTimelineTime($timestamp) {
    // TODO: Implement setTimelineTime() method.
  }

  /**
   * Returns the notification rich content.
   *
   * @return string
   *   The notification rich content.
   */
  public function getRichContent() {
    // TODO: Implement getRichContent() method.
  }

  /**
   * Sets the notification rich content.
   *
   * @param string $content
   *   The content.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setRichContent($content) {
    // TODO: Implement setRichContent() method.
  }

  /**
   * Returns the notification image entity.
   *
   * @return \Drupal\file\FileInterface
   *   The notification image entity.
   */
  public function getImage() {
    // TODO: Implement getImage() method.
  }

  /**
   * Sets the notification image entity.
   *
   * @param FileInterface $image
   *   The image entity object.
   *
   * @return \Drupal\notification_entity\NotificationEntityInterface
   *   The called notification entity.
   */
  public function setImage(FileInterface $image) {
    // TODO: Implement setImage() method.
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['nid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Notification ID'))
      ->setDescription(t('The notification ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The notification UUID.'))
      ->setReadOnly(TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The notification type.'))
      ->setSetting('target_type', 'notification_type')
      ->setReadOnly(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of this notification, always treated as non-markup plain text.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['hostid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Host node'))
      ->setDescription(t('The node id of the Node entity the notification applies to.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback(array('Drupal\notification_entity\Entity\NotificationEntity', 'getCurrentNodeId'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'node',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the node was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the node was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Default value callback for 'hostid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentNodeId() {
    return array(\Drupal::request()->attributes->get('node')->id());
  }
}

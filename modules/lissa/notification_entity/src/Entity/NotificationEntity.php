<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Entity\NotificationEntity.
 */

namespace Drupal\notification_entity\Entity;

use DateInterval;
use DateTime;
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
 *   id = "notification_entity",
 *   label = @Translation("Notification"),
 *   bundle_label = @Translation("Notification type"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "storage_schema" = "Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema",
 *     "view_builder" = "Drupal\notification_entity\NotificationEntityViewBuilder",
 *     "access" = "Drupal\notification_entity\NotificationEntityAccessControlHandler",
 *     "views_data" = "Drupal\notification_entity\NotificationEntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\notification_entity\Form\NotificationEntityDeleteForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm"
 *     },
 *     "list_builder" = "Drupal\notification_entity\NotificationEntityListBuilder"
 *   },
 *   base_table = "notification_entity",
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
 *     "canonical" = "entity.notification_entity.canonical",
 *     "delete-form" = "entity.notification_entity.delete_form",
 *     "edit-form" = "entity.notification_entity.edit_form"
 *   }
 * )
 */
class NotificationEntity extends ContentEntityBase implements NotificationEntityInterface {

  // Number of seconds
  CONST TIMELINE_TIME = 30;

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
    return $this->get('host_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setHost(NodeInterface $node) {
    $this->set('host_id', $node->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHostId() {
    return $this->get('host_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setHostId($hostId) {
    $this->set('host_id', $hostId);
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
   * {@inheritdoc}
   */
  public function getTimelineTime() {
    return $this->get('timeline')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTimelineTime($timestamp) {
    $this->set('timeline', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRichContent() {
    return $this->get('content')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRichContent($content) {
    $this->set('content', $content);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImage() {
    return $this->get('image_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setImage(FileInterface $image) {
    $this->set('image_id', $image->id());
    return $this;
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
        'settings' => array(
          'size' => 60,
        ),
      ));

    $fields['host_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Host node'))
      ->setDescription(t('The node id of the Node entity the notification applies to.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\notification_entity\Entity\NotificationEntity::getCurrentNodeId')
      ->setTranslatable(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the node was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the node was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['timeline'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Time'))
      ->setDescription(t('The time that the notification should appear on the timeline.'))
      ->setDefaultValueCallback('Drupal\notification_entity\Entity\NotificationEntity::getDefaultTimelineTime')
      ->setRevisionable(TRUE)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'datetime_default',
        'weight' => -4,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'timeline_date',
        'weight' => -10,
      ));

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setDescription(t('The file fid of the file entity containing the notification image.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(NULL)
      ->setSetting('target_type', 'file')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'node',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 50,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ));

    $fields['content'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Content'))
      ->setDisplayOptions('view', array(
        'type' => 'text',
        'weight' => 0,
        'label' => 'hidden',
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => 51,
        'settings' => array(
          'rows' => 2,
        ),
      ));

    return $fields;
  }

  /**
   * Default value callback for 'host_id' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentNodeId() {
    $node = \Drupal::request()->attributes->get('node');

    if (isset($node)) {
      return [$node->id()];
    }

    return [];
  }

  /**
   * Default value callback for 'timeline'
   * Returns a timestamp from 30 seconds before the request time
   *
   * @return array
   *   An array of default values
   */
  public static function getDefaultTimelineTime() {
    $time = new DateTime;
    $time->setTimestamp(REQUEST_TIME);

    // Subtract x seconds from request time
    $time->sub(new DateInterval('PT' . NotificationEntity::TIMELINE_TIME . 'S'));

    return [['default_date_type' => TRUE, 'default_date' => $time->format('Y-m-d H:i:s')]];
  }
}

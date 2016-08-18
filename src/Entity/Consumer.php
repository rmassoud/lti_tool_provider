<?php
/**
 * @file
 * Contains \Drupal\lti_tool_provider\Entity\LTIToolProviderConsumer.
 */

namespace Drupal\lti_tool_provider\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\lti_tool_provider\ConsumerInterface;
use Drupal\user\UserInterface;

/**
 * Defines the LTIToolProviderConsumer entity.
 *
 * @ingroup lti_tool_provider
 *
 * @ContentEntityType(
 *   id = "lti_tool_provider_consumer",
 *   label = @Translation("Consumer entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lti_tool_provider\Entity\Controller\ConsumerListBuilder",
 *     "form" = {
 *       "add" = "Drupal\lti_tool_provider\Form\ConsumerForm",
 *       "edit" = "Drupal\lti_tool_provider\Form\ConsumerForm",
 *       "delete" = "Drupal\lti_tool_provider\Form\ConsumerDeleteForm",
 *     },
 *     "access" = "Drupal\lti_tool_provider\ConsumerAccessControlHandler",
 *   },
 *   base_table = "lti_tool_provider_consumer",
 *   admin_permission = "administer lti_tool_provider module",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "lti_tool_provider_consumer_id",
 *     "label" = "lti_tool_provider_consumer_consumer",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "lti_tool_provider_consumer.edit_form",
 *     "delete-form" = "lti_tool_provider.consumer_delete"
 *   },
 * )
 *
 */
class Consumer extends ContentEntityBase implements ConsumerInterface {

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
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
  public function getChangedTime() {
    return $this->get('changed')->value;
  }


  /**
   * {@inheritdoc}
   *
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['lti_tool_provider_consumer_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Contact entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Contact entity.'))
      ->setReadOnly(TRUE);

    $fields['lti_tool_provider_consumer_consumer'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Consumer'))
      ->setDescription(t('The name of the Consumer entity.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -6,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['lti_tool_provider_consumer_key'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Key'))
      ->setDescription(t('The key of the Consumer entity.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['lti_tool_provider_consumer_secret'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Secret'))
      ->setDescription(t('The secret of the Consumer entity.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // $fields['domain'] = BaseFieldDefinition::create('string')
    //   ->setLabel(t('Domain'))
    //   ->setDescription(t('The domain of the Consumer entity.'))
    //   ->setSettings(array(
    //     'default_value' => '',
    //     'max_length' => 255,
    //     'text_processing' => 0,
    //   ))
    //   ->setDisplayOptions('view', array(
    //     'label' => 'above',
    //     'type' => 'string',
    //     'weight' => -3,
    //   ))
    //   ->setDisplayOptions('form', array(
    //     'type' => 'string',
    //     'weight' => -3,
    //   ))
    //   ->setDisplayConfigurable('form', TRUE)
    //   ->setDisplayConfigurable('view', TRUE);

    $fields['date_joined'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Date joined'))
      ->setDescription(t('Date the consumer was added'));


    return $fields;
  }
}

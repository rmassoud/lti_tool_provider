<?php
/**
 * @file
 * Contains Drupal\lti_tool_provider\Entity\Controller\ConsumerListBuilder.
 */

namespace Drupal\lti_tool_provider\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for lti_tool_provider entity.
 *
 * @ingroup lti_tool_provider
 */
class ConsumerListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    // $build['description'] = array(
    //   '#markup' => $this->t('Administration page for LTI Tool Consumers.'),
    // );
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['lti_tool_provider_consumer_id'] = $this->t('ConsumerID');
    $header['lti_tool_provider_consumer_consumer'] = $this->t('Consumer');
    $header['lti_tool_provider_consumer_key'] = $this->t('Key');
    $header['lti_tool_provider_consumer_secret'] = $this->t('Secret');
    // $header['domain'] = $this->t('Domain');
    $header['date_joined'] = $this->t('Date joined');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\lti_tool_provider\Entity\LTIToolProviderConsumer */
    $row['lti_tool_provider_consumer_id'] = $entity->id();
    $row['lti_tool_provider_consumer_consumer'] = \Drupal::l(
      $this->getLabel($entity),
      Url::fromRoute('lti_tool_provider.consumer_view',
        array(
          'lti_tool_provider_consumer' => $entity->id(),
        )
      )
    );
    $row['lti_tool_provider_consumer_key'] = $entity->lti_tool_provider_consumer_key->value;
    $row['lti_tool_provider_consumer_secret'] = $entity->lti_tool_provider_consumer_secret->value;
    // $row['domain'] = $entity->domain->value;
    $row['date_joined'] = format_date($entity->date_joined->value);
    return $row + parent::buildRow($entity);
  }

}

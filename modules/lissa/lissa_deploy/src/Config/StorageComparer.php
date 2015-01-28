<?php

/**
 * @file
 * Contains \Drupal\lissa_deploy\Config\StorageComparer.
 */

namespace Drupal\lissa_deploy\Config;

use Drupal\Component\Utility\String;
use Drupal\Core\Config\Entity\ConfigDependencyManager;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\StorageComparer as StorageComparerBase;

/**
 * Defines a config storage comparer that ignores UUIDs.
 *
 * Ignoring config entity UUIDs allows configuration to be imported from other
 * sites. This is useful for OTAP setups where each drupal instance was
 * installed seperately.
 */
class StorageComparer extends StorageComparerBase {

  /**
   * {@inheritdoc}
   */  The storage collection to operate on.
   */
  protected function addChangelistUpdate($collection) {
    foreach (array_intersect($this->sourceNames[$collection], $this->targetNames[$collection]) as $name) {
      if (!$this->isDataEqual($this->sourceData[$collection][$name], $this->targetData[$collection][$name])) {
        // Make sure the UUIDs are equal.
        if (isset($this->targetData[$collection][$name]['uuid'])) {
          $this->sourceData[$collection][$name]['uuid'] = $this->targetData[$collection][$name]['uuid'];
          $this->getSourceStorage($collection)->write($name, $this->sourceData[$collection][$name]);
        }
        $this->addChangeList($collection, 'update', array($name));
      }
    }
  }

  /**
   * Returns TRUE if the datasets, except the UUID, are equal.
   */
  protected function isDataEqual($sourceData, $targetData) {
    if (isset($sourceData['uuid']) && isset($targetData['uuid'])) {
      unset($sourceData['uuid'], $targetData['uuid']);
    }

    return $sourceData === $targetData;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSiteUuid() {
    return TRUE;
  }
}

<?php

/**
 * @file
 * Contains \Drupal\lissa_kickstart_core\Controller\LissaKickstartCoreController.
 */

namespace Drupal\lissa_kickstart_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

/**
 * Controller routings for lissa_kickstart_core routes.
 */
class LissaKickstartCoreController extends ControllerBase {

  /**
   * Constructs the LISSA Kickstart homepage.
   *
   * This controller will show the user login page when not logged in.
   */
  public function home() {
    $roles = $this->currentUser()->getRoles();

    // Show the events overview when the user has production or operator access.
    if (in_array('production', $roles) || in_array('operator', $roles)) {
      $response = $this->redirect('view.events.admin_overview');
    }
    // If user is anonymous or has no manage event access.
    else {
      $response = $this->redirect('user.page');
    }
    return $response;
  }
}
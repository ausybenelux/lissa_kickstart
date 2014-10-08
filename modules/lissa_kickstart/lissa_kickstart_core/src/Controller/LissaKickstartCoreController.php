<?php

/**
 * @file
 * Contains \Drupal\lissa_kickstart_core\Controller\LissaKickstartCoreController.
 */

namespace Drupal\lissa_kickstart_core\Controller;

use Drupal\Core\Controller\ControllerBase;

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
    $response = $this->redirect('user.page');
    return $response;
  }
}
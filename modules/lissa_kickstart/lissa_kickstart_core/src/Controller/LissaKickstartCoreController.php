<?php

/**
 * @file
 * Contains \Drupal\lissa_kickstart_core\Controller\LissaKickstartCoreController.
 */

namespace Drupal\lissa_kickstart_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Controller routings for lissa_kickstart_core routes.
 */
class LissaKickstartCoreController extends ControllerBase {

  protected static $redirectMap = [
    'production' => 'view.events.admin_overview',
    'operator' => 'view.events.admin_overview',
    'technical_engineer' => 'view.events.admin_overview',
  ];

  /**
   * Returns the route name of the current user's homepage.
   */
  protected static function getHomePageRoute($account = NULL) {
    if (empty($account)) {
      $account = \Drupal::currentUser();
    }
    $roles = $account->getRoles();

    foreach (self::$redirectMap as $role_name => $route) {
      if (in_array($role_name, $roles)) {
        return $route;
      }
    }

    return 'user.page';
  }

  /**
   * Constructs the LISSA Kickstart homepage.
   */
  public function home() {
    $route = self::getHomePageRoute($this->currentUser());
    $response = $this->redirect($route);
    return $response;
  }

  /**
   * Send the user to another page after logging in.
   */
  public static function redirectLoginForm(&$form, FormStateInterface &$form_state) {
    if (!\Drupal::request()->request->has('destination')) {
      $account = \Drupal::entityManager()->getStorage('user')->load($form_state->get('uid'));
      $route = self::getHomePageRoute($account);
      $form_state->setRedirect($route);
    }
  }
}
<?php

/**
 * @package     Password Protected for Babypad
 * @subpackage  Admin Bar
 *
 * Adds an indicator in the admin if パスワード保護 for ベビーパッド 有効.
 */

namespace Password_Protected_Babypad;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'plugins_loaded', array( 'Password_Protected_Babypad\Admin_Bar', 'load' ), 15 );

class Admin_Bar {

  /**
   * Load
   *
   * @internal  Private. Called via `plugins_loaded` actions.
   */
  public static function load() {

    add_action( 'wp_head', array( get_class(), 'styles' ) );
    add_action( 'admin_head', array( get_class(), 'styles' ) );
    add_action( 'wp_before_admin_bar_render', array( get_class(), 'toolbar_item' ) );

  }

  /**
   * Toolbar Item
   *
   * @internal  Private. Called via `wp_before_admin_bar_render` actions.
   */
  public static function toolbar_item() {

    global $wp_admin_bar;

    if ( self::allow_current_user() ) {

      $wp_admin_bar->add_menu( array(
        'id'     => 'password_protected_babypad',
        'title'  => '',
        'href'   => self::get_toolbar_item_url(),
        'meta'   => array(
          'title' => self::get_toolbar_item_title()
        )
      ) );

    }

  }

  /**
   * Get Toolbar Item URL
   *
   * @return  string
   */
  private static function get_toolbar_item_url() {

    if ( current_user_can( 'manage_options' ) ) {
      return admin_url( 'options-general.php?page=password-protected-babypad' );
    }

    return '';

  }

  /**
   * Get Toolbar Item Title
   *
   * @return  string
   */
  private static function get_toolbar_item_title() {

    if ( self::is_enabled() ) {
      return __( 'パスワード保護 for ベビーパッド 有効', 'password-protected-babypad' );
    }

    return __( 'パスワード保護 for ベビーパッド 無効', 'password-protected-babypad' );

  }

  /**
   * Styles
   *
   * @internal  Private. Called via `wp_head` and `admin_head` actions.
   */
  public static function styles() {

    if ( self::allow_current_user() ) {

      if ( self::is_enabled() ) {
        $icon = '\f160';  // Locked
        $background = '#46b450';
      } else {
        $icon = '\f528';  // Unlocked
        $background = 'transparent';
      }

      ?>
      <style type="text/css">
      #wp-admin-bar-password_protected_babypad { background-color: <?php echo $background; ?> !important; }
      #wp-admin-bar-password_protected_babypad > .ab-item { color: #fff !important;  }
      #wp-admin-bar-password_protected_babypad > .ab-item:before { content: "<?php echo $icon; ?>"; top: 2px; color: #fff !important; margin-right: 0px; }
      #wp-admin-bar-password_protected_babypad:hover > .ab-item { background-color: <?php echo $background; ?> !important; color: #fff; }
      </style>
      <?php

    }

  }

  /**
   * Allow Current User
   *
   * @return  boolean
   */
  private static function allow_current_user() {

    return is_user_logged_in();

  }

  /**
   * Is Enabled
   *
   * @return  boolean
   */
  private static function is_enabled() {

    return (bool) get_option( 'password_protected_babypad_status' );

  }
}

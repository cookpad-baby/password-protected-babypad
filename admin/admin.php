<?php

class Password_Protected_Babypad_Admin {

  var $settings_page_id;
  var $options_group = 'password-protected-babypad';

  /**
   * Constructor
   */
  public function __construct() {

    global $wp_version;

    add_action( 'admin_init', array( $this, 'password_protected_babypad_settings' ), 5 );
    add_action( 'admin_init', array( $this, 'add_privacy_policy' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_action( 'password_protected_babypad_help_tabs', array( $this, 'help_tabs' ), 5 );
    add_action( 'admin_notices', array( $this, 'password_protected_babypad_admin_notices' ) );
    add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
    add_filter( 'plugin_action_links_password-protected-babypad/password-protected-babypad.php', array( $this, 'plugin_action_links' ) );
    add_filter( 'pre_update_option_password_protected_babypad_password', array( $this, 'pre_update_option_password_protected_babypad_password' ), 10, 2 );

  }

  /**
   * Add Privacy Policy
   */
  public function add_privacy_policy() {

    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
      return;
    }

    $content = _x( 'The パスワード保護 for ベビーパッド plugin stores a cookie on successful password login containing a hashed version of the entered password. It does not store any information about the user. The cookie stored is named <code>bid_n_password_protected_babypad_auth</code> where <code>n</code> is the blog ID in a multisite network', 'privacy policy content', 'password-protected-babypad' );

    wp_add_privacy_policy_content( __( 'パスワード保護 for ベビーパッド Plugin', 'password-protected-babypad' ), wp_kses_post( wpautop( $content, false ) ) );

  }

  /**
   * Admin Menu
   */
  public function admin_menu() {

    $this->settings_page_id = add_options_page( __( 'パスワード保護 for ベビーパッド', 'password-protected-babypad' ), __( 'パスワード保護 for ベビーパッド', 'password-protected-babypad' ), 'manage_options', 'password-protected-babypad', array( $this, 'settings_page' ) );
    add_action( 'load-' . $this->settings_page_id, array( $this, 'add_help_tabs' ), 20 );

  }

  /**
   * Settings Page
   */
  public function settings_page() {
    ?>

    <div class="wrap">
      <div id="icon-options-general" class="icon32"><br /></div>
      <h2><?php _e( 'パスワード保護 for ベビーパッドの設定', 'password-protected-babypad' ) ?></h2>
      <form method="post" action="options.php">
        <?php
        settings_fields( 'password-protected-babypad' );
        do_settings_sections( 'password-protected-babypad' );
        ?>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ) ?>"></p>
      </form>
      <?php do_settings_sections( 'password-protected-babypad-compat' ); ?>
    </div>

    <?php
  }

  /**
   * Add Help Tabs
   */
  public function add_help_tabs() {

    global $wp_version;

    if ( version_compare( $wp_version, '3.3', '<' ) ) {
      return;
    }

    do_action( 'password_protected_babypad_help_tabs', get_current_screen() );

  }

  /**
   * Help Tabs
   *
   * @param  object  $current_screen  Screen object.
   */
  public function help_tabs( $current_screen ) {

    $current_screen->add_help_tab( array(
      'id'      => 'PASSWORD_PROTECTED_SETTINGS',
      'title'   => __( 'パスワード保護 for ベビーパッド', 'password-protected-babypad' ),
      'content' =>  __( '<p><strong>クラウド以外の保護</strong><br />クラウド以外にも、ベビーパッドアプリではない端末（PCなど）からのアクセスに対してパスワードの入力を求めるようにします。</p>', 'password-protected-babypad' )
        . __( '<p><strong>パスワードの併用</strong><br />固定パスワードと通常パスワード（有効期間、更新のあるパスワード）を併用できます。</p>', 'password-protected-babypad' )
    ) );

  }

  /**
   * Settings API
   */
  public function password_protected_babypad_settings() {

    add_settings_section(
      'password_protected_babypad',
      '',
      array( $this, 'password_protected_babypad_settings_section' ),
      $this->options_group
    );

/*    add_settings_field(
      'password_protected_babypad_status',
      __( 'パスワード保護状況', 'password-protected-babypad' ),
      array( $this, 'password_protected_babypad_status_field' ),
      $this->options_group,
      'password_protected_babypad'
    );*/

    add_settings_field(
      'password_protected_babypad_permissions',
      __( 'クラウド以外の保護', 'password-protected-babypad' ),
      array( $this, 'password_protected_babypad_permissions_field' ),
      $this->options_group,
      'password_protected_babypad'
    );

    add_settings_field(
      'password_protected_babypad_both',
      __( 'パスワードの併用', 'password-protected-babypad' ),
      array( $this, 'password_protected_babypad_both_field' ),
      $this->options_group,
      'password_protected_babypad'
    );

    add_settings_field(
      'password_protected_babypad_remember_me',
      __( 'ログインユーザー記録', 'password-protected-babypad' ),
      array( $this, 'password_protected_babypad_remember_me_field' ),
      $this->options_group,
      'password_protected_babypad'
    );

    add_settings_field(
      'password_protected_babypad_remember_me_lifetime',
      __( 'ログインユーザー記録日数', 'password-protected-babypad' ),
      array( $this, 'password_protected_babypad_remember_me_lifetime_field' ),
      $this->options_group,
      'password_protected_babypad'
    );

/*    register_setting( $this->options_group, 'password_protected_babypad_status', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_feeds', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_rest', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_administrators', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_users', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_password', array( $this, 'sanitize_password_protected_babypad_password' ) ); */
    register_setting( $this->options_group, 'password_protected_babypad_pc', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_both', 'intval' );
    register_setting( $this->options_group, 'password_protected_babypad_remember_me', 'boolval' );
    register_setting( $this->options_group, 'password_protected_babypad_remember_me_lifetime', 'intval' );

  }

  /**
   * Sanitize Password Field Input
   *
   * @param   string  $val  Password.
   * @return  string        Sanitized password.
   */
  public function sanitize_password_protected_babypad_password( $val ) {

    $old_val = get_option( 'password_protected_babypad_password' );

    if ( is_array( $val ) ) {
      if ( empty( $val['new'] ) ) {
        return $old_val;
      } elseif ( empty( $val['confirm'] ) ) {
        add_settings_error( 'password_protected_babypad_password', 'password_protected_babypad_password', __( 'New password not saved. When setting a new password please enter it in both fields.', 'password-protected-babypad' ) );
        return $old_val;
      } elseif ( $val['new'] != $val['confirm'] ) {
        add_settings_error( 'password_protected_babypad_password', 'password_protected_babypad_password', __( 'New password not saved. Password fields did not match.', 'password-protected-babypad' ) );
        return $old_val;
      } elseif ( $val['new'] == $val['confirm'] ) {
        add_settings_error( 'password_protected_babypad_password', 'password_protected_babypad_password', __( '新しいパスワードを設定しました。', 'password-protected-babypad' ), 'updated' );
        return $val['new'];
      }
      return get_option( 'password_protected_babypad_password' );
    }

    return $val;

  }

  /**
   * パスワード保護 for ベビーパッド Section
   */
  public function password_protected_babypad_settings_section() {

    if(!get_option( 'password_protected_babypad_status' )) {
      echo $this->admin_error_display( __( 'パスワードリストファイルに、まだ情報が追加されていません。確認してください。', 'password-protected-babypad').'<br />'.__('ここでの設定は、リストファイルにパスワード情報がある場合に限り有効になります。', 'password-protected-babypad' ) );
    }

    echo '<p>' . __( 'パスワードでベビーパッドを保護します。ユーザーはスマートフォンでベビーパッドのコンテンツを表示する際にパスワードの入力を求められます。', 'password-protected-babypad' ) . '<br />
      ' . __( 'パスワード保護 for ベビーパッドの設定の詳細については、このページの上部にある「ヘルプ」タブを参照してください。', 'password-protected-babypad' ) . '</p>';

  }

  /**
   * Password Protection Status Field
   */
/*  public function password_protected_babypad_status_field() {
    echo '<label><input name="password_protected_babypad_status" id="password_protected_babypad_status" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_status', 1 ), false ) . ' /> ' . __( '有効', 'password-protected-babypad' ) . '</label>';
  }*/

  /**
   * Password Protection Permissions Field
   */
  public function password_protected_babypad_permissions_field() {

    echo '<label><input name="password_protected_babypad_pc" id="password_protected_babypad_pc" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_pc', 1 ), false ) . ' /> ' . __( 'ベビーパッドアプリ以外のアクセスを保護する', 'password-protected-babypad' ) . '</label>';
//    echo '<label><input name="password_protected_babypad_administrators" id="password_protected_babypad_administrators" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_administrators' ), false ) . ' /> ' . __( '管理者を許可する', 'password-protected-babypad' ) . '</label>';
//    echo '<label><input name="password_protected_babypad_users" id="password_protected_babypad_users" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_users' ), false ) . ' style="margin-left: 20px;" /> ' . __( 'ログインしたユーザーを許可する', 'password-protected-babypad' ) . '</label>';
//    echo '<label><input name="password_protected_babypad_babypad" id="password_protected_babypad_babypad" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_babypad' ), false ) . ' style="margin-left: 20px;" /> ' . __( 'ベビーパッドアプリ(iPad)でのアクセスを許可する', 'password-protected-babypad' ) . '</label>';
  }

  /**
   * Password Protection Both Field
   */
  public function password_protected_babypad_both_field() {

    echo '<label><input name="password_protected_babypad_both" id="password_protected_babypad_both" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_both' ), false ) . ' /> ' . __( '固定パスワードと通常パスワードを併用する', 'password-protected-babypad' ) . '</label>';
//    echo '<label><input name="password_protected_babypad_administrators" id="password_protected_babypad_administrators" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_administrators' ), false ) . ' /> ' . __( '管理者を許可する', 'password-protected-babypad' ) . '</label>';
//    echo '<label><input name="password_protected_babypad_users" id="password_protected_babypad_users" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_users' ), false ) . ' style="margin-left: 20px;" /> ' . __( 'ログインしたユーザーを許可する', 'password-protected-babypad' ) . '</label>';
//    echo '<label><input name="password_protected_babypad_babypad" id="password_protected_babypad_babypad" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_babypad' ), false ) . ' style="margin-left: 20px;" /> ' . __( 'ベビーパッドアプリ(iPad)でのアクセスを許可する', 'password-protected-babypad' ) . '</label>';
  }

  /**
   * Remember Me Field
   */
  public function password_protected_babypad_remember_me_field() {

    echo '<label><input name="password_protected_babypad_remember_me" id="password_protected_babypad_remember_me" type="checkbox" value="1" ' . checked( 1, get_option( 'password_protected_babypad_remember_me', 1 ), false ) . ' /></label>';

  }

  /**
   * Remember Me lifetime field
   */
  public function password_protected_babypad_remember_me_lifetime_field() {

    echo '<label><input name="password_protected_babypad_remember_me_lifetime" id="password_protected_babypad_remember_me_lifetime" type="number" value="' . get_option( 'password_protected_babypad_remember_me_lifetime', 120 ) . '" /></label>';

  }

  /**
   * Pre-update 'password_protected_babypad_password' Option
   *
   * Before the password is saved, MD5 it!
   * Doing it in this way allows developers to intercept with an earlier filter if they
   * need to do something with the plaintext password.
   *
   * @param   string  $newvalue  New Value.
   * @param   string  $oldvalue  Old Value.
   * @return  string             Filtered new value.
   */
  public function pre_update_option_password_protected_babypad_password( $newvalue, $oldvalue ) {

    global $Password_Protected_Babypad;

    if ( $newvalue != $oldvalue ) {
      $newvalue = $Password_Protected_Babypad->encrypt_password( $newvalue );
    }

    return $newvalue;

  }

  /**
   * Plugin Row Meta
   *
   * Adds GitHub and translate links below the plugin description on the plugins page.
   *
   * @param   array   $plugin_meta  Plugin meta display array.
   * @param   string  $plugin_file  Plugin reference.
   * @param   array   $plugin_data  Plugin data.
   * @param   string  $status       Plugin status.
   * @return  array                 Plugin meta array.
   */
  public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

    if ( 'password-protected-babypad/password-protected-babypad.php' == $plugin_file ) {
      $plugin_meta[] = sprintf( '<a href="%s">%s</a>', __( 'http://github.com/benhuson/password-protected-babypad', 'password-protected-babypad' ), __( 'GitHub', 'password-protected-babypad' ) );
      $plugin_meta[] = sprintf( '<a href="%s">%s</a>', __( 'https://translate.wordpress.org/projects/wp-plugins/password-protected-babypad', 'password-protected-babypad' ), __( 'Translate', 'password-protected-babypad' ) );
    }

    return $plugin_meta;

  }

  /**
   * Plugin Action Links
   *
   * Adds settings link on the plugins page.
   *
   * @param   array  $actions  Plugin action links array.
   * @return  array            Plugin action links array.
   */
  public function plugin_action_links( $actions ) {

    $actions[] = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=password-protected-babypad' ), __( 'Settings', 'password-protected-babypad' ) );
    return $actions;

  }

  /**
   * Password Admin Notice
   * Warns the user if they have enabled password protection but not entered a password
   */
  public function password_protected_babypad_admin_notices() {

    global $Password_Protected_Babypad;

    // Check Support
    $screens = $this->plugin_screen_ids( array( 'dashboard', 'plugins' ) );
    if ( $this->is_current_screen( $screens ) ) {
      $supported = $Password_Protected_Babypad->is_plugin_supported();
      if ( is_wp_error( $supported ) ) {
        echo $this->admin_error_display( $supported->get_error_message( $supported->get_error_code() ) );
      }
    }

    // Settings
    if ( $this->is_current_screen( $this->plugin_screen_ids() ) ) {
      $status = get_option( 'password_protected_babypad_status' );
      $pwd = get_option( 'password_protected_babypad_password' );


/*      if ( (bool) $status && empty( $pwd ) ) {
        echo $this->admin_error_display( __( 'パスワード保護を有効化しましたが、パスワードはまだ設定されていません。下の欄で設定してください。', 'password-protected-babypad' ) );
      }

      if ( current_user_can( 'manage_options' ) && ( (bool) get_option( 'password_protected_babypad_pc' ) ) ) {
        echo $this->admin_error_display( __( 'パスワード保護を有効化し、PCでの閲覧を保護しました。PCでサイトを表示するためにパスワードを入力する必要があります。', 'password-protected-babypad' ) );
      }

        if ( (bool) get_option( 'password_protected_babypad_administrators' ) && (bool) get_option( 'password_protected_babypad_users' ) ) {
          echo $this->admin_error_display( __( 'パスワード保護を有効し、管理者とログインしているユーザーの閲覧を許可しました。他のユーザーは、サイトを表示するためにパスワードを入力する必要があります。', 'password-protected-babypad' ) );
        } elseif ( (bool) get_option( 'password_protected_babypad_administrators' ) ) {
          echo $this->admin_error_display( __( 'パスワード保護を有効化し、管理者の閲覧を許可しました。他のユーザーはサイトを見るのにパスワードを入力する必要があります。', 'password-protected-babypad' ) );
        } elseif ( (bool) get_option( 'password_protected_babypad_users' ) ) {
          echo $this->admin_error_display( __( 'パスワード保護を有効化し、ログインしているユーザーの閲覧を許可しました。他のユーザーはサイトを見るのにパスワードを入力する必要があります。', 'password-protected-babypad' ) );
        }
      }*/
    }
  }

  /**
   * Admin Error Display
   *
   * Returns a string wrapped in HTML to display an admin error.
   *
   * @param   string  $string  Error string.
   * @return  string           HTML error.
   */
  private function admin_error_display( $string ) {

    return '<div class="error"><p>' .  $string . '</p></div>';

  }

  /**
   * Is Current Screen
   *
   * Checks wether the admin is displaying a specific screen.
   *
   * @param   string|array  $screen_id  Admin screen ID(s).
   * @return  boolean
   */
  public function is_current_screen( $screen_id ) {

    if ( function_exists( 'get_current_screen' ) ) {
      $current_screen = get_current_screen();
      if ( ! is_array( $screen_id ) ) {
        $screen_id = array( $screen_id );
      }
      if ( in_array( $current_screen->id, $screen_id ) ) {
        return true;
      }
    }

    return false;

  }

  /**
   * Plugin Screen IDs
   *
   * @param   string|array  $screen_id  Additional screen IDs to add to the returned array.
   * @return  array                     Screen IDs.
   */
  public function plugin_screen_ids( $screen_id = '' ) {

    $screen_ids = array( 'options-' . $this->options_group, 'settings_page_' . $this->options_group );

    if ( ! empty( $screen_id ) ) {
      if ( is_array( $screen_id ) ) {
        $screen_ids = array_merge( $screen_ids, $screen_id );
      } else {
        $screen_ids[] = $screen_id;
      }
    }

    return $screen_ids;

  }

}

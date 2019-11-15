<?php

/**
 * Based roughly on wp-login.php @revision 19414
 * http://core.trac.wordpress.org/browser/trunk/wp-login.php?rev=19414
 */

global $wp_version, $Password_Protected_Babypad, $error;

/**
 * WP Shake JS
 */
if ( ! function_exists( 'wp_shake_js' ) ) {
  function wp_shake_js() {
    if ( wp_is_mobile() ) {
      return;
    }
    ?>
    <script type="text/javascript">
    addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
    function s(id,pos){g(id).left=pos+'px';}
    function g(id){return document.getElementById(id).style;}
    function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
    addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
    </script>
    <?php
  }
}

/**
 * @since 3.7.0
 */
if ( ! function_exists( 'wp_login_viewport_meta' ) ) {
  function wp_login_viewport_meta() {
    ?>
    <meta name="viewport" content="width=device-width" />
    <?php
  }
}

nocache_headers();
header( 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );

// Set a cookie now to see if they are supported by the browser.
setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN );
if ( SITECOOKIEPATH != COOKIEPATH ) {
  setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN );
}

// If cookies are disabled we can't log in even with a valid password.
if ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
  $Password_Protected_Babypad->errors->add( 'test_cookie', __( "<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress.", 'password-protected-babypad' ) );
}

// Shake it!
$shake_error_codes = array( 'empty_password', 'incorrect_password' );
if ( $Password_Protected_Babypad->errors->get_error_code() && in_array( $Password_Protected_Babypad->errors->get_error_code(), $shake_error_codes ) ) {
  add_action( 'password_protected_babypad_login_head', 'wp_shake_js', 12 );
}

// Obey privacy setting
add_action( 'password_protected_babypad_login_head', 'wp_no_robots' );

add_action( 'password_protected_babypad_login_head', 'wp_login_viewport_meta' );

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>

<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<title><?php echo apply_filters( 'password_protected_babypad_wp_title', __('クラウドログイン ‹ ').get_bloginfo( 'name' ).__(' — ベビーパッド') ); ?></title>

<?php

if ( version_compare( $wp_version, '3.9-dev', '>=' ) ) {
  wp_admin_css( 'login', true );
} else {
  wp_admin_css( 'wp-admin', true );
  wp_admin_css( 'colors-fresh', true );
}

?>

<style type="text/css" media="screen">
#login_error, .login .message, #loginform { margin-bottom: 20px; }
</style>

<?php

if ( wp_is_mobile() ) {
  ?>
  <meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
  <style type="text/css" media="screen">
  .login form, .login .message, #login_error { margin-left: 0px; }
  .login #nav, .login #backtoblog { margin-left: 8px; }
  #login { padding: 20px 0; }
  .login.login-password-protected-babypad h1 { background-image: none; width: auto; text-indent: 0; height: auto; margin-top: 80px; }
  .login.login-password-protected-babypad form { background-color: rgba(255, 255, 255, .3); }
  .login.login-password-protected-babypad form p.submit .button-primary { background-color: #eb748d !important; border-color: #eb748d; box-shadow: none!important; color: #fff; text-decoration: none; text-shadow: none!important; }
  input#password_protected_babypad_pass:focus { border-color: #F0A59D!important; box-shadow: 0 0 2px rgba(240,165,157,.8); }
  </style>
  <?php
}

do_action( 'login_enqueue_scripts' );
do_action( 'password_protected_babypad_login_head' );

?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/yakuhanjp@3.2.0/dist/css/yakuhanrp.min.css">
<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c:400,500,700&display=swap" rel="stylesheet">

</head>
<body class="login login-password-protected-babypad login-action-password-protected-babypad-login wp-core-ui">

<div id="login">
  <h1><?php bloginfo( 'name' ); ?></h1>

    <?php do_action( 'password_protected_babypad_login_messages' ); ?>
    <?php do_action( 'password_protected_babypad_before_login_form' ); ?>

  <form name="loginform" id="loginform" action="<?php echo esc_url( $Password_Protected_Babypad->login_url() ); ?>" method="post">
    <p>
      <label for="password_protected_babypad_pass"><?php echo apply_filters( 'password_protected_babypad_login_password_title', __( 'パスワードを入力してください(半角数値)', 'password-protected-babypad' ) ); ?><br />
      <input type="tel" name="password_protected_babypad_pwd" id="password_protected_babypad_pass" class="input" value="" size="20" tabindex="20" /></label>
    </p>

    <?php if ( $Password_Protected_Babypad->allow_remember_me() ) : ?>
      <p class="forgetmenot">
        <label for="password_protected_babypad_rememberme"><input name="password_protected_babypad_rememberme" type="checkbox" id="password_protected_babypad_rememberme" value="1" tabindex="90" /> <?php esc_attr_e( 'Remember Me' ); ?></label>
      </p>
    <?php endif; ?>

    <p class="submit">
      <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Log In' ); ?>" tabindex="100" />
      <input type="hidden" name="testcookie" value="1" />
      <input type="hidden" name="password-protected-babypad" value="login" />
      <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $_REQUEST['redirect_to'] ); ?>" />
    </p>
  </form>

  <?php do_action( 'password_protected_babypad_after_login_form' ); ?>

</div>

<script type="text/javascript">
try{document.getElementById('password_protected_babypad_pass').focus();}catch(e){}
if(typeof wpOnload=='function')wpOnload();
</script>

<?php do_action( 'login_footer' ); ?>

<div class="clear"></div>

</body>
</html>

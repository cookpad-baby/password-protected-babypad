<?php

/**
 * @package     Password Protected for Babypad
 * @subpackage  Admin Caching
 *
 * @since  2.1
 */

class Password_Protected_Babypad_Admin_Caching {

	/**
	 * Plugin
	 *
	 * @since  2.1
	 *
	 * @var  Password_Protected_Babypad|null
	 */
	private $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  2.1
	 *
	 * @internal  Private. This class should only be instantiated once by the plugin.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;

		add_action( 'admin_init', array( $this, 'cache_settings_info' ) );

	}

	/**
	 * Cache Settings Info
	 *
	 * Displays information on the settings page for helping
	 * to configure Password Protected for Babypad to work with caching setups.
	 *
	 * @since  2.1
	 */
	public function cache_settings_info() {

		// Caching Section
		add_settings_section(
			'password_protected_babypad_compat_caching',
			__( 'Caching', 'password-protected-babypad' ),
			array( $this, 'section_caching' ),
			'password-protected-babypad-compat'
		);

		// Cookies
		add_settings_field(
			'password_protected_babypad_compat_caching_cookie',
			__( 'Cookie Name', 'password-protected-babypad' ),
			array( $this, 'field_cookies' ),
			'password-protected-babypad-compat',
			'password_protected_babypad_compat_caching'
		);

		// WP Engine Hosting
		if ( $this->test_wp_engine() ) {

			add_settings_field(
				'password_protected_babypad_compat_caching_wp_engine',
				__( 'WP Engine Hosting', 'password-protected-babypad' ),
				array( $this, 'field_wp_engine' ),
				'password-protected-babypad-compat',
				'password_protected_babypad_compat_caching'
			);

		}

		// W3 Total Cache
		if ( $this->test_w3_total_cache() ) {

			add_settings_field(
				'password_protected_babypad_compat_caching_w3_total_cache',
				__( 'W3 Total Cache', 'password-protected-babypad' ),
				array( $this, 'field_w3_total_cache' ),
				'password-protected-babypad-compat',
				'password_protected_babypad_compat_caching'
			);

		}

	}

	/**
	 * Caching Section
	 *
	 * @since  2.1
	 */
	public function section_caching() {

		echo '<p>' . __( 'Password Protected for Babypad does not always work well with sites that use caching.', 'password-protected-babypad' ) . '<br />
			' . __( 'If your site uses a caching plugin or your web hosting uses server-side caching, you may need to configure your caching setup to disable caching for the Password Protected for Babypad cookie:', 'password-protected-babypad' ) . '</p>';

	}

	/**
	 * Password Protection Status Field
	 *
	 * @since  2.1
	 */
	public function field_cookies() {

		echo '<p><code>' . esc_html( $this->plugin->cookie_name() ) . '</code></p>';

	}

	/**
	 * WP Engine Hosting
	 *
	 * @since  2.1
	 */
	public function field_wp_engine() {

		echo '<p>' . __( 'We have detected your site may be running on WP Engine hosting.', 'password-protected-babypad' ) . '<br />
			' . __( 'In order for Password Protected for Babypad to work with WP Engine\'s caching configuration you must ask them to disable caching for the Password Protection for Babypad cookie.', 'password-protected-babypad' ) . '</p>';

	}

	/**
	 * W3 Total Cache Plugin
	 *
	 * @since  2.1
	 */
	public function field_w3_total_cache() {

		echo '<p>' . __( 'It looks like you may be using the W3 Total Cache plugin?', 'password-protected-babypad' ) . '<br />
			' . __( 'In order for Password Protected for Babypad to work with W3 Total Cache you must disable caching when the Password Protection for Babypad cookie is set.', 'password-protected-babypad' ) . ' 
			' . sprintf( __( 'You can adjust the cookie settings for W3 Total Cache under <a href="%s">Performance > Page Cache > Advanced > Rejected Cookies</a>.', 'password-protected-babypad' ), admin_url( '/admin.php?page=w3tc_pgcache#advanced' ) ) . '</p>';

	}

	/**
	 * Test: WP Engine
	 *
	 * @since  2.1
	 *
	 * @return  boolean
	 */
	private function test_wp_engine() {

		return ( function_exists( 'is_wpe' ) && is_wpe() ) || ( function_exists( 'is_wpe_snapshot' ) && is_wpe_snapshot() );

	}

	/**
	 * Test: W3 Total Cache
	 *
	 * @since  2.1
	 *
	 * @return  boolean
	 */
	private function test_w3_total_cache() {

		return defined( 'W3TC' ) && W3TC;

	}

}

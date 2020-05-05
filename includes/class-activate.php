<?php
/**
 * Plugin activation class.
 *
 * This file must not be namespaced.
 *
 * @package    WP_Themes
 * @subpackage Includes
 *
 * @since      1.0.0
 */

// Stop if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Plugin activation class.
 *
 * @since  1.0.0
 * @access public
 */
class WP_Themes_Activate {

	/**
	 * Instance of the class
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object Returns the instance.
	 */
	public static function instance() {

		// Varialbe for the instance to be used outside the class.
		static $instance = null;

		if ( is_null( $instance ) ) {

			// Set variable for new instance.
			$instance = new self;

			// Activation function
			$instance->activate();

		}

		// Return the instance.
		return $instance;

	}

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void Constructor method is empty.
	 *              Change to `self` if used.
	 */
	public function __construct() {}

	/**
	 * Fired during plugin activation.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function activate() {}

}

/**
 * Put an instance of the class into a function
 *
 * @since  1.0.0
 * @access public
 * @return object Returns an instance of the class.
 */
function wptp_activate() {

	return WP_Themes_Activate::instance();

}
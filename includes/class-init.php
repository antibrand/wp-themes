<?php
/**
 * The core plugin class
 *
 * @package    WP_Themes
 * @subpackage Includes
 *
 * @since      1.0.0
 */

namespace WP_Themes\Includes;

// Stop if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Begin the core functionality of the plugin.
 *
 * @since  1.0.0
 * @access public
 */
class Init {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
    public function __construct() {

		// Add the theme install page.
        add_action( 'admin_menu', [ $this, 'theme_install_page' ] );
	}

	/**
	 * Add the theme install page
	 *
	 * Uses the universal slug partial for admin pages. Set this
	 * slug in the core plugin file.
	 *
	 * Adds a contextual help section.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function theme_install_page() {

		$this->theme_install_page_help = add_submenu_page(
			'themes.php',
			__( 'Install WordPress Themes' ),
			__( 'WordPress Themes' ),
			'install_themes',
			WPTP_ADMIN_SLUG,
			[ $this, 'theme_install_page_output' ]
		);

		// Add content to the Help tab.
		add_action( 'load-' . $this->theme_install_page_help, [ $this, 'theme_install_page_help' ] );

	}

	/**
	 * Get output of the about page for the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function theme_install_page_output() {

		require WPTP_PATH . 'admin/install-wp-themes.php';

	}

	/**
	 * Add tabs to the about page contextual help section.
	 *
	 * @since      1.0.0
	 */
	public function theme_install_page_help() {

		// Add to the about page.
		$screen = get_current_screen();
		if ( $screen->id != $this->theme_install_page_help ) {
			return;
		}

		// More information tab.
		$screen->add_help_tab( [
			'id'       => 'help_plugin_info',
			'title'    => __( 'More Information', WPTP_DOMAIN ),
			'content'  => null,
			'callback' => [ $this, 'theme_install_page_info' ]
		] );

		// Add a help sidebar.
		$screen->set_help_sidebar(
			$this->theme_install_page_sidebar()
		);

	}

	/**
	 * Get more information help tab content.
	 *
	 * @since      1.0.0
	 */
	public function theme_install_page_info() {

		include_once WPTP_PATH . 'admin/help/theme-install-info.php';

	}

	/**
	 * The about page contextual tab sidebar content.
	 *
	 * @since      1.0.0
	 */
	public function theme_install_page_sidebar() {

		$html  = sprintf( '<h4>%1s</h4>', __( '', WPTP_DOMAIN ) );
		$html .= sprintf(
			'<p>%1s</p>',
			__( '', WPTP_DOMAIN )
		);
		$html .= sprintf(
			'<p>%1s</p>',
			__( '', WPTP_DOMAIN )
		);
		$html .= sprintf(
			'<p>%1s</p>',
			__( '', WPTP_DOMAIN )
		);

		return $html;

	}

}

// Run the class.
$install_wp_themes = new Init();
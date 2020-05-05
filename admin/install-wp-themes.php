<?php


require( ABSPATH . 'wp-admin/includes/theme-install.php' );

$tab = '';
wp_reset_vars( array( 'tab' ) );

if ( ! current_user_can('install_themes') )
	wp_die( __( 'Sorry, you are not allowed to install themes on this site.' ) );

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'theme-install.php' ) );
	exit();
}

$title       = __( 'Install WordPress Themes' );
$parent_file = 'themes.php';

$installed_themes = search_theme_directories();

if ( false === $installed_themes ) {
	$installed_themes = array();
}

foreach ( $installed_themes as $k => $v ) {
	if ( false !== strpos( $k, '/' ) ) {
		unset( $installed_themes[ $k ] );
	}
}

wp_localize_script( 'theme', '_wpThemeSettings', array(
	'themes'   => false,
	'settings' => array(
		'isInstall'  => true,
		'canInstall' => current_user_can( 'install_themes' ),
		'installURI' => current_user_can( 'install_themes' ) ? self_admin_url( 'theme-install.php' ) : null,
		'adminUrl'   => parse_url( self_admin_url(), PHP_URL_PATH )
	),
	'l10n' => array(
		'addNew'              => __( 'Add New Theme' ),
		'search'              => __( 'Search Themes' ),
		'searchPlaceholder'   => __( 'Search themes...' ), // placeholder (no ellipsis)
		'upload'              => __( 'Upload Theme' ),
		'back'                => __( 'Back' ),
		'error'               => sprintf(
			/* translators: %s: support forums URL */
			__( 'An unexpected error occurred. Something may be wrong with wordpress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
			__( 'https://wordpress.org/support/' )
		),
		'tryAgain'            => __( 'Try Again' ),
		'themesFound'         => __( 'Number of Themes found: %d' ),
		'noThemesFound'       => __( 'No themes found. Try a different search.' ),
		'collapseSidebar'     => __( 'Collapse Sidebar' ),
		'expandSidebar'       => __( 'Expand Sidebar' ),
		/* translators: accessibility text */
		'selectFeatureFilter' => __( 'Select one or more Theme features to filter by' ),
	),
	'installedThemes' => array_keys( $installed_themes ),
) );

wp_enqueue_script( 'theme' );
wp_enqueue_script( 'updates' );

if ( $tab ) {
	/**
	 * Fires before each of the tabs are rendered on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme installation tab. Possible values are 'dashboard', 'search', 'upload',
	 * 'featured', 'new', or 'updated'.
	 *
	 * @since 2.8.0
	 */
	do_action( "install_themes_pre_{$tab}" );
}
?>
<div class="wrap">
	<h1><?php echo esc_html( $title ); ?></h1>

	<hr class="wp-header-end">

	<h2 class="screen-reader-text hide-if-no-js"><?php _e( 'Filter themes list' ); ?></h2>

	<div class="wp-filter hide-if-no-js">
		<div class="filter-count">
			<span class="count theme-count"></span>
		</div>

		<ul class="filter-links">
			<!-- Featured tab has been removed -->
			<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="favorites"><?php _ex( 'Favorites', 'themes' ); ?></a></li>
		</ul>

		<button type="button" class="button drawer-toggle" aria-expanded="false"><?php _e( 'Feature Filter' ); ?></button>

		<form class="search-form"></form>

		<div class="favorites-form">
			<?php
			$action = 'save_wporg_username_' . get_current_user_id();
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), $action ) ) {
				$user = isset( $_GET['user'] ) ? wp_unslash( $_GET['user'] ) : get_user_option( 'wporg_favorites' );
				update_user_meta( get_current_user_id(), 'wporg_favorites', $user );
			} else {
				$user = get_user_option( 'wporg_favorites' );
			}
			?>
			<p class="install-help"><?php _e( 'If you have marked themes as favorites on wordpress.org, you can browse them here.' ); ?></p>

			<p>
				<label for="wporg-username-input"><?php _e( 'Your wordpress.org username:' ); ?></label>
				<input type="hidden" id="wporg-username-nonce" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( $action ) ); ?>" />
				<input type="search" id="wporg-username-input" value="<?php echo esc_attr( $user ); ?>" />
				<input type="button" class="button favorites-form-submit" value="<?php esc_attr_e( 'Get Favorites' ); ?>" />
			</p>
		</div>

		<div class="filter-drawer">
			<div class="buttons">
				<button type="button" class="apply-filters button"><?php _e( 'Apply Filters' ); ?><span></span></button>
				<button type="button" class="clear-filters button" aria-label="<?php esc_attr_e( 'Clear current filters' ); ?>"><?php _e( 'Clear' ); ?></button>
			</div>
		<?php
		$feature_list = get_theme_feature_list( false ); // Use the core list, rather than the .org API, due to inconsistencies and to ensure tags are translated.
		foreach ( $feature_list as $feature_name => $features ) {
			echo '<fieldset class="filter-group">';
			$feature_name = esc_html( $feature_name );
			echo '<legend>' . $feature_name . '</legend>';
			echo '<div class="filter-group-feature">';
			foreach ( $features as $feature => $feature_name ) {
				$feature = esc_attr( $feature );
				echo '<input type="checkbox" id="filter-id-' . $feature . '" value="' . $feature . '" /> ';
				echo '<label for="filter-id-' . $feature . '">' . $feature_name . '</label>';
			}
			echo '</div>';
			echo '</fieldset>';
		}
		?>
			<div class="buttons">
				<button type="button" class="apply-filters button"><?php _e( 'Apply Filters' ); ?><span></span></button>
				<button type="button" class="clear-filters button" aria-label="<?php esc_attr_e( 'Clear current filters' ); ?>"><?php _e( 'Clear' ); ?></button>
			</div>
			<div class="filtered-by">
				<span><?php _e( 'Filtering by:' ); ?></span>
				<div class="tags"></div>
				<button type="button" class="button-link edit-filters"><?php _e( 'Edit Filters' ); ?></button>
			</div>
		</div>
	</div>
	<h2 class="screen-reader-text hide-if-no-js"><?php _e( 'Themes list' ); ?></h2>
	<div class="theme-browser content-filterable"></div>
	<div class="theme-install-overlay wp-full-overlay expanded"></div>

	<p class="no-themes"><?php _e( 'No themes found. Try a different search.' ); ?></p>
	<span class="spinner"></span>

<?php
if ( $tab ) {
	/**
	 * Fires at the top of each of the tabs on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme installation tab. Possible values are 'dashboard', 'search', 'upload',
	 * 'featured', 'new', or 'updated'.
	 *
	 * @since 2.8.0
	 *
	 * @param int $paged Number of the current page of results being viewed.
	 */
	do_action( "install_themes_{$tab}", $paged );
}
?>
</div>

<script id="tmpl-theme" type="text/template">
	<# if ( data.screenshot_url ) { #>
		<figure class="theme-cover-image">
			<img src="{{ data.screenshot_url }}" alt="" />
		</figure>
	<# } else { #>
		<div class="theme-cover-image blank"></div>
	<# } #>
	<span class="more-details"><?php _ex( 'Details &amp; Preview', 'theme' ); ?></span>
	<div class="theme-author">
		<?php
		/* translators: %s: Theme author name */
		printf( __( 'By %s' ), '{{ data.author }}' );
		?>
	</div>

	<div class="theme-id-container">
		<h3 class="theme-name">{{ data.name }}</h3>

		<div class="theme-actions">
			<# if ( data.installed ) { #>
				<?php
				/* translators: %s: Theme name */
				$aria_label = sprintf( _x( 'Activate %s', 'theme' ), '{{ data.name }}' );
				?>
				<# if ( data.activate_url ) { #>
					<a class="button button-primary activate" href="{{ data.activate_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Activate' ); ?></a>
				<# } #>
				<# if ( data.customize_url ) { #>
					<a class="button load-customize" href="{{ data.customize_url }}"><?php _e( 'Live Preview' ); ?></a>
				<# } else { #>
					<button class="button preview install-theme-preview"><?php _e( 'Preview' ); ?></button>
				<# } #>
			<# } else { #>
				<?php
				/* translators: %s: Theme name */
				$aria_label = sprintf( __( 'Install %s' ), '{{ data.name }}' );
				?>
				<a class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}" href="{{ data.install_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Install' ); ?></a>
				<button class="button preview install-theme-preview"><?php _e( 'Preview' ); ?></button>
			<# } #>
		</div>
	</div>

	<# if ( data.installed ) { #>
		<div class="notice notice-success notice-alt"><p><?php _ex( 'Installed', 'theme' ); ?></p></div>
	<# } #>
</script>

<script id="tmpl-theme-preview" type="text/template">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<button class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
			<button class="previous-theme"><span class="screen-reader-text"><?php _ex( 'Previous', 'Button label for a theme' ); ?></span></button>
			<button class="next-theme"><span class="screen-reader-text"><?php _ex( 'Next', 'Button label for a theme' ); ?></span></button>
			<# if ( data.installed ) { #>
				<a class="button button-primary activate" href="{{ data.activate_url }}"><?php _e( 'Activate' ); ?></a>
			<# } else { #>
				<a href="{{ data.install_url }}" class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}"><?php _e( 'Install' ); ?></a>
			<# } #>
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<div class="install-theme-info">
				<h3 class="theme-name">{{ data.name }}</h3>
					<span class="theme-by">
						<?php
						/* translators: %s: Theme author name */
						printf( __( 'By %s' ), '{{ data.author }}' );
						?>
					</span>

					<figure>
						<img class="theme-cover-image" src="{{ data.screenshot_url }}" alt="" />
					</figure>

					<div class="theme-details">
						<# if ( data.rating ) { #>
							<div class="theme-rating">
								{{{ data.stars }}}
								<span class="num-ratings">({{ data.num_ratings }})</span>
							</div>
						<# } else { #>
							<span class="no-rating"><?php _e( 'This theme has not been rated yet.' ); ?></span>
						<# } #>
						<div class="theme-version">
							<?php
							/* translators: %s: Theme version */
							printf( __( 'Version: %s' ), '{{ data.version }}' );
							?>
						</div>
						<div class="theme-description">{{{ data.description }}}</div>
					</div>
				</div>
			</div>
			<div class="wp-full-overlay-footer">
				<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar' ); ?>">
					<span class="collapse-sidebar-arrow"></span>
					<span class="collapse-sidebar-label"><?php _e( 'Collapse' ); ?></span>
				</button>
			</div>
		</div>
		<div class="wp-full-overlay-main">
		<iframe src="{{ data.preview_url }}" title="<?php esc_attr_e( 'Preview' ); ?>"></iframe>
	</div>
</script>

<?php
wp_print_request_filesystem_credentials_modal();
wp_print_admin_notice_templates();
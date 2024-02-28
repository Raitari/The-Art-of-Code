<?php
/**
 * Theme About Page
 *
 * @package OutWit Lite
 * @since 1.0
 */

function outwit_lite_admin_plugin_notice() {
    
    $screen = get_current_screen();
    
    if ( ! empty( $screen->base ) && 'appearance_page_outwit-lite-theme' === $screen->base ) {
        return false;
    }
    ?>
    <div class="notice notice-info is-dismissible outwit-lite-admin-notice">
        <div class="outwit-lite-admin-notice-wrapper">
            <h2><?php esc_html_e( 'OutWit', 'outwit-lite' ); ?></h2>
            <p><?php esc_html_e( 'Get your hands on the WordPress Full Site Editing features. Start building your website with advanced block patterns and custom blocks! Get additional 27+ Pre-Designed Block Patterns, 25 FSE Templates, and 12 Template Parts  that are highly customizable and fully responsive.', 'outwit-lite' ); ?></p>
            
            <a target="_blank" class="button-primary button green" href="<?php echo esc_url( 'https://catchthemes.com/themes/outwit/'); ?>"><?php esc_html_e( 'Get OutWit', 'outwit-lite' ); ?></a>
            
            <a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=outwit-lite-theme' ) ); ?>" ><?php esc_html_e( 'Theme Info', 'outwit-lite' ); ?></a>
        </div>
    </div>
    <?php
}
add_action( 'admin_notices', 'outwit_lite_admin_plugin_notice' );

function outwit_lite_theme_page_admin_style( $hook ) {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_enqueue_style(
			'outwit-lite-theme-admin-style',
			get_theme_file_uri( 'assets/css/about-admin.css' ),
			array(),
			$version_string
		);
}
add_action( 'admin_enqueue_scripts', 'outwit_lite_theme_page_admin_style' );

/**
 * Add theme page
 */
function outwit_lite_menu() {
	add_theme_page( esc_html__( 'OutWit Lite', 'outwit-lite' ), esc_html__( 'OutWit Lite', 'outwit-lite' ), 'edit_theme_options', 'outwit-lite-theme', 'outwit_lite_theme_page_display' );
}
add_action( 'admin_menu', 'outwit_lite_menu' );

/**
 * Display About page
 */
function outwit_lite_theme_page_display() {
	$theme = wp_get_theme();
	
	if ( is_child_theme() ) {
		$theme = wp_get_theme()->parent();
	}
	?>
	
	<div id="welcome-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-header">
				<h2><?php echo esc_html( $theme->Name ); ?></h2>
				<p><?php esc_html_e( 'Free Full Site Editing WordPress Theme', 'outwit-lite' ); ?></p>
			</div>
			
			<div class="welcome-panel-column-container">
				<div class="container-wrap">
					<div class="welcome-panel-column two-columns">
						<!-- <div class="welcome-panel-icon-pages"></div> -->
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'Getting Started with OutWit Lite!', 'outwit-lite' ); ?></h3>
							<p><?php esc_html_e( 'Awesome! OutWit Lite has been installed and activated successfully. Now, you can start building your dream website with a wide range of highly-customizable block patterns, templates, and template parts available in this astounding theme.', 'outwit-lite' ); ?></p>
							<a target="_blank" href="https://catchthemes.com/themes/outwit-lite/#theme-instructions"><?php esc_html_e( 'Theme instructions', 'outwit-lite' ); ?></a>
						</div>
					</div>
					
					<div class="welcome-panel-column two-columns">
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'More Features with OutWit Theme', 'outwit-lite' ); ?></h3>
							<p><?php esc_html_e( 'To get more beautiful block patterns and templates, we recommend you upgrade to OutWit. With the pro theme installed, get more options, blocks, block patterns, templates and template parts.', 'outwit-lite' ); ?></p>
							<a target="_blank" class="button green button-primary button-hero green" href="https://catchthemes.com/themes/outwit/"><?php esc_html_e( 'Buy Outwit', 'outwit-lite' ); ?></a>
						</div>
					</div>
					
				</div>
				<div class="sidebar">
					<div class="welcome-panel-column important-links">
					<!-- <div class="welcome-panel-icon-pages"></div> -->
					<div class="welcome-panel-column-content">
						<h3><?php esc_html_e( 'Important Links', 'outwit-lite' ); ?></h3>
						<a target="_blank" href="<?php echo esc_url( $theme->get( 'ThemeURI' ) ); ?>"><?php esc_html_e( 'Theme Info', 'outwit-lite' ); ?></a>
						<a target="_blank" href="https://fse.catchthemes.com/outwit-lite/"><?php esc_html_e( 'View Demo', 'outwit-lite' ); ?></a>
						<a target="_blank" href="<?php echo esc_url( 'https://catchthemes.com/fse-faq' ); ?>"><?php esc_html_e( 'FSE FAQs', 'outwit-lite' ); ?></a>
						<a  target="_blank" href="<?php echo esc_url( $theme->get( 'ThemeURI' ) . '/#changelog' ); ?>"><?php esc_html_e( 'Change log', 'outwit-lite' ); ?></a>
						<a target="_blank" href="<?php echo esc_url( 'https://catchthemes.com/support-forum/forum/full-site-editing/' ); ?>"><?php esc_html_e( 'Support', 'outwit-lite' ); ?></a>
					</div>
				</div>
				
				<div class="welcome-panel-column review">
					<!-- <div class="welcome-panel-icon-pages"></div> -->
					<div class="welcome-panel-column-content">
						<h3><?php esc_html_e( 'Leave us a review', 'outwit-lite' ); ?></h3>
						<p><?php esc_html_e( 'Loved OutWit Lite? Feel free to leave your feedback. Your opinion helps us reach more audiences!', 'outwit-lite' ); ?></p>
						<a href="https://wordpress.org/support/theme/outwit-lite/reviews/" class="button button-primary button-hero" style="text-decoration: none;" target="_blank"><?php esc_html_e( 'Review', 'outwit-lite' ); ?></a>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

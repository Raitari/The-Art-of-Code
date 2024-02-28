<?php
/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package Outwit Lite
 * @since 1.0
 */

if ( function_exists( 'register_block_style' ) ) {
	/**
	 * Register block styles.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	function outwit_lite_register_block_styles() {

		//Wp Block Padding Zero
		register_block_style(
			'core/group',
			array(
				'name'  => 'outwit-lite-padding-0',
				'label' => esc_html__( 'No Padding', 'outwit-lite' ),
			)
		);

		//Wp Block Post Author Style
		register_block_style(
			'core/post-author',
			array(
				'name'  => 'outwit-lite-post-author-card',
				'label' => esc_html__( 'Theme Style', 'outwit-lite' ),
			)
		);

		//Wp Block Button Style
		register_block_style(
			'core/button',
			array(
				'name'         => 'outwit-lite-button',
				'label'        => esc_html__( 'Plain', 'outwit-lite' ),
			)
		);

		//Post Comments Style
		register_block_style(
			'core/post-comments',
			array(
				'name'         => 'outwit-lite-post-comments',
				'label'        => esc_html__( 'Theme Style', 'outwit-lite' ),
			)
		);

		//Latest Comments Style
		register_block_style(
			'core/latest-comments',
			array(
				'name'         => 'outwit-lite-latest-comments',
				'label'        => esc_html__( 'Theme Style', 'outwit-lite' ),
			)
		);


		//Wp Block Table Style
		register_block_style(
			'core/table',
			array(
				'name'         => 'outwit-lite-wp-table',
				'label'        => esc_html__( 'Theme Style', 'outwit-lite' ),
			)
		);


		//Wp Block Pre Style
		register_block_style(
			'core/preformatted',
			array(
				'name'         => 'outwit-lite-wp-preformatted',
				'label'        => esc_html__( 'Theme Style', 'outwit-lite' ),
			)
		);

		//Wp Block Verse Style
		register_block_style(
			'core/verse',
			array(
				'name'         => 'outwit-lite-wp-verse',
				'label'        => esc_html__( 'Theme Style', 'outwit-lite' ),
			)
		);
	}
	add_action( 'init', 'outwit_lite_register_block_styles' );
}

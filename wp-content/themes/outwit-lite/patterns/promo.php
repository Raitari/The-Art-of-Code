<?php
 /**
  * Title: Promo Section
  * Slug: outwit-lite/promo	
  * Categories: outwit-lite, featured
  */
?>
<!-- wp:group {"align":"full","className":"promo-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull promo-section">
	<!-- wp:cover {"url":"<?php echo esc_url( get_parent_theme_file_uri( '/assets/images/promotional-bg.jpg' ) ); ?>","id":22679,"dimRatio":0,"align":"full"} -->
	<div class="wp-block-cover alignfull">
		<span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span>
		<img class="wp-block-cover__image-background wp-image-22679" alt="" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/images/promotional-bg.jpg' ) ); ?>" data-object-fit="cover"/>
		<div class="wp-block-cover__inner-container">
			<!-- wp:group {"className":"promotional-content","layout":{"type":"constrained"}} -->
			<div class="wp-block-group promotional-content">
				<!-- wp:paragraph {"align":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e('Promotional Content','outwit-lite');?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"textAlign":"center","fontSize":"section-title"} -->
				<h2 class="has-text-align-center has-section-title-font-size"><?php esc_html_e('Autumn 2023 Collection','outwit-lite');?></h2>
				<!-- /wp:heading -->
				<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button">
						<a class="wp-block-button__link wp-element-button" href="#"><?php esc_html_e('Shop now','outwit-lite');?></a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
	</div>
	<!-- /wp:cover -->
</div>
<!-- /wp:group -->

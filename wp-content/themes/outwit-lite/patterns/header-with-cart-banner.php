<?php
 /**
  * Title: Header With Cart Banner
  * Slug: outwit-lite/header-with-cart-banner	
  * Categories: outwit-lite, header
  */
?>
<!-- wp:cover {"url":"<?php echo get_parent_theme_file_uri( '/assets/images/header-media.jpg' ); ?>","id":655,"dimRatio":0,"align":"full","className":"header-media"} -->
<div class="wp-block-cover alignfull header-media">
	<span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span>
	<img class="wp-block-cover__image-background wp-image-655" alt="" src="<?php echo get_parent_theme_file_uri( '/assets/images/header-media.jpg' ); ?>" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"className":"transparent-header header-with-banner","layout":{"type":"default"}} -->
		<div class="wp-block-group transparent-header header-with-banner">
			<!-- wp:group {"className":"header","layout":{"type":"constrained"}} -->
			<div class="wp-block-group header">
				<!-- wp:group {"align":"full","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
				<div class="wp-block-group alignfull">
					<!-- wp:group {"className":"nav-row","layout":{"type":"flex","flexWrap":"nowrap"}} -->
					<div class="wp-block-group nav-row">
						<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
						<div class="wp-block-group">
							<!-- wp:navigation {"ref":136,"layout":{"type":"flex","justifyContent":"left"}} /-->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
					<!-- wp:group {"className":"site-section","layout":{"type":"flex","flexWrap":"nowrap"}} -->
					<div class="wp-block-group site-section">
						<!-- wp:group {"className":"site-title-section"} -->
						<div class="wp-block-group site-title-section">
							<!-- wp:site-title /-->
							<!-- wp:site-tagline /-->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
					<!-- wp:group {"className":"right-section","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
					<div class="wp-block-group right-section">
						<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search products…","buttonText":"Search","buttonUseIcon":true,"query":{"post_type":"product"},"className":"search-product"} /-->
						<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search","buttonUseIcon":true,"className":"dummy-icon"} /-->
						<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only"} /-->
						<!-- wp:group {"className":"cart-row","layout":{"type":"flex","flexWrap":"nowrap"}} -->
						<div class="wp-block-group cart-row">
							<!-- wp:woocommerce/mini-cart {"addToCartBehaviour":"open_drawer","hasHiddenPrice":true} /-->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"header-media-content"} -->
			<div class="wp-block-columns alignwide are-vertically-aligned-center header-media-content">
				<!-- wp:column {"verticalAlignment":"center","className":"slider-content","layout":{"type":"default"}} -->
				<div class="wp-block-column is-vertically-aligned-center slider-content">
					<!-- wp:heading {"textAlign":"left","fontSize":"huge"} -->
					<h2 class="wp-block-heading has-text-align-left has-huge-font-size"><?php esc_html_e('denim Co.','outwit-lite');?></h2>
					<!-- /wp:heading -->
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button -->
						<div class="wp-block-button">
							<a class="wp-block-button__link wp-element-button"><?php esc_html_e('View More','outwit-lite');?></a>
						</div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
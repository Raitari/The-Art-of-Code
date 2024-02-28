<?php
 /**
  * Title: Header With Cart
  * Slug: outwit-lite/header-with-cart
  * Categories: outwit-lite, header
  */
?>
<!-- wp:group {"align":"full","layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group alignfull">
	<!-- wp:group {"align":"full","className":"header header-with-cart without-transparent","layout":{"inherit":true,"type":"constrained"}} -->
	<div class="wp-block-group alignfull header header-with-cart without-transparent">
		<!-- wp:group {"align":"full","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group alignfull">
			<!-- wp:group {"className":"nav-row","layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group nav-row">
				<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:navigation {"layout":{"type":"flex","justifyContent":"left"}} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"site-section","layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group site-section">
				<!-- wp:group {"className":"site-title-section","layout":{"type":"flex","orientation":"vertical"}} -->
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

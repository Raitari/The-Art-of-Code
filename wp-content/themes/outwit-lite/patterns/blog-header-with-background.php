<?php
 /**
  * Title: Blog Header With Background
  * Slug: outwit-lite/blog-header-with-background
  * Categories: outwit-lite, header
  */
?>


<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"}}},"className":"banner","layout":{"inherit":false}} -->
<div class="wp-block-group alignfull banner" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:cover {"overlayColor":"footer-bg-color","className":"is-dark"} -->
<div class="wp-block-cover is-dark"><span aria-hidden="true" class="wp-block-cover__background has-footer-bg-color-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
			<!-- wp:group {"layout":{"inherit":true,"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:group {"className":"alignwide"} -->
				<div class="wp-block-group alignwide">
					<!-- wp:heading {"textAlign":"center","level":1,"className":"wp-block-post-title"} -->
						<h1 class="has-text-align-center wp-block-post-title">
							<?php esc_html_e ( 'Recent From Blog', 'outwit-lite' ); ?>
						</h1>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
	</div>
	<!-- /wp:cover -->
</div>
<!-- /wp:group -->
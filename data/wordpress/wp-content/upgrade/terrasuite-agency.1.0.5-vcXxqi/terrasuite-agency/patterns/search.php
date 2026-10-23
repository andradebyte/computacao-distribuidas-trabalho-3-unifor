<?php
/**
 * Title: Search results
 * Slug: terrasuite-agency/search
 * Inserter: no
 * Description: The results of a site search, with the box carried over so a visitor can correct the wording.
 *
 * @package TerraSuiteAgency
 */

?>
<!-- wp:query-title {"type":"search","level":1} /-->

<!-- wp:search {"label":"<?php echo esc_attr__( 'Search this site', 'terrasuite-agency' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr__( 'Search this site', 'terrasuite-agency' ); ?>","buttonText":"<?php echo esc_attr__( 'Search', 'terrasuite-agency' ); ?>","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|50"}}}} /-->

<!-- wp:query {"queryId":3,"query":{"perPage":12,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->
<div class="wp-block-query">
	<!-- wp:post-template {"className":"ts-cards","layout":{"type":"grid","columnCount":3}} -->
		<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->
		<!-- wp:post-title {"level":2,"isLink":true,"fontSize":"x-large","style":{"spacing":{"margin":{"top":"0.35rem","bottom":"0.4rem"}}}} /-->
		<!-- wp:post-excerpt {"excerptLength":24,"moreText":"<?php echo esc_attr__( 'Continue reading', 'terrasuite-agency' ); ?>","fontSize":"small"} /-->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph {"textColor":"muted","fontSize":"large"} -->
		<p class="has-muted-color has-text-color has-large-font-size"><?php echo esc_html__( 'Nothing on the site matched those words. Try fewer of them, or a place name on its own.', 'terrasuite-agency' ); ?></p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->

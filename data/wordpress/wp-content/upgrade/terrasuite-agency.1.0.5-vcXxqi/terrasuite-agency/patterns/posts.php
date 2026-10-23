<?php
/**
 * Title: Posts
 * Slug: terrasuite-agency/posts
 * Inserter: no
 * Description: The list of posts used by the blog index, the archives and the fallback template.
 *
 * One copy, used by three templates, for the reason set out in header.php: a
 * block theme's .html is static markup that gettext never sees, so "Read the
 * guide" and "There is nothing here yet" were English on a Spanish site for
 * ever. They were also written for one agency's own website rather than for
 * whoever installs this theme.
 *
 * @package TerraSuiteAgency
 */

/*
 * The chips are a way of narrowing a short list, not a wall of every term the
 * site has ever used. Past a dozen they stop being navigation and become the
 * tallest thing on the page, so past a dozen they are not shown.
 */
$terrasuite_terms = (int) wp_count_terms(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
	)
);

if ( $terrasuite_terms > 1 && $terrasuite_terms <= 12 ) :
	?>
<!-- wp:categories {"showPostCounts":true,"className":"ts-chips","style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|50"}}}} /-->
	<?php
endif;
?>

<!-- wp:query {"queryId":1,"query":{"perPage":12,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->
<div class="wp-block-query">
	<!-- wp:post-template {"className":"ts-cards","layout":{"type":"grid","columnCount":3}} -->
		<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->
		<!-- wp:post-terms {"term":"category","fontSize":"small","textColor":"accent","style":{"spacing":{"margin":{"top":"0.9rem"}}}} /-->
		<!-- wp:post-title {"level":2,"isLink":true,"fontSize":"x-large","style":{"spacing":{"margin":{"top":"0.35rem","bottom":"0.4rem"}}}} /-->
		<!-- wp:post-excerpt {"excerptLength":24,"moreText":"<?php echo esc_attr__( 'Continue reading', 'terrasuite-agency' ); ?>","fontSize":"small"} /-->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph {"textColor":"muted"} -->
		<p class="has-muted-color has-text-color"><?php echo esc_html__( 'Nothing has been published here yet.', 'terrasuite-agency' ); ?></p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->

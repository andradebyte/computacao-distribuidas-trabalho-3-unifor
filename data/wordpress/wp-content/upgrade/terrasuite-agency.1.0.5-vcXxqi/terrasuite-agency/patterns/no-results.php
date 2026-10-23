<?php
/**
 * Title: Nothing found
 * Slug: terrasuite-agency/no-results
 * Inserter: no
 * Description: What a visitor sees when a page does not exist or a search finds nothing.
 *
 * Both cases get a search box rather than an apology and a dead end, because
 * on an estate agency site somebody arriving at a missing page is usually
 * looking at a property that has sold and is still worth keeping.
 *
 * @package TerraSuiteAgency
 */

?>
<!-- wp:paragraph {"textColor":"muted","fontSize":"large","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<p class="has-muted-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)"><?php echo esc_html__( 'The page you asked for is not here. It may have been moved, or it may have been something that has since been taken off the market. Try a search, or start again from the front page.', 'terrasuite-agency' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_attr__( 'Search this site', 'terrasuite-agency' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr__( 'Search this site', 'terrasuite-agency' ); ?>","buttonText":"<?php echo esc_attr__( 'Search', 'terrasuite-agency' ); ?>","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} /-->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
	<!-- wp:button {"className":"is-style-outline"} -->
	<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Back to the front page', 'terrasuite-agency' ); ?></a></div>
	<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

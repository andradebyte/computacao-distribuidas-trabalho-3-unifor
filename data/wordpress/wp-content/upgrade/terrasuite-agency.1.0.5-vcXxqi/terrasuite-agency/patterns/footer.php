<?php
/**
 * Title: Footer
 * Slug: terrasuite-agency/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Columns of links, grouped by what a visitor is trying to do.
 *
 * A PHP pattern rather than parts/footer.html for the reason set out in
 * header.php: a block theme's .html is static markup that gettext never sees,
 * so anything written in one is English on a Spanish site for ever.
 *
 * The links are filtered the same way the header's are: a column appears only
 * when at least one of its pages exists, so a site that has not created them
 * yet gets a footer of what it does have rather than eleven links to a 404.
 *
 * @package TerraSuite
 */

$terrasuite_columns = array(
	array(
		'heading' => __( 'Buying', 'terrasuite-agency' ),
		'links'   => array(
			array( 'property-search', '/property-search/', __( 'Property search', 'terrasuite-agency' ) ),
			array( 'map-search', '/map-search/', __( 'Search on a map', 'terrasuite-agency' ) ),
			array( 'register-your-requirements', '/register-your-requirements/', __( 'Register your requirements', 'terrasuite-agency' ) ),
			array( 'book-a-viewing', '/book-a-viewing/', __( 'Book a viewing', 'terrasuite-agency' ), 'bookings' ),
		),
	),
	array(
		'heading' => __( 'Selling and letting', 'terrasuite-agency' ),
		'links'   => array(
			array( 'free-valuation', '/free-valuation/', __( 'Free valuation', 'terrasuite-agency' ) ),
			array( 'help-and-advice', '/help-and-advice/', __( 'Advice', 'terrasuite-agency' ), 'knowledgebase' ),
			array( 'area-guides', '/area-guides/', __( 'Area guides', 'terrasuite-agency' ) ),
		),
	),
	array(
		'heading' => __( 'Your account', 'terrasuite-agency' ),
		'links'   => array(
			array( 'my-account', '/my-account/', __( 'My account', 'terrasuite-agency' ), 'client_accounts' ),
			array( 'my-shortlist', '/my-shortlist/', __( 'My shortlist', 'terrasuite-agency' ), 'shortlist' ),
			array( 'my-viewings', '/my-viewings/', __( 'My viewings', 'terrasuite-agency' ), 'bookings' ),
			array( 'ask-a-question', '/ask-a-question/', __( 'Ask a question', 'terrasuite-agency' ) ),
		),
	),
);

$terrasuite_live = array();

foreach ( $terrasuite_columns as $terrasuite_column ) {
	$terrasuite_kept = array();

	foreach ( $terrasuite_column['links'] as $terrasuite_link ) {
		if ( terrasuite_agency_link( $terrasuite_link[0], isset( $terrasuite_link[3] ) ? $terrasuite_link[3] : '' ) ) {
			$terrasuite_kept[] = $terrasuite_link;
		}
	}

	if ( $terrasuite_kept ) {
		$terrasuite_live[] = array(
			'heading' => $terrasuite_column['heading'],
			'links'   => $terrasuite_kept,
		);
	}
}

?>
<!-- wp:group {"className":"ts-footer","backgroundColor":"ink","layout":{"type":"constrained","contentSize":"1360px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-group ts-footer has-ink-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)">

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns">

<!-- wp:column {"width":"34%"} -->
<div class="wp-block-column" style="flex-basis:34%">
<!-- wp:site-title {"level":0} /-->
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html__( 'Buying, selling and letting. Talk to us about what you are trying to do and we will tell you honestly whether we can help.', 'terrasuite-agency' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<?php if ( $terrasuite_live ) : ?>
	<?php foreach ( $terrasuite_live as $terrasuite_column ) : ?>
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php echo esc_html( $terrasuite_column['heading'] ); ?></h3>
<!-- /wp:heading -->
<!-- wp:list {"fontSize":"small"} -->
<ul class="wp-block-list has-small-font-size"><?php foreach ( $terrasuite_column['links'] as $terrasuite_link ) : ?><!-- wp:list-item --><li><a href="<?php echo esc_url( $terrasuite_link[1] ); ?>"><?php echo esc_html( $terrasuite_link[2] ); ?></a></li><!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
	<?php endforeach; ?>
<?php else : ?>
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:page-list {"fontSize":"small"} /-->
</div>
<!-- /wp:column -->
<?php endif; ?>

</div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php
printf(
	/* translators: 1: current year, 2: site name. */
	esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'terrasuite-agency' ),
	esc_html( gmdate( 'Y' ) ),
	esc_html( get_bloginfo( 'name' ) )
);

/*
 * The credit line is a setting rather than a fixture, and is off unless the
 * site owner turns it on on the Designs screen. A theme on the WordPress.org
 * directory may not put its author's words in somebody else's footer, and an
 * agency that has paid for the software is entitled to a footer that says only
 * their own name. On our own sites we switch it on.
 */
/*
 * AND THE LINK IS READ OUT OF style.css, NOT WRITTEN HERE.
 *
 * A theme may show one front-facing credit link and it has to be the Theme URI
 * or the Author URI from style.css. This was hardcoded to https://terrasuite.uk/
 * while the Theme URI was https://terrasuite.uk/terrasuite-agency/ - the same site, a
 * different address, and the Themes Team asked for it on 24 September 2026.
 *
 * There is NO Theme URI header at all in 1.0.5. terrasuite.uk is behind a sign-in
 * until the theme and the free plugin are both published, and a reviewer cannot
 * be asked to take a gated page on trust, so the Themes Team asked for the
 * header to be dropped and added back once the page is live. The credit falls
 * through to the Author URI, which is a public site, and nothing else changes.
 *
 * Taking it from the header rather than repeating it means the two cannot drift
 * apart: restore the Theme URI and the credit follows it on its own.
 */
if ( terrasuite_agency_shows_credit() ) {
	$terrasuite_credit = wp_get_theme( get_template() )->get( 'ThemeURI' );

	if ( ! $terrasuite_credit ) {
		$terrasuite_credit = wp_get_theme( get_template() )->get( 'AuthorURI' );
	}

	if ( $terrasuite_credit ) {
		echo ' ';
		printf(
			'<a href="%s" rel="nofollow">%s</a>',
			esc_url( $terrasuite_credit ),
			esc_html__( 'Powered by TerraSuite.', 'terrasuite-agency' )
		);
	}
}
?></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

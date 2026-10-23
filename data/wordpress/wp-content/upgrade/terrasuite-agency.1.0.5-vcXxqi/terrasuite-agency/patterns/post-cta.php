<?php
/**
 * Title: After a post
 * Slug: terrasuite-agency/post-cta
 * Inserter: no
 * Description: A line at the foot of an article pointing at the search and the requirements form.
 *
 * Emits nothing unless those pages exist. It used to be two hardcoded links in
 * single.html, which meant two links to a 404 on any site that had not created
 * them - and, because a .html template is never put through gettext, two lines
 * of English on a translated site.
 *
 * @package TerraSuiteAgency
 */

$terrasuite_search = terrasuite_agency_page( 'property-search' );
$terrasuite_wants  = terrasuite_agency_page( 'register-your-requirements' );

if ( ! $terrasuite_search && ! $terrasuite_wants ) {
	return;
}

$terrasuite_parts = array();

if ( $terrasuite_search ) {
	$terrasuite_parts[] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( home_url( '/property-search/' ) ),
		esc_html__( 'see what we have available', 'terrasuite-agency' )
	);
}

if ( $terrasuite_wants ) {
	$terrasuite_parts[] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( home_url( '/register-your-requirements/' ) ),
		esc_html__( 'tell us what you are after and we will let you know the moment something matches', 'terrasuite-agency' )
	);
}

?>
<!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide" style="margin-top:var(--wp--preset--spacing--50)"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"fontSize":"small","textColor":"muted"} -->
<p class="has-muted-color has-text-color has-small-font-size"><?php
echo esc_html__( 'Looking for somewhere here?', 'terrasuite-agency' ) . ' ';

// Each link is escaped where it is built, just above.
echo wp_kses(
	implode( esc_html__( ', or ', 'terrasuite-agency' ), $terrasuite_parts ),
	array( 'a' => array( 'href' => array() ) )
);

echo '.';
?></p>
<!-- /wp:paragraph -->

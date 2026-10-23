<?php
/**
 * Title: Header
 * Slug: terrasuite-agency/header
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Site title, the navigation split by what a visitor came to do, and the valuation button.
 *
 * WHY THIS IS A PHP PATTERN AND NOT parts/header.html.
 *
 * A block theme's .html files are static markup. WordPress does not put them
 * through gettext and never will, so every word in one is English for ever
 * however complete the translation is. With the Spanish catalogue finished at
 * two and a half thousand strings, the only English left on a Spanish page was
 * in here: six navigation labels and a button, on every page of the site.
 *
 * A pattern is PHP, so its strings translate like everything else. The template
 * part is now one line pointing at this, which is exactly what Twenty
 * Twenty-Four and Twenty Twenty-Five do for the same reason.
 *
 * WHY THE LINKS ARE FILTERED.
 *
 * The navigation is split by what a visitor came to do, which is the point of
 * the theme, but it names pages an agency creates: a property search, a
 * valuation, area guides. On a site where none of them exist yet - a fresh
 * install, or the review copy on WordPress.org - a fixed list of six is six
 * links to a 404. So each is offered only once its page is there, and if none
 * of them are, the block falls back to core's page list, which is a working
 * menu of whatever the site does have.
 *
 * @package TerraSuite
 */

$terrasuite_nav = array(
	'property-search'  => array( __( 'Buying', 'terrasuite-agency' ), '/property-search/' ),
	'property-search#' => array( __( 'Renting', 'terrasuite-agency' ), '/property-search/?dept=rent' ),
	'free-valuation'   => array( __( 'Selling', 'terrasuite-agency' ), '/free-valuation/' ),
	'area-guides'      => array( __( 'Area guides', 'terrasuite-agency' ), '/area-guides/' ),
	'help-and-advice'  => array( __( 'Advice', 'terrasuite-agency' ), '/help-and-advice/', 'knowledgebase' ),
	'my-account'       => array( __( 'My account', 'terrasuite-agency' ), '/my-account/', 'client_accounts' ),
);

$terrasuite_links = array();

/*
 * WHAT THE AGENCY CHOSE, IF THEY CHOSE ANYTHING.
 *
 * The list above is a good first menu and a poor permanent one: it is what we
 * think an agency's menu should be, not what theirs is. TerraSuite's Menu
 * screen writes one, and an agency owner can reach that screen without
 * edit_theme_options, which is the whole reason it exists - editing the
 * navigation block means opening the site editor, and the site editor is not
 * something to hand somebody so they can rename "Buying".
 *
 * Nothing stored means nothing chosen, not an empty menu, so the computed list
 * still runs. The theme therefore works exactly as before on a site where
 * nobody has visited that screen, and on a site with no plugin at all.
 */
if ( function_exists( 'terrasuite_menu_items' ) ) {
	foreach ( terrasuite_menu_items() as $terrasuite_chosen ) {
		if ( ! empty( $terrasuite_chosen['label'] ) && ! empty( $terrasuite_chosen['url'] ) ) {
			$terrasuite_links[] = array( $terrasuite_chosen['label'], $terrasuite_chosen['url'] );
		}
	}
}

if ( ! $terrasuite_links ) {
	foreach ( $terrasuite_nav as $terrasuite_path => $terrasuite_item ) {
		if ( terrasuite_agency_link( rtrim( $terrasuite_path, '#' ), isset( $terrasuite_item[2] ) ? $terrasuite_item[2] : '' ) ) {
			$terrasuite_links[] = $terrasuite_item;
		}
	}
}

?>
<!-- wp:group {"className":"ts-header","layout":{"type":"constrained","contentSize":"1360px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-group ts-header" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
<!-- wp:group {"className":"ts-header__inner","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
<div class="wp-block-group ts-header__inner">

<!-- wp:site-title {"level":0} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"right"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-group">

<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<?php if ( $terrasuite_links ) : ?>
	<?php foreach ( $terrasuite_links as $terrasuite_item ) : ?>
<!-- wp:navigation-link {"label":"<?php echo esc_attr( $terrasuite_item[0] ); ?>","url":"<?php echo esc_attr( $terrasuite_item[1] ); ?>","kind":"custom","isTopLevelLink":true} /-->
	<?php endforeach; ?>
<?php else : ?>
<!-- wp:page-list /-->
<?php endif; ?>
<!-- /wp:navigation -->

<?php if ( terrasuite_agency_page( 'free-valuation' ) ) : ?>
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/free-valuation/"><?php echo esc_html__( 'Book a valuation', 'terrasuite-agency' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<?php endif; ?>

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

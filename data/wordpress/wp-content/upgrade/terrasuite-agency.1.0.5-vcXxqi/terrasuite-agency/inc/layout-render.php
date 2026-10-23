<?php
/**
 * Putting a layout on the page.
 *
 * Four jobs, and no more than four:
 *
 *   1. Write the layout onto the body as classes, so assets/layouts.css can
 *      rearrange what is already there. Nothing here rewrites content.
 *   2. Print the showcase carousel, for the layouts that have one, immediately
 *      before the front page content. It is printed as markup that already
 *      shows its first slide, so if the script never runs the page still has a
 *      property on it.
 *   3. Print the contact bar for the layouts that carry one.
 *   4. Print the browse-by-place and browse-by-type tiles for the layouts that
 *      lead with them, using the plugin's own shortcodes rather than a second
 *      copy of the same query.
 *
 * @package TerraSuiteAgency
 */

defined( 'ABSPATH' ) || exit;

/**
 * Write the current design onto the body.
 *
 * @param array $classes Body classes.
 * @return array
 */
function terrasuite_agency_body_class( $classes ) {
	$layout = terrasuite_agency_current_layout();

	// Only when the pair in force is actually one of the named designs. A site
	// that has chosen its own arrangement and its own colours is not running
	// Harbour, and saying so on the body would let a stylesheet written for
	// Harbour apply to it.
	$named = terrasuite_agency_named_pairing();

	if ( '' !== $named ) {
		$classes[] = 'ts-design-' . sanitize_html_class( $named );
	}

	$classes[] = 'ts-layout-' . sanitize_html_class( terrasuite_agency_current_layout_slug() );
	$classes[] = 'ts-hero-' . sanitize_html_class( $layout['hero'] );
	$classes[] = 'ts-cards-' . sanitize_html_class( $layout['cards'] );
	$classes[] = 'ts-header-' . sanitize_html_class( $layout['header'] );

	// AFTER the four above, not among them. Those four are written as one run
	// and read back as one run - a stylesheet targets them together and the
	// pack suite greps for exactly that sequence. Putting the colours in the
	// middle of it broke eight checks that were right to break.
	$classes[] = 'ts-style-' . sanitize_html_class( terrasuite_agency_current_style() );

	return $classes;
}
add_filter( 'body_class', 'terrasuite_agency_body_class' );

/**
 * The telephone bar, for the layouts whose header carries one.
 *
 * The number comes from the agency record the plugin already keeps for this
 * installation. It used to be read from a `agency_phone` setting that the
 * plugin has never written, so the bar printed no telephone number at all.
 *
 * WhatsApp sits in the header of every competitor site in this market, and is
 * how a good half of overseas enquiries actually arrive. It is only offered
 * when the Pro messaging class is present, because the free plugin has nothing
 * to build the link with.
 *
 * @return void
 */
function terrasuite_agency_contact_bar() {
	$layout = terrasuite_agency_current_layout();

	if ( 'contact' !== $layout['header'] ) {
		return;
	}

	$agency   = class_exists( 'TerraSuite_Agencies' ) ? TerraSuite_Agencies::own() : null;
	$phone    = $agency && ! empty( $agency->phone ) ? $agency->phone : '';
	$whatsapp = $agency && ! empty( $agency->whatsapp ) && class_exists( 'TerraSuite_Messages' )
		? TerraSuite_Messages::whatsapp_link( $agency->whatsapp )
		: '';

	echo '<div class="ts-contact-bar"><div class="ts-contact-bar__inner">';

	// An agency that has not filled in a telephone number gets no empty box
	// where one would have been.
	if ( '' !== $phone || '' !== $whatsapp ) {
		echo '<div class="ts-contact-bar__left">';
	}

	if ( '' !== $phone ) {
		printf(
			'<a href="tel:%s">%s</a>',
			esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ),
			esc_html( $phone )
		);
	}

	if ( '' !== $whatsapp ) {
		printf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( $whatsapp ),
			esc_html__( 'WhatsApp', 'terrasuite-agency' )
		);
	}

	if ( '' !== $phone || '' !== $whatsapp ) {
		echo '</div>';
	}

	echo '<div class="ts-contact-bar__right">';

	printf(
		'<a href="%s">%s</a>',
		esc_url( home_url( '/free-valuation/' ) ),
		esc_html__( 'Book a valuation', 'terrasuite-agency' )
	);

	terrasuite_agency_shortlist_count();

	printf(
		'<a href="%s">%s</a>',
		esc_url( home_url( '/my-account/' ) ),
		esc_html__( 'My account', 'terrasuite-agency' )
	);

	echo '</div></div></div>';
}
add_action( 'wp_body_open', 'terrasuite_agency_contact_bar' );

/**
 * The saved-properties counter that follows a visitor around.
 *
 * The number is printed by PHP so it is right on arrival, and carries the
 * attribute the plugin's own script already looks for, so pressing a heart
 * anywhere on the page moves it without a reload. Nothing new is wired up
 * here: that hook existed and the header was simply not using it.
 *
 * @return void
 */
function terrasuite_agency_shortlist_count() {
	if ( ! function_exists( 'terrasuite_current_lead' ) || ! class_exists( 'TerraSuite_Leads' ) ) {
		return;
	}

	$page  = (int) terrasuite_setting( 'page_favourites' );
	$lead  = terrasuite_current_lead();
	$saved = $lead ? count( TerraSuite_Leads::property_ids( $lead->id, 'favourite' ) ) : 0;

	printf(
		'<a class="ts-shortlist-count" href="%s">%s <span class="ts-shortlist-count__n" data-terrasuite-fav-count>%d</span></a>',
		esc_url( $page ? get_permalink( $page ) : home_url( '/' ) ),
		esc_html__( 'Shortlist', 'terrasuite-agency' ),
		(int) $saved
	);
}

/**
 * The properties the showcase carousel puts on screen.
 *
 * Featured first, then the newest, because a carousel with three slides in it
 * looks like a fault rather than a choice.
 *
 * @return array List of arrays with url, image, title, price.
 */
function terrasuite_agency_showcase_properties() {
	if ( ! function_exists( 'terrasuite_get_property' ) ) {
		return array();
	}

	$ids = get_posts(
		array(
			'post_type'        => 'terrasuite_property',
			'post_status'      => 'publish',
			'numberposts'      => 6,
			'fields'           => 'ids',
			'suppress_filters' => false,
			'meta_key'         => '_terrasuite_featured',
			'meta_value'       => '1',
		)
	);

	if ( count( $ids ) < 3 ) {
		$fill = get_posts(
			array(
				'post_type'        => 'terrasuite_property',
				'post_status'      => 'publish',
				'numberposts'      => 6,
				'fields'           => 'ids',
				'exclude'          => $ids,
				'suppress_filters' => false,
			)
		);

		$ids = array_merge( $ids, $fill );
	}

	$out = array();

	foreach ( array_slice( $ids, 0, 6 ) as $id ) {
		$property = terrasuite_get_property( $id );

		if ( ! $property || ! has_post_thumbnail( $id ) ) {
			continue;
		}

		$out[] = array(
			'url'   => (string) get_permalink( $id ),
			'image' => get_the_post_thumbnail( $id, 'large', array( 'loading' => 'eager', 'decoding' => 'async', 'alt' => '' ) ),
			'title' => $property->title(),
			'price' => wp_strip_all_tags( $property->price_html() ),
		);
	}

	return $out;
}

/**
 * Print the showcase carousel above the front page.
 *
 * @param string $content Post content.
 * @return string
 */
function terrasuite_agency_showcase( $content ) {
	if ( ! is_front_page() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$layout = terrasuite_agency_current_layout();

	if ( 'carousel' !== $layout['hero'] ) {
		return $content;
	}

	$properties = terrasuite_agency_showcase_properties();

	// Fewer than three and it is not a carousel, it is a picture. Fall back to
	// the ordinary photograph hero rather than printing something embarrassing.
	if ( count( $properties ) < 3 ) {
		return $content;
	}

	$out  = '<div class="ts-showcase" role="region" aria-roledescription="carousel" aria-label="' . esc_attr__( 'Featured properties', 'terrasuite-agency' ) . '" tabindex="0">';
	$out .= '<div class="ts-showcase__track">';

	foreach ( $properties as $property ) {
		$out .= sprintf(
			'<a class="ts-showcase__slide" href="%s">%s<span class="ts-showcase__caption"><strong>%s</strong><span class="ts-showcase__price">%s</span></span></a>',
			esc_url( $property['url'] ),
			$property['image'],
			esc_html( $property['title'] ),
			esc_html( $property['price'] )
		);
	}

	$out .= '</div>';

	$out .= sprintf(
		'<button class="ts-showcase__arrow ts-showcase__arrow--prev" type="button" aria-label="%s">&#8249;</button>',
		esc_attr__( 'Previous property', 'terrasuite-agency' )
	);

	$out .= sprintf(
		'<button class="ts-showcase__arrow ts-showcase__arrow--next" type="button" aria-label="%s">&#8250;</button>',
		esc_attr__( 'Next property', 'terrasuite-agency' )
	);

	$out .= '<div class="ts-showcase__nav">';

	foreach ( $properties as $n => $property ) {
		$out .= sprintf(
			'<button class="ts-showcase__dot" type="button" aria-current="%s" aria-label="%s"></button>',
			0 === $n ? 'true' : 'false',
			/* translators: %d: slide number. */
			esc_attr( sprintf( __( 'Property %d', 'terrasuite-agency' ), $n + 1 ) )
		);
	}

	$out .= '</div></div>';

	return $out . $content;
}
add_filter( 'the_content', 'terrasuite_agency_showcase', 5 );

/**
 * Print the browse tiles on the front page.
 *
 * Two of the layouts open with places rather than a search box, which is what
 * every working agency site in this market does and what none of the first six
 * layouts offered. The tiles come from the plugin's own shortcodes, so an
 * agency that would rather place them by hand on some other page can, and the
 * theme is not carrying a second copy of the same query.
 *
 * 'lead' puts them directly beneath the hero. The hero is part of the front
 * page content, so the tiles are spliced in after it when the hero can be
 * found; when an agency has replaced the hero with something of their own they
 * go to the foot instead, which is the honest fallback rather than a guess at
 * where the new hero ends.
 *
 * THERE ARE TWO SHAPES OF HERO AND THIS ONLY KNEW ONE.
 *
 * The plugin's own front page opens with <section class="ts-hero">. A front
 * page built in the editor opens with a cover block. Both are heroes; only the
 * first was looked for, so on every site of the second kind the two layouts
 * that exist to put places first quietly put them last instead - which is the
 * other arrangement, offered under its own names. No error, no difference
 * anybody would attribute to this, and the two designs sold on "pick a town
 * first" did not.
 *
 * Found by the content-pack suite, which measures the byte offsets rather than
 * trusting the page to say what it is.
 *
 * This runs at priority 6, before do_blocks() at 9, so the block comments are
 * still in the content and the end of the cover is a marker rather than a
 * closing tag that would have to be counted.
 *
 * @param string $content Post content.
 * @return string
 */
function terrasuite_agency_browse( $content ) {
	if ( ! is_front_page() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$layout = terrasuite_agency_current_layout();
	$where  = $layout['browse'] ?? '';

	if ( '' === $where || ! shortcode_exists( 'terrasuite_browse_areas' ) ) {
		return $content;
	}

	$tiles = do_shortcode( '[terrasuite_browse_areas limit="8"]' )
		. do_shortcode( '[terrasuite_browse_types limit="6" layout="chips"]' );

	if ( '' === trim( $tiles ) ) {
		return $content;
	}

	if ( 'lead' === $where ) {
		$after = terrasuite_agency_hero_ends( $content );

		if ( null !== $after ) {
			return substr( $content, 0, $after ) . $tiles . substr( $content, $after );
		}
	}

	return $content . $tiles;
}
add_filter( 'the_content', 'terrasuite_agency_browse', 6 );

/**
 * Where the hero finishes, in bytes, or null if there is not one to find.
 *
 * @param string $content Front page content, before the blocks are rendered.
 * @return int|null
 */
function terrasuite_agency_hero_ends( $content ) {
	// The plugin's own front page: plain markup, and the class is the marker.
	$end = strpos( $content, '</section>' );

	if ( false !== $end && false !== strpos( substr( $content, 0, $end ), 'ts-hero' ) ) {
		return $end + strlen( '</section>' );
	}

	/*
	 * A front page built in the editor, where the hero is a cover block.
	 *
	 * Only when the cover is the FIRST thing on the page. A cover half way down
	 * is a band between two sections of a page that opens with something else,
	 * and splicing the tiles after that would drop them into the middle of
	 * somebody's page rather than under a hero. Anything before it that is only
	 * whitespace or a comment does not count as content.
	 */
	if ( ! preg_match( '#<!--\s+wp:cover\b#', $content, $open, PREG_OFFSET_CAPTURE ) ) {
		return null;
	}

	// Whitespace and an ordinary HTML comment are not content: a page can carry
	// a note above its hero and the hero is still the first thing on it. A
	// BLOCK delimiter is an HTML comment too, so only comments that are not
	// wp: delimiters are stepped over.
	$before = substr( $content, 0, $open[0][1] );
	$before = preg_replace( '#<!--(?!\s*/?wp:).*?-->#s', '', $before );

	if ( '' !== trim( (string) $before ) ) {
		return null;
	}

	// Counted rather than taking the first closing marker: a cover with a cover
	// inside it would otherwise end at the inner one, and the tiles would land
	// inside the hero.
	if ( ! preg_match_all( '#<!--\s+(/?)wp:cover\b#', $content, $marks, PREG_OFFSET_CAPTURE ) ) {
		return null;
	}

	$depth = 0;

	foreach ( $marks[0] as $i => $mark ) {
		$depth += ( '/' === $marks[1][ $i ][0] ) ? -1 : 1;

		if ( 0 === $depth ) {
			$tail = strpos( $content, '-->', $mark[1] );

			return false === $tail ? null : $tail + 3;
		}
	}

	return null;
}

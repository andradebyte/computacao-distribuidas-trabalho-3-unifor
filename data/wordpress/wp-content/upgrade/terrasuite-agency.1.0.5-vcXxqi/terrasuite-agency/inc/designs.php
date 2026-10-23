<?php
/**
 * Designs: real layout choices, not eighteen colour schemes.
 *
 * WordPress style variations can change tokens - colour, type, spacing, radius
 * - and nothing else. They cannot move the search box, turn the hero into a
 * carousel or change a grid of cards into a list. So a theme that offers only
 * style variations offers one layout painted eighteen ways, and an agency
 * choosing between them is choosing a palette.
 *
 * This file adds the other axis. A LAYOUT changes the arrangement: what the
 * hero is, how the cards are shaped, where the header sits. A DESIGN is a
 * named pairing of one layout with one style, which is what an agency actually
 * picks from. Nine layouts and nine palettes give eighty one possibilities; the
 * twenty four curated below are the ones worth putting names to.
 *
 * The layout is applied as a class on the body and implemented in
 * assets/layouts.css, so switching design never touches a template and never
 * touches an agency's content.
 *
 * @package TerraSuiteAgency
 */

defined( 'ABSPATH' ) || exit;

/**
 * The layout presets.
 *
 * hero:
 *   photo        - full bleed photograph, search panel over it
 *   split        - photograph one side, search on a solid panel the other
 *   carousel     - featured properties moving behind the search
 *   panel        - no photograph at all: type and search on a colour block
 *   compact      - a short hero, straight into the properties
 *   panel-search - photograph with a wide multi-column filter panel across it
 *
 * browse: whether the front page carries tiles for the places you cover and
 * the kinds of property you sell, and where they sit.
 *   ''    - not shown
 *   lead  - immediately below the hero
 *   tail  - at the foot of the front page
 *
 * cards:
 *   grid      - equal cards in a grid
 *   wide      - large picture with the detail beside it, two across
 *   list      - one row per property, picture left
 *   masonry   - staggered heights, picture led
 *   overlay   - detail over the picture, no card around it
 *
 * header:
 *   left      - brand left, navigation right
 *   centred   - brand centred over the navigation
 *   contact   - brand left, navigation right, telephone bar above
 *
 * @return array
 */
function terrasuite_agency_layouts() {
	return array(
		'classic'   => array(
			'label'  => __( 'Classic', 'terrasuite-agency' ),
			'note'   => __( 'A photograph across the top with the search over it, and properties in an even grid. The arrangement most agencies use, done properly.', 'terrasuite-agency' ),
			'hero'   => 'photo',
			'cards'  => 'grid',
			'header' => 'left',
		),
		'split'     => array(
			'label'  => __( 'Split', 'terrasuite-agency' ),
			'note'   => __( 'The photograph takes one half and the search sits on a solid panel in the other. Reads well on a wide screen and stacks cleanly on a phone.', 'terrasuite-agency' ),
			'hero'   => 'split',
			'cards'  => 'wide',
			'header' => 'left',
		),
		'showcase'  => array(
			'label'  => __( 'Showcase', 'terrasuite-agency' ),
			'note'   => __( 'Your best properties move behind the search, and the listings appear as pictures with the detail over them. For an agency whose stock is the selling point.', 'terrasuite-agency' ),
			'hero'   => 'carousel',
			'cards'  => 'overlay',
			'header' => 'centred',
		),
		'editorial' => array(
			'label'  => __( 'Editorial', 'terrasuite-agency' ),
			'note'   => __( 'No hero photograph. A confident line of type, the search beneath it, and properties in a list with room to say something about each. Fast, and unlike everyone else.', 'terrasuite-agency' ),
			'hero'   => 'panel',
			'cards'  => 'list',
			'header' => 'contact',
		),
		'gallery'   => array(
			'label'  => __( 'Gallery', 'terrasuite-agency' ),
			'note'   => __( 'A short hero and then straight into the properties, staggered so the pictures lead. Suits an agency with a lot of stock and good photography.', 'terrasuite-agency' ),
			'hero'   => 'compact',
			'cards'  => 'masonry',
			'header' => 'centred',
		),
		'boutique'  => array(
			'label'  => __( 'Boutique', 'terrasuite-agency' ),
			'note'   => __( 'Split the other way, with more air around everything and fewer properties on the page. For a small number of expensive houses.', 'terrasuite-agency' ),
			'hero'   => 'split',
			'cards'  => 'overlay',
			'header' => 'centred',
		),

		/*
		 * The three below were drawn from what agencies on this coast actually
		 * ship, rather than from taste. Looked at 28 August 2026:
		 * casas-connect.es, casaverano.no and marshallsspain.com.
		 *
		 * Three things all of them do that none of our first six did:
		 *
		 *   - The search in the hero is a WIDE MULTI-COLUMN PANEL, not a single
		 *     row. Area, price from, price to, beds and reference all visible
		 *     at once, because a buyer for Spanish property arrives knowing
		 *     roughly all five and resents being asked one at a time.
		 *   - They open with TILES, not a search box: pick a place, or pick a
		 *     kind of property. Somebody who does not yet know what they want
		 *     cannot fill in a form.
		 *   - The contact details live in a bar at the very top - telephone,
		 *     WhatsApp, and a shortlist counter that follows you around.
		 */
		'connect'   => array(
			'label'  => __( 'Connect', 'terrasuite-agency' ),
			'note'   => __( 'A search panel running the full width of the photograph with the filters side by side, and cards that stack their badges. Built for a busy sales portfolio where Reserved, Reduced and Exclusive all matter.', 'terrasuite-agency' ),
			'hero'   => 'panel-search',
			'cards'  => 'grid',
			'header' => 'contact',
		),
		'discover'  => array(
			'label'  => __( 'Discover', 'terrasuite-agency' ),
			'note'   => __( 'Opens with the places you cover and the kinds of property you sell, as pictures somebody can press. For an agency whose buyers are choosing a town before they choose a house.', 'terrasuite-agency' ),
			'hero'   => 'compact',
			'cards'  => 'grid',
			'header' => 'centred',
			'browse' => 'lead',
		),
		'estate'    => array(
			'label'  => __( 'Estate', 'terrasuite-agency' ),
			'note'   => __( 'Dark and gold, with the telephone, WhatsApp and shortlist along the top and the regions you cover below the fold. The luxury end of this market, done without shouting.', 'terrasuite-agency' ),
			'hero'   => 'photo',
			'cards'  => 'overlay',
			'header' => 'contact',
			'browse' => 'tail',
		),
	);
}

/**
 * The named designs an agency chooses from.
 *
 * @return array
 */
function terrasuite_agency_designs() {
	$designs = array(
		'harbour'    => array( 'Harbour', 'classic', 'coastal', __( 'Blue, open and friendly. The safe choice, and a good one.', 'terrasuite-agency' ) ),
		'meridian'   => array( 'Meridian', 'classic', 'metropolitan', __( 'Square corners, black and a single blue. Corporate without being cold.', 'terrasuite-agency' ) ),
		'rowan'      => array( 'Rowan', 'classic', 'forest', __( 'Deep green and warm neutrals. Reads as rural without resorting to tweed.', 'terrasuite-agency' ) ),
		'sandstone'  => array( 'Sandstone', 'split', 'sandstone', __( 'Warm stone and soft edges beside a strong photograph.', 'terrasuite-agency' ) ),
		'piccadilly' => array( 'Piccadilly', 'split', 'metropolitan', __( 'A city agency. Hard edges, plenty of white, one accent.', 'terrasuite-agency' ) ),
		'nightfall'  => array( 'Nightfall', 'split', 'nocturne', __( 'Dark throughout with gold detail. Photographs look expensive on it.', 'terrasuite-agency' ) ),
		'pavilion'   => array( 'Pavilion', 'showcase', 'coastal', __( 'Properties moving behind the search, in blue and white.', 'terrasuite-agency' ) ),
		'marquee'    => array( 'Marquee', 'showcase', 'terracotta', __( 'Warm terracotta and cream. Suits Mediterranean stock particularly.', 'terrasuite-agency' ) ),
		'onyx'       => array( 'Onyx', 'showcase', 'ink', __( 'Near black, high contrast, no ornament. The pictures do everything.', 'terrasuite-agency' ) ),
		'broadsheet' => array( 'Broadsheet', 'editorial', 'ink', __( 'No hero image at all. Type, a search box and a list. Loads instantly.', 'terrasuite-agency' ) ),
		'chapter'    => array( 'Chapter', 'editorial', 'heritage', __( 'Serif headings, forest and claret. For an old firm that says so.', 'terrasuite-agency' ) ),
		'signal'     => array( 'Signal', 'editorial', 'metropolitan', __( 'The fastest thing here. Monochrome, one accent, nothing decorative.', 'terrasuite-agency' ) ),
		'atrium'     => array( 'Atrium', 'gallery', 'coastal', __( 'Staggered picture grid, light and airy.', 'terrasuite-agency' ) ),
		'kiln'       => array( 'Kiln', 'gallery', 'terracotta', __( 'The same grid in warm clay and cream.', 'terrasuite-agency' ) ),
		'lantern'    => array( 'Lantern', 'gallery', 'nocturne', __( 'Dark ground, staggered pictures, gold type.', 'terrasuite-agency' ) ),
		'belgravia'  => array( 'Belgravia', 'boutique', 'heritage', __( 'Few properties, generous space, serif headings.', 'terrasuite-agency' ) ),
		'linen'      => array( 'Linen', 'boutique', 'sandstone', __( 'Quiet, warm and unhurried. Country house agency.', 'terrasuite-agency' ) ),
		'estuary'    => array( 'Estuary', 'boutique', 'harbour', __( 'Slate and sea green with a lot of air.', 'terrasuite-agency' ) ),

		/* Drawn from three working Costa Blanca agency sites. See the note in
		 * terrasuite_agency_layouts(). */
		'costa'      => array( 'Costa', 'connect', 'coastal', __( 'A full width search across the photograph, and badges on the cards. The Spanish coastal agency shape.', 'terrasuite-agency' ) ),
		'mercado'    => array( 'Mercado', 'connect', 'terracotta', __( 'The same wide search in warm clay and cream.', 'terrasuite-agency' ) ),
		'atlas'      => array( 'Atlas', 'discover', 'harbour', __( 'Pick a town, then a kind of house. For buyers choosing a place first.', 'terrasuite-agency' ) ),
		'verano'     => array( 'Verano', 'discover', 'sandstone', __( 'The same tiles, warmer and softer edged.', 'terrasuite-agency' ) ),
		'marquis'    => array( 'Marquis', 'estate', 'nocturne', __( 'Dark and gold with a contact bar. The luxury end.', 'terrasuite-agency' ) ),
		'consulate'  => array( 'Consulate', 'estate', 'ink', __( 'The same arrangement in black and white, for an agency that would rather look serious than expensive.', 'terrasuite-agency' ) ),
	);

	$out = array();

	foreach ( $designs as $slug => $row ) {
		$out[ $slug ] = array(
			'label'  => $row[0],
			'layout' => $row[1],
			'style'  => $row[2],
			'note'   => $row[3],
		);
	}

	/**
	 * Filter the named designs.
	 *
	 * @param array $out slug => design.
	 */
	return apply_filters( 'terrasuite_agency_designs', $out );
}

/**
 * Which design is in use.
 *
 * @return string
 */
function terrasuite_agency_current_design() {
	$slug    = (string) get_theme_mod( 'terrasuite_design', 'harbour' );
	$designs = terrasuite_agency_designs();

	return isset( $designs[ $slug ] ) ? $slug : 'harbour';
}

/**
 * Whether the footer carries the TerraSuite credit line.
 *
 * Off unless the site owner switches it on. The footer belongs to the agency,
 * not to us, and a theme in the WordPress.org directory may not put its
 * author's words on somebody else's pages.
 *
 * @return bool
 */
function terrasuite_agency_shows_credit() {
	return (bool) get_theme_mod( 'terrasuite_credit', false );
}

/**
 * The layout preset currently in force.
 *
 * THE ARRANGEMENT AND THE COLOURS ARE TWO CHOICES, NOT ONE.
 *
 * This used to read the arrangement out of whichever named design was in use,
 * which meant an agency could have one of the twenty-four named pairings and
 * nothing else. The other fifty-seven combinations of the nine arrangements
 * and nine palettes were not reachable from any screen, while the website
 * advertised eighty-one of them.
 *
 * Each half is now recorded on its own and a named design simply sets both at
 * once. A site that chose a design before this existed has no arrangement of
 * its own recorded, so it still gets that design's - nothing moves under
 * anybody on upgrade.
 *
 * @return array
 */
function terrasuite_agency_current_layout() {
	$layouts = terrasuite_agency_layouts();
	$slug    = terrasuite_agency_current_layout_slug();

	return isset( $layouts[ $slug ] ) ? $layouts[ $slug ] : reset( $layouts );
}

/**
 * The slug of the arrangement in use.
 *
 * @return string
 */
function terrasuite_agency_current_layout_slug() {
	$layouts = terrasuite_agency_layouts();
	$chosen  = (string) get_theme_mod( 'terrasuite_layout', '' );

	if ( isset( $layouts[ $chosen ] ) ) {
		return $chosen;
	}

	$designs = terrasuite_agency_designs();
	$design  = $designs[ terrasuite_agency_current_design() ];

	return isset( $layouts[ $design['layout'] ] ) ? $design['layout'] : (string) key( $layouts );
}

/**
 * The colour schemes this theme ships, as slug => name.
 *
 * Read from the theme's own style variations rather than listed here, so a
 * palette added to the theme turns up on the screen without anybody having to
 * remember a second place to add it.
 *
 * @return array
 */
function terrasuite_agency_styles() {
	$out = array();

	foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
		$title = isset( $variation['title'] ) ? (string) $variation['title'] : '';
		$slug  = sanitize_title( $title );

		if ( '' !== $slug ) {
			$out[ $slug ] = $title;
		}
	}

	return $out;
}

/**
 * The slug of the colour scheme in use.
 *
 * @return string
 */
function terrasuite_agency_current_style() {
	$styles = terrasuite_agency_styles();
	$chosen = (string) get_theme_mod( 'terrasuite_style', '' );

	if ( isset( $styles[ $chosen ] ) ) {
		return $chosen;
	}

	$designs = terrasuite_agency_designs();
	$design  = $designs[ terrasuite_agency_current_design() ];

	return (string) $design['style'];
}

/**
 * The colour schemes that go with an arrangement.
 *
 * Taken from the named designs rather than written out a second time: a
 * pairing is recommended because somebody put those two together and gave it a
 * name. It also means the suggestions cannot drift away from the designs.
 *
 * @param string $layout Arrangement slug.
 * @return string[] Colour scheme slugs.
 */
function terrasuite_agency_styles_for_layout( $layout ) {
	$out = array();

	foreach ( terrasuite_agency_designs() as $design ) {
		if ( $design['layout'] === $layout ) {
			$out[ $design['style'] ] = true;
		}
	}

	return array_keys( $out );
}

/**
 * And the arrangements that go with a colour scheme.
 *
 * @param string $style Colour scheme slug.
 * @return string[] Arrangement slugs.
 */
function terrasuite_agency_layouts_for_style( $style ) {
	$out = array();

	foreach ( terrasuite_agency_designs() as $design ) {
		if ( $design['style'] === $style ) {
			$out[ $design['layout'] ] = true;
		}
	}

	return array_keys( $out );
}

/**
 * The named design matching what is actually in force, or '' for a pairing of
 * the agency's own.
 *
 * The screen marks a design "in use", and once the two halves can be chosen
 * separately that has to be worked out from the halves rather than from a
 * record of what was last pressed.
 *
 * @return string
 */
function terrasuite_agency_named_pairing() {
	$layout = terrasuite_agency_current_layout_slug();
	$style  = terrasuite_agency_current_style();

	foreach ( terrasuite_agency_designs() as $slug => $design ) {
		if ( $design['layout'] === $layout && $design['style'] === $style ) {
			return $slug;
		}
	}

	return '';
}

/**
 * Use one arrangement, and leave the colours alone.
 *
 * @param string $slug Arrangement slug.
 * @return bool
 */
function terrasuite_agency_apply_layout( $slug ) {
	$layouts = terrasuite_agency_layouts();

	if ( ! isset( $layouts[ $slug ] ) ) {
		return false;
	}

	set_theme_mod( 'terrasuite_layout', $slug );

	// Record the colours explicitly too. Without it, a site that had only ever
	// chosen a named design would keep resolving its colours through that
	// design, and changing the arrangement would silently change them back.
	set_theme_mod( 'terrasuite_style', terrasuite_agency_current_style() );

	terrasuite_agency_remember_pairing();

	return true;
}

/**
 * Use one colour scheme, and leave the arrangement alone.
 *
 * The variation is written into the user's global styles, which is what the
 * site editor itself does when somebody clicks a variation there.
 *
 * @param string $slug Colour scheme slug.
 * @return bool
 */
function terrasuite_agency_apply_style( $slug ) {
	$styles = terrasuite_agency_styles();

	if ( ! isset( $styles[ $slug ] ) ) {
		return false;
	}

	foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
		if ( sanitize_title( isset( $variation['title'] ) ? $variation['title'] : '' ) !== $slug ) {
			continue;
		}

		$user_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

		if ( $user_id ) {
			wp_update_post(
				array(
					'ID'           => $user_id,
					'post_content' => wp_json_encode(
						array(
							'version'  => isset( $variation['version'] ) ? $variation['version'] : 3,
							'settings' => isset( $variation['settings'] ) ? $variation['settings'] : array(),
							'styles'   => isset( $variation['styles'] ) ? $variation['styles'] : array(),
						)
					),
				)
			);
		}

		break;
	}

	set_theme_mod( 'terrasuite_style', $slug );
	set_theme_mod( 'terrasuite_layout', terrasuite_agency_current_layout_slug() );

	terrasuite_agency_remember_pairing();

	return true;
}

/**
 * Keep the record of which named design is in use honest.
 *
 * @return void
 */
function terrasuite_agency_remember_pairing() {
	$named = terrasuite_agency_named_pairing();

	if ( '' !== $named ) {
		set_theme_mod( 'terrasuite_design', $named );
		return;
	}

	// A pairing of their own is not any of the named ones, and saying it is
	// would put "in use" against a design the site is not running.
	remove_theme_mod( 'terrasuite_design' );
}

/**
 * Apply a design: record the choice and switch the style variation with it.
 *
 * The style variation is applied by writing the variation's settings and styles
 * into the user's global styles record, which is what the site editor itself
 * does when somebody clicks a variation. Doing it here means one click changes
 * both halves rather than leaving an agency to find the Styles panel.
 *
 * @param string $slug Design slug.
 * @return bool
 */
function terrasuite_agency_apply_design( $slug ) {
	$designs = terrasuite_agency_designs();

	if ( ! isset( $designs[ $slug ] ) ) {
		return false;
	}

	// A named design is a shortcut for one arrangement and one set of colours,
	// not a third thing sitting over them. Both halves are set explicitly, so
	// the screen shows the same two choices whichever way they were made.
	terrasuite_agency_apply_layout( $designs[ $slug ]['layout'] );
	terrasuite_agency_apply_style( $designs[ $slug ]['style'] );

	set_theme_mod( 'terrasuite_design', $slug );

	return true;
}

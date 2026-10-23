<?php
/**
 * Designs: the arrangement and the colour scheme.
 *
 * In the TerraSuite menu when the plugin is there, and Appearance > Designs
 * when the theme is on its own - see terrasuite_agency_design_cap().
 *
 * Eighteen designs shown as drawn wireframes rather than screenshots. A
 * screenshot goes stale the moment somebody changes a colour, needs generating
 * for every combination, and weighs more than the theme. A wireframe drawn in
 * the browser from the layout definition and tinted with that design's own
 * palette is always current, weighs nothing, and shows the one thing an agency
 * is actually choosing between: where things sit.
 *
 * @package TerraSuiteAgency
 */

defined( 'ABSPATH' ) || exit;

/**
 * Who may change the design, and what the screen is called on this site.
 *
 * WHY THIS IS NOT edit_theme_options ANY MORE.
 *
 * It was an Appearance page behind edit_theme_options, and an Agency Owner
 * holds neither that capability nor manage_options - deliberately, and the
 * reason is written out in the plugin's installer: edit_theme_options opens
 * the site editor, where an agency can take the templates apart with no way
 * back. The cost of that was this screen. The one place an agency chooses how
 * its own website looks was the one place an agency could not reach, while the
 * hosted pages sell nine arrangements and nine colour schemes.
 *
 * Found by the screenshot script, which signs in as an owner and was refused.
 *
 * So the screen moves into the TerraSuite menu beside Your Website and takes
 * that screen's capability, which an owner holds and an administrator is given
 * a copy of. Nothing the screen does needs the site editor: it writes two
 * theme mods and the global styles post, neither of which is capability
 * checked at the point this calls it.
 *
 * With the plugin absent there is no such menu and no such capability, so it
 * stays exactly where it was.
 *
 * @return string The capability that opens it.
 */
function terrasuite_agency_design_cap() {
	return class_exists( 'TerraSuite_Admin_Menu' ) ? 'terrasuite_manage_website' : 'edit_theme_options';
}

/**
 * The screen's own hook suffix, remembered when the menu is built.
 *
 * It used to be hard coded as appearance_page_terrasuite-designs, which is
 * wrong the moment the screen has two possible parents.
 *
 * @param string|null $set Set it, or null to read it.
 * @return string
 */
function terrasuite_agency_design_hook( $set = null ) {
	static $hook = '';

	if ( null !== $set ) {
		$hook = (string) $set;
	}

	return $hook;
}

/**
 * Add the screen.
 *
 * At eleven, so the TerraSuite menu it attaches to has been built.
 *
 * @return void
 */
function terrasuite_agency_design_menu() {
	if ( class_exists( 'TerraSuite_Admin_Menu' ) ) {
		$hook = add_submenu_page(
			'terrasuite',
			__( 'Designs', 'terrasuite-agency' ),
			__( 'Designs', 'terrasuite-agency' ),
			terrasuite_agency_design_cap(),
			'terrasuite-designs',
			'terrasuite_agency_design_screen'
		);
	} else {
		$hook = add_theme_page(
			__( 'Designs', 'terrasuite-agency' ),
			__( 'Designs', 'terrasuite-agency' ),
			terrasuite_agency_design_cap(),
			'terrasuite-designs',
			'terrasuite_agency_design_screen'
		);
	}

	terrasuite_agency_design_hook( $hook );
}
add_action( 'admin_menu', 'terrasuite_agency_design_menu', 11 );


/**
 * The palette of one style variation, for tinting the wireframes.
 *
 * @param string $style Variation slug.
 * @return array Six hex colours, keyed.
 */
function terrasuite_agency_style_palette( $style ) {
	static $cache = null;

	if ( null === $cache ) {
		$cache = array();

		foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
			$slug = sanitize_title( isset( $variation['title'] ) ? $variation['title'] : '' );

			if ( '' === $slug ) {
				continue;
			}

			$colours = array();
			$palette = isset( $variation['settings']['color']['palette'] ) ? $variation['settings']['color']['palette'] : array();

			foreach ( $palette as $entry ) {
				if ( isset( $entry['slug'], $entry['color'] ) ) {
					$colours[ $entry['slug'] ] = $entry['color'];
				}
			}

			$cache[ $slug ] = $colours;
		}
	}

	$found = isset( $cache[ $style ] ) ? $cache[ $style ] : array();

	// The default variation carries no palette of its own - it inherits the
	// theme's. Falling back to invented greys would draw Coastal in colours
	// that appear nowhere on the site.
	$theme = array();

	foreach ( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $entry ) {
		if ( isset( $entry['slug'], $entry['color'] ) ) {
			$theme[ $entry['slug'] ] = $entry['color'];
		}
	}

	$pick = function ( $slug, $fallback ) use ( $found, $theme ) {
		if ( isset( $found[ $slug ] ) ) {
			return $found[ $slug ];
		}

		return isset( $theme[ $slug ] ) ? $theme[ $slug ] : $fallback;
	};

	return array(
		'base'    => $pick( 'base', '#ffffff' ),
		'surface' => $pick( 'surface', '#f4f5f7' ),
		'ink'     => $pick( 'ink', '#16202b' ),
		'accent'  => $pick( 'accent', '#1d6fa5' ),
		'line'    => $pick( 'line', '#dfe3e8' ),
		'muted'   => $pick( 'muted', '#63707e' ),
	);
}

/**
 * Draw one design as a wireframe.
 *
 * @param array $layout  Layout preset.
 * @param array $palette Colours.
 * @return string
 */
function terrasuite_agency_wireframe( $layout, $palette ) {
	$ink    = esc_attr( $palette['ink'] );
	$base   = esc_attr( $palette['base'] );
	$accent = esc_attr( $palette['accent'] );
	$line   = esc_attr( $palette['line'] );
	$soft   = esc_attr( $palette['surface'] );

	$svg = '<svg class="tsd-wire" viewBox="0 0 300 200" role="img" aria-hidden="true" focusable="false">';
	$svg .= '<rect width="300" height="200" fill="' . $base . '"/>';

	// The header band, positioned by the header preset.
	if ( 'centred' === $layout['header'] ) {
		$svg .= '<rect x="126" y="8" width="48" height="7" rx="2" fill="' . $ink . '"/>';
		$svg .= '<rect x="96" y="20" width="108" height="4" rx="2" fill="' . $line . '"/>';
		$head = 32;
	} elseif ( 'contact' === $layout['header'] ) {
		$svg .= '<rect x="0" y="0" width="300" height="10" fill="' . $ink . '"/>';
		$svg .= '<rect x="12" y="18" width="42" height="7" rx="2" fill="' . $ink . '"/>';
		$svg .= '<rect x="170" y="19" width="118" height="4" rx="2" fill="' . $line . '"/>';
		$head = 32;
	} else {
		$svg .= '<rect x="12" y="10" width="42" height="7" rx="2" fill="' . $ink . '"/>';
		$svg .= '<rect x="150" y="11" width="108" height="4" rx="2" fill="' . $line . '"/>';
		$svg .= '<rect x="266" y="8" width="22" height="10" rx="3" fill="' . $accent . '"/>';
		$head = 26;
	}

	// The hero, which is the thing that actually differs.
	$hero_h = 78;

	switch ( $layout['hero'] ) {
		case 'split':
			$svg .= '<rect x="0" y="' . $head . '" width="150" height="' . $hero_h . '" fill="' . $soft . '"/>';
			$svg .= '<rect x="150" y="' . $head . '" width="150" height="' . $hero_h . '" fill="' . $ink . '" opacity="0.75"/>';
			$svg .= '<rect x="16" y="' . ( $head + 16 ) . '" width="96" height="9" rx="2" fill="' . $ink . '"/>';
			$svg .= '<rect x="16" y="' . ( $head + 32 ) . '" width="118" height="18" rx="4" fill="' . $base . '" stroke="' . $line . '"/>';
			$svg .= '<rect x="112" y="' . ( $head + 36 ) . '" width="18" height="10" rx="3" fill="' . $accent . '"/>';
			break;

		case 'panel':
			$svg .= '<rect x="0" y="' . $head . '" width="300" height="' . $hero_h . '" fill="' . $ink . '"/>';
			$svg .= '<rect x="20" y="' . ( $head + 14 ) . '" width="150" height="12" rx="3" fill="' . $base . '" opacity="0.92"/>';
			$svg .= '<rect x="20" y="' . ( $head + 32 ) . '" width="96" height="6" rx="2" fill="' . $base . '" opacity="0.55"/>';
			$svg .= '<rect x="20" y="' . ( $head + 46 ) . '" width="200" height="18" rx="4" fill="' . $base . '"/>';
			$svg .= '<rect x="200" y="' . ( $head + 50 ) . '" width="16" height="10" rx="3" fill="' . $accent . '"/>';
			break;

		case 'carousel':
			$svg .= '<rect x="0" y="' . $head . '" width="300" height="' . $hero_h . '" fill="' . $ink . '" opacity="0.82"/>';
			$svg .= '<rect x="18" y="' . ( $head + 46 ) . '" width="110" height="8" rx="2" fill="' . $base . '" opacity="0.92"/>';
			$svg .= '<rect x="18" y="' . ( $head + 58 ) . '" width="52" height="6" rx="2" fill="' . $accent . '"/>';

			for ( $d = 0; $d < 4; $d++ ) {
				$fill = 0 === $d ? $base : $line;
				$svg .= '<rect x="' . ( 128 + $d * 14 ) . '" y="' . ( $head + $hero_h - 9 ) . '" width="10" height="3" rx="1.5" fill="' . esc_attr( $fill ) . '"/>';
			}

			$svg .= '<circle cx="16" cy="' . ( $head + $hero_h / 2 ) . '" r="8" fill="' . $base . '" opacity="0.85"/>';
			$svg .= '<circle cx="284" cy="' . ( $head + $hero_h / 2 ) . '" r="8" fill="' . $base . '" opacity="0.85"/>';
			break;

		case 'compact':
			$hero_h = 42;
			$svg .= '<rect x="0" y="' . $head . '" width="300" height="' . $hero_h . '" fill="' . $ink . '" opacity="0.8"/>';
			$svg .= '<rect x="20" y="' . ( $head + 10 ) . '" width="80" height="8" rx="2" fill="' . $base . '" opacity="0.9"/>';
			$svg .= '<rect x="20" y="' . ( $head + 24 ) . '" width="150" height="10" rx="3" fill="' . $base . '"/>';
			break;

		default:
			$svg .= '<rect x="0" y="' . $head . '" width="300" height="' . $hero_h . '" fill="' . $ink . '" opacity="0.72"/>';
			$svg .= '<rect x="20" y="' . ( $head + 14 ) . '" width="120" height="10" rx="2" fill="' . $base . '" opacity="0.92"/>';
			$svg .= '<rect x="20" y="' . ( $head + 32 ) . '" width="200" height="20" rx="4" fill="' . $base . '"/>';
			$svg .= '<rect x="198" y="' . ( $head + 36 ) . '" width="18" height="12" rx="3" fill="' . $accent . '"/>';
			break;
	}

	// The listings, which differ as much as the hero does.
	$top = $head + $hero_h + 10;

	switch ( $layout['cards'] ) {
		case 'list':
			for ( $r = 0; $r < 2; $r++ ) {
				$y = $top + $r * 30;
				$svg .= '<rect x="14" y="' . $y . '" width="272" height="24" rx="4" fill="' . $base . '" stroke="' . $line . '"/>';
				$svg .= '<rect x="14" y="' . $y . '" width="60" height="24" rx="4" fill="' . $soft . '"/>';
				$svg .= '<rect x="84" y="' . ( $y + 6 ) . '" width="120" height="5" rx="2" fill="' . $ink . '"/>';
				$svg .= '<rect x="84" y="' . ( $y + 15 ) . '" width="60" height="4" rx="2" fill="' . $line . '"/>';
			}
			break;

		case 'wide':
			for ( $c = 0; $c < 2; $c++ ) {
				$x = 14 + $c * 140;
				$svg .= '<rect x="' . $x . '" y="' . $top . '" width="132" height="46" rx="4" fill="' . $base . '" stroke="' . $line . '"/>';
				$svg .= '<rect x="' . $x . '" y="' . $top . '" width="58" height="46" rx="4" fill="' . $soft . '"/>';
				$svg .= '<rect x="' . ( $x + 66 ) . '" y="' . ( $top + 10 ) . '" width="56" height="5" rx="2" fill="' . $ink . '"/>';
				$svg .= '<rect x="' . ( $x + 66 ) . '" y="' . ( $top + 21 ) . '" width="34" height="4" rx="2" fill="' . $accent . '"/>';
			}
			break;

		case 'masonry':
			$heights = array( 34, 48, 28, 40, 30, 44 );

			for ( $c = 0; $c < 3; $c++ ) {
				$x = 14 + $c * 94;
				$y = $top;

				for ( $r = 0; $r < 2; $r++ ) {
					$h = $heights[ ( $c * 2 + $r ) % 6 ];
					$svg .= '<rect x="' . $x . '" y="' . $y . '" width="86" height="' . $h . '" rx="4" fill="' . $soft . '"/>';
					$y += $h + 6;
				}
			}
			break;

		case 'overlay':
			for ( $c = 0; $c < 3; $c++ ) {
				$x = 14 + $c * 94;
				$svg .= '<rect x="' . $x . '" y="' . $top . '" width="86" height="48" rx="4" fill="' . $ink . '" opacity="0.68"/>';
				$svg .= '<rect x="' . ( $x + 8 ) . '" y="' . ( $top + 30 ) . '" width="52" height="5" rx="2" fill="' . $base . '"/>';
				$svg .= '<rect x="' . ( $x + 8 ) . '" y="' . ( $top + 39 ) . '" width="30" height="4" rx="2" fill="' . $base . '" opacity="0.7"/>';
			}
			break;

		default:
			for ( $c = 0; $c < 3; $c++ ) {
				$x = 14 + $c * 94;
				$svg .= '<rect x="' . $x . '" y="' . $top . '" width="86" height="48" rx="4" fill="' . $base . '" stroke="' . $line . '"/>';
				$svg .= '<rect x="' . $x . '" y="' . $top . '" width="86" height="26" rx="4" fill="' . $soft . '"/>';
				$svg .= '<rect x="' . ( $x + 8 ) . '" y="' . ( $top + 32 ) . '" width="52" height="5" rx="2" fill="' . $ink . '"/>';
				$svg .= '<rect x="' . ( $x + 8 ) . '" y="' . ( $top + 41 ) . '" width="28" height="4" rx="2" fill="' . $accent . '"/>';
			}
			break;
	}

	return $svg . '</svg>';
}

/**
 * The screen.
 *
 * @return void
 */
function terrasuite_agency_design_screen() {
	if ( ! current_user_can( terrasuite_agency_design_cap() ) ) {
		wp_die( esc_html__( 'You cannot change the design.', 'terrasuite-agency' ) );
	}

	$notice = '';
	$failed = false;

	if ( isset( $_POST['terrasuite_credit_save'] ) && check_admin_referer( 'terrasuite-design' ) ) {
		$on = ! empty( $_POST['terrasuite_credit'] );

		set_theme_mod( 'terrasuite_credit', $on );

		$notice = $on
			? __( 'The footer now carries the TerraSuite credit.', 'terrasuite-agency' )
			: __( 'The footer now carries your own name only.', 'terrasuite-agency' );
	}

	if ( isset( $_POST['terrasuite_design'] ) && check_admin_referer( 'terrasuite-design' ) ) {
		$slug = sanitize_key( wp_unslash( $_POST['terrasuite_design'] ) );

		if ( terrasuite_agency_apply_design( $slug ) ) {
			$designs = terrasuite_agency_designs();
			/* translators: %s: design name. */
			$notice = sprintf( __( '%s is now live on the site.', 'terrasuite-agency' ), $designs[ $slug ]['label'] );
		} else {
			// A design that does not exist used to reload the screen with no
			// notice at all, so the only way to tell it had refused was to
			// notice the page had not changed. Not reachable by pressing a
			// button, and silent failure is the habit worth not having.
			$notice = __( 'That design is not one this theme offers, so nothing was changed.', 'terrasuite-agency' );
			$failed = true;
		}
	}

	if ( isset( $_POST['terrasuite_layout'] ) && check_admin_referer( 'terrasuite-design' ) ) {
		$slug = sanitize_key( wp_unslash( $_POST['terrasuite_layout'] ) );

		if ( terrasuite_agency_apply_layout( $slug ) ) {
			$all = terrasuite_agency_layouts();
			/* translators: %s: arrangement name. */
			$notice = sprintf( __( 'The site is now arranged as %s. Your colours are unchanged.', 'terrasuite-agency' ), $all[ $slug ]['label'] );
		} else {
			$notice = __( 'That arrangement is not one this theme offers, so nothing was changed.', 'terrasuite-agency' );
			$failed = true;
		}
	}

	if ( isset( $_POST['terrasuite_style'] ) && check_admin_referer( 'terrasuite-design' ) ) {
		$slug = sanitize_key( wp_unslash( $_POST['terrasuite_style'] ) );

		if ( terrasuite_agency_apply_style( $slug ) ) {
			$all = terrasuite_agency_styles();
			/* translators: %s: colour scheme name. */
			$notice = sprintf( __( 'The site is now in %s. The arrangement is unchanged.', 'terrasuite-agency' ), $all[ $slug ] );
		} else {
			$notice = __( 'That colour scheme is not one this theme offers, so nothing was changed.', 'terrasuite-agency' );
			$failed = true;
		}
	}

	$designs = terrasuite_agency_designs();
	$layouts = terrasuite_agency_layouts();
	$styles  = terrasuite_agency_styles();

	// Worked out from the two halves actually in force, so a pairing of the
	// agency's own does not put "in use" against a design they are not running.
	$current     = terrasuite_agency_named_pairing();
	$live_layout = terrasuite_agency_current_layout_slug();
	$live_style  = terrasuite_agency_current_style();

	echo '<div class="wrap tsd">';
	echo '<h1>' . esc_html__( 'Designs', 'terrasuite-agency' ) . '</h1>';
	echo '<p class="tsd__intro">' . esc_html__( 'A design is an arrangement of the site paired with a set of colours: where the search sits, how the listings are shaped, and what the whole thing looks like. Take one of the pairings below, or choose the arrangement and the colours separately further down. Your pages, properties and words are untouched either way, so you can try one and change your mind.', 'terrasuite-agency' ) . '</p>';
	echo '<h2 class="tsd__heading">' . esc_html__( 'Designs we have put together', 'terrasuite-agency' ) . '</h2>';

	if ( '' !== $notice ) {
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			$failed ? 'error' : 'success',
			esc_html( $notice )
		);
	}

	echo '<div class="tsd__grid">';

	foreach ( $designs as $slug => $design ) {
		$layout  = isset( $layouts[ $design['layout'] ] ) ? $layouts[ $design['layout'] ] : reset( $layouts );
		$palette = terrasuite_agency_style_palette( $design['style'] );
		$is_live = $slug === $current;

		printf(
			'<div class="tsd__card%s">',
			$is_live ? ' is-live' : ''
		);

		echo '<div class="tsd__preview">' . terrasuite_agency_wireframe( $layout, $palette ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- drawn above, values escaped there.

		echo '<div class="tsd__body">';
		echo '<h2>' . esc_html( $design['label'] );

		if ( $is_live ) {
			echo ' <span class="tsd__live">' . esc_html__( 'in use', 'terrasuite-agency' ) . '</span>';
		}

		echo '</h2>';
		echo '<p class="tsd__note">' . esc_html( $design['note'] ) . '</p>';
		echo '<p class="tsd__layout">' . esc_html( $layout['label'] ) . ' &middot; ' . esc_html( $layout['note'] ) . '</p>';

		echo '<div class="tsd__swatches">';

		foreach ( array( 'ink', 'accent', 'surface', 'base' ) as $key ) {
			printf( '<span style="background:%s"></span>', esc_attr( $palette[ $key ] ) );
		}

		echo '</div>';

		if ( ! $is_live ) {
			echo '<form method="post">';
			wp_nonce_field( 'terrasuite-design' );
			printf( '<input type="hidden" name="terrasuite_design" value="%s">', esc_attr( $slug ) );
			printf( '<button type="submit" class="button button-primary">%s</button>', esc_html__( 'Use this design', 'terrasuite-agency' ) );
			echo '</form>';
		}

		echo '</div></div>';
	}

	echo '</div>';

	/*
	 * AND THE TWO HALVES, SEPARATELY.
	 *
	 * The twenty-four named designs are pairings somebody chose; they are not
	 * the only pairings allowed. An agency that wants the Estate arrangement in
	 * Coastal blue can have it, and until now could not.
	 *
	 * Each gallery marks the ones that go with what is already chosen, taken
	 * from the named designs themselves - so the advice is the same advice,
	 * whichever half they pick first.
	 */
	$suggested_styles  = terrasuite_agency_styles_for_layout( $live_layout );
	$suggested_layouts = terrasuite_agency_layouts_for_style( $live_style );

	echo '<h2 class="tsd__heading">' . esc_html__( 'Or choose the two separately', 'terrasuite-agency' ) . '</h2>';
	echo '<p class="tsd__intro">' . esc_html__( 'The arrangement and the colours are independent. Change one and the other stays as it is; the ones we think go well together are marked.', 'terrasuite-agency' ) . '</p>';

	/* ---------------------------------------------------------- arrangement */

	echo '<h3 class="tsd__subhead">' . esc_html__( 'The arrangement', 'terrasuite-agency' ) . '</h3>';
	echo '<div class="tsd__grid tsd__grid--half">';

	$palette = terrasuite_agency_style_palette( $live_style );

	foreach ( $layouts as $slug => $layout ) {
		$is_live    = $slug === $live_layout;
		$is_advised = in_array( $slug, $suggested_layouts, true );

		printf( '<div class="tsd__card%s">', $is_live ? ' is-live' : '' );
		echo '<div class="tsd__preview">' . terrasuite_agency_wireframe( $layout, $palette ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- drawn above, values escaped there.
		echo '<div class="tsd__body">';
		echo '<h2>' . esc_html( $layout['label'] );

		if ( $is_live ) {
			echo ' <span class="tsd__live">' . esc_html__( 'in use', 'terrasuite-agency' ) . '</span>';
		}

		echo '</h2>';

		if ( $is_advised && ! $is_live ) {
			echo '<p class="tsd__advised">' . esc_html__( 'Goes well with your colours', 'terrasuite-agency' ) . '</p>';
		}

		echo '<p class="tsd__note">' . esc_html( $layout['note'] ) . '</p>';

		if ( ! $is_live ) {
			echo '<form method="post">';
			wp_nonce_field( 'terrasuite-design' );
			printf( '<input type="hidden" name="terrasuite_layout" value="%s">', esc_attr( $slug ) );
			printf( '<button type="submit" class="button">%s</button>', esc_html__( 'Use this arrangement', 'terrasuite-agency' ) );
			echo '</form>';
		}

		echo '</div></div>';
	}

	echo '</div>';

	/* -------------------------------------------------------------- colours */

	echo '<h3 class="tsd__subhead">' . esc_html__( 'The colours', 'terrasuite-agency' ) . '</h3>';
	echo '<div class="tsd__grid tsd__grid--half">';

	$live_layout_preset = isset( $layouts[ $live_layout ] ) ? $layouts[ $live_layout ] : reset( $layouts );

	foreach ( $styles as $slug => $label ) {
		$is_live    = $slug === $live_style;
		$is_advised = in_array( $slug, $suggested_styles, true );
		$colours    = terrasuite_agency_style_palette( $slug );

		printf( '<div class="tsd__card%s">', $is_live ? ' is-live' : '' );

		// Drawn in the arrangement they already have, so what changes between
		// one card and the next is only the thing being chosen.
		echo '<div class="tsd__preview">' . terrasuite_agency_wireframe( $live_layout_preset, $colours ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- drawn above, values escaped there.

		echo '<div class="tsd__body">';
		echo '<h2>' . esc_html( $label );

		if ( $is_live ) {
			echo ' <span class="tsd__live">' . esc_html__( 'in use', 'terrasuite-agency' ) . '</span>';
		}

		echo '</h2>';

		if ( $is_advised && ! $is_live ) {
			echo '<p class="tsd__advised">' . esc_html__( 'Goes well with your arrangement', 'terrasuite-agency' ) . '</p>';
		}

		echo '<div class="tsd__swatches">';

		foreach ( array( 'ink', 'accent', 'surface', 'base' ) as $key ) {
			printf( '<span style="background:%s"></span>', esc_attr( $colours[ $key ] ) );
		}

		echo '</div>';

		if ( ! $is_live ) {
			echo '<form method="post">';
			wp_nonce_field( 'terrasuite-design' );
			printf( '<input type="hidden" name="terrasuite_style" value="%s">', esc_attr( $slug ) );
			printf( '<button type="submit" class="button">%s</button>', esc_html__( 'Use these colours', 'terrasuite-agency' ) );
			echo '</form>';
		}

		echo '</div></div>';
	}

	echo '</div>';

	/*
	 * The footer credit, at the foot.
	 *
	 * It used to sit between the "Designs we have put together" heading and the
	 * first row of designs, so the first thing under a heading about designs
	 * was a checkbox about the footer. It is a small preference about one line
	 * of text and it belongs after the choosing, not in front of it.
	 */
	printf(
		'<form method="post" class="tsd__credit">%s<label><input type="checkbox" name="terrasuite_credit" value="1"%s> %s</label> <button type="submit" name="terrasuite_credit_save" value="1" class="button">%s</button></form>',
		wp_nonce_field( 'terrasuite-design', '_wpnonce', true, false ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated markup.
		checked( terrasuite_agency_shows_credit(), true, false ),
		esc_html__( 'Show "Powered by TerraSuite" in the footer', 'terrasuite-agency' ),
		esc_html__( 'Save', 'terrasuite-agency' )
	);

	echo '</div>';
}

/**
 * The screen's own styles. Small enough to print, and nothing else uses them.
 *
 * @param string $hook Screen.
 * @return void
 */
function terrasuite_agency_design_styles( $hook ) {
	if ( terrasuite_agency_design_hook() !== $hook || '' === $hook ) {
		return;
	}

	$css = '
	.tsd__intro { font-size: 14px; }
	.tsd__credit {
		display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
		margin-top: 16px; padding: 12px 16px;
		background: #fff; border: 1px solid #dcdcde; border-radius: 10px;
	}
	.tsd__credit label { display: flex; align-items: center; gap: 7px; font-size: 13px; }
	.tsd__grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(min(100%, 22rem), 1fr));
		gap: 20px;
		margin-top: 20px;
	}
	.tsd__card {
		background: #fff;
		border: 1px solid #dcdcde;
		border-radius: 10px;
		overflow: hidden;
		display: flex;
		flex-direction: column;
		box-shadow: 0 1px 2px rgba(0,0,0,.04);
	}
	.tsd__card.is-live { border-color: #2271b1; box-shadow: 0 0 0 2px rgba(34,113,177,.18); }
	.tsd__preview { border-bottom: 1px solid #f0f0f1; line-height: 0; }
	.tsd-wire { width: 100%; height: auto; display: block; }
	.tsd__body { padding: 14px 16px 16px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
	.tsd__body h2 { margin: 0; font-size: 15px; display: flex; align-items: center; gap: 8px; }
	.tsd__live {
		font-size: 11px; font-weight: 600; text-transform: uppercase;
		letter-spacing: .05em; color: #2271b1;
		background: rgba(34,113,177,.1); padding: 2px 7px; border-radius: 999px;
	}
	.tsd__note { margin: 0; color: #3c434a; font-size: 13px; }
	.tsd__heading { margin: 34px 0 4px; font-size: 18px; }
	.tsd__subhead { margin: 22px 0 10px; font-size: 14px; text-transform: uppercase; letter-spacing: .06em; color: #646970; }
	/* The two halves are simpler cards than a named design, so more of them
	   fit across and the whole choice is on one screen. */
	.tsd__grid--half { grid-template-columns: repeat(auto-fill, minmax(215px, 1fr)); }
	.tsd__advised {
		margin: 0;
		font-size: 12px;
		font-weight: 600;
		color: #1d6b3f;
	}
	.tsd__advised::before { content: "\2713"; margin-right: 5px; }
	.tsd__layout { margin: 0; color: #646970; font-size: 12px; }
	.tsd__swatches { display: flex; gap: 5px; margin-top: auto; }
	.tsd__swatches span {
		width: 20px; height: 20px; border-radius: 5px;
		border: 1px solid rgba(0,0,0,.12); display: block;
	}
	.tsd__body form { margin-top: 4px; }
	';

	wp_register_style( 'terrasuite-designs', false, array(), '1.0.0' );
	wp_enqueue_style( 'terrasuite-designs' );
	wp_add_inline_style( 'terrasuite-designs', $css );
}
add_action( 'admin_enqueue_scripts', 'terrasuite_agency_design_styles' );

<?php
/**
 * TerraSuite Agency.
 *
 * A block theme for estate agencies, built to what sites in this trade that
 * actually convert are observed to do rather than to taste: one hero committed
 * to a single decision with the search as the dominant element, navigation
 * split by what the visitor came to do, and the valuation never far away.
 *
 * The look is carried entirely by tokens in theme.json, so a style variation
 * in styles/ restyles the whole site - including the plugin's own screens -
 * by redefining ten values.
 *
 * @package TerraSuiteAgency
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/designs.php';
require_once __DIR__ . '/inc/layout-render.php';

if ( is_admin() ) {
	require_once __DIR__ . '/inc/design-admin.php';
}

/**
 * Set the theme up.
 *
 * @return void
 */
function terrasuite_agency_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/agency.css' );
}
add_action( 'after_setup_theme', 'terrasuite_agency_setup' );

/**
 * Load the stylesheet, versioned by its own modification time so a change
 * reaches a browser behind a cache without anybody remembering to bump it.
 *
 * @return void
 */
function terrasuite_agency_assets() {
	$version = function ( $file ) {
		$path = get_theme_file_path( $file );

		return is_readable( $path ) ? (string) filemtime( $path ) : '1.0.0';
	};

	wp_enqueue_style(
		'terrasuite-agency',
		get_theme_file_uri( 'assets/agency.css' ),
		array(),
		$version( 'assets/agency.css' )
	);

	// The arrangement, loaded after the base so a layout can override it.
	wp_enqueue_style(
		'terrasuite-agency-layouts',
		get_theme_file_uri( 'assets/layouts.css' ),
		array( 'terrasuite-agency' ),
		$version( 'assets/layouts.css' )
	);

	$layout = terrasuite_agency_current_layout();

	/*
	 * Only the layouts that carry a showcase pay for the script.
	 *
	 * This used to load on a property page as well, "because the gallery uses
	 * the same code" - a connection that was never actually made. It also meant
	 * a theme in the WordPress.org directory loading a script on a post type it
	 * does not register, on a premise that was untrue. The gallery is the
	 * plugin's, and so is its behaviour now.
	 */
	if ( 'carousel' === $layout['hero'] ) {
		wp_enqueue_script(
			'terrasuite-agency-carousel',
			get_theme_file_uri( 'assets/carousel.js' ),
			array(),
			$version( 'assets/carousel.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'terrasuite_agency_assets' );

/**
 * Block styles an editor picks from the sidebar instead of typing a class.
 *
 * @return void
 */
function terrasuite_agency_block_styles() {
	$styles = array(
		array( 'core/group', 'ts-card', __( 'Card', 'terrasuite-agency' ) ),
		array( 'core/group', 'ts-panel', __( 'Panel', 'terrasuite-agency' ) ),
		array( 'core/paragraph', 'ts-eyebrow', __( 'Eyebrow', 'terrasuite-agency' ) ),
	);

	foreach ( $styles as $style ) {
		register_block_style( $style[0], array( 'name' => $style[1], 'label' => $style[2] ) );
	}
}
add_action( 'init', 'terrasuite_agency_block_styles' );

/**
 * A page on this site, by its path, or nothing.
 *
 * @param string $path Page path, without slashes.
 * @return WP_Post|null
 */
function terrasuite_agency_page( $path ) {
	static $found = array();

	if ( ! array_key_exists( $path, $found ) ) {
		$found[ $path ] = get_page_by_path( $path );
	}

	return $found[ $path ];
}

/**
 * Whether a menu should carry a link to one of the plugin's pages.
 *
 * THE PAGE EXISTING IS NOT ENOUGH.
 *
 * A page whose feature the licence does not include still renders - it says
 * that part of the site is not switched on - so linking to it from the header
 * and the footer of every page advertises something the visitor cannot have.
 * The knowledgebase was the plain case: absent at the free tier, and still
 * offered as "Advice" in two menus.
 *
 * A theme running with no plugin behind it has no features to ask about, and
 * no pages of these names either, so it falls through to the page check alone
 * and behaves exactly as it did before.
 *
 * @param string $path    Page path, without slashes.
 * @param string $feature Feature key the page needs, or '' when it needs none.
 * @return bool
 */
function terrasuite_agency_link( $path, $feature = '' ) {
	if ( ! terrasuite_agency_page( $path ) ) {
		return false;
	}

	if ( '' === $feature || ! class_exists( 'TerraSuite_Features' ) ) {
		return true;
	}

	return (bool) TerraSuite_Features::has( $feature );
}

/**
 * The two things somebody on a phone came to do, always within reach.
 *
 * Rendered from the theme rather than put in page content, so it is on every
 * page and cannot be deleted by accident while editing one of them.
 *
 * Every other link the theme carries is inside the header or footer pattern,
 * which a site owner can edit. This one they cannot, so it is the one place
 * where a link to a page that was never created would be a dead end nobody
 * could remove: on a fresh install, with no property plugin and no pages of
 * these names, it prints nothing at all rather than two links to a 404. Each
 * half appears on its own as soon as its page exists.
 *
 * @return void
 */
function terrasuite_agency_mobile_bar() {
	if ( is_admin() ) {
		return;
	}

	$links = array();

	if ( terrasuite_agency_page( 'property-search' ) ) {
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( home_url( '/property-search/' ) ),
			esc_html__( 'Search properties', 'terrasuite-agency' )
		);
	}

	if ( terrasuite_agency_page( 'free-valuation' ) ) {
		$links[] = sprintf(
			'<a class="is-primary" href="%s">%s</a>',
			esc_url( home_url( '/free-valuation/' ) ),
			esc_html__( 'Book a valuation', 'terrasuite-agency' )
		);
	}

	if ( ! $links ) {
		return;
	}

	printf(
		'<nav class="ts-mobile-bar" aria-label="%s">%s</nav>',
		esc_attr__( 'Quick actions', 'terrasuite-agency' ),
		implode( '', $links ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above, each part escaped there.
	);
}
add_action( 'wp_footer', 'terrasuite_agency_mobile_bar' );

/**
 * Tell the plugin's guide what designs this theme offers.
 *
 * The plugin has no idea what a design is, and should not: an agency running a
 * different theme should get a manual that does not describe a screen they do
 * not have. So the theme answers, and the guide prints what it is told.
 *
 * @param array $designs Designs from anywhere else.
 * @return array
 */
function terrasuite_agency_guide_designs( $designs ) {
	foreach ( terrasuite_agency_designs() as $design ) {
		$designs[ $design['label'] ] = $design['note'];
	}

	return $designs;
}
add_filter( 'terrasuite_guide_designs', 'terrasuite_agency_guide_designs' );

/**
 * Report this theme's translations on the Languages screen.
 *
 * @param array $domains Text domains.
 * @return array
 */
function terrasuite_agency_text_domain( $domains ) {
	$domains['terrasuite-agency'] = array( untrailingslashit( get_template_directory() ) . '/languages' );

	return $domains;
}
add_filter( 'terrasuite_text_domains', 'terrasuite_agency_text_domain' );

/**
 * Load this theme's translations.
 *
 * A block theme has fewer strings than a plugin, but the ones it has are the
 * words in the header and the footer - the most visible text on the site. A
 * site switched to Norwegian with an English "Book a valuation" in the contact
 * bar looks broken rather than bilingual.
 *
 * @return void
 */
function terrasuite_agency_load_textdomain() {
	load_theme_textdomain( 'terrasuite-agency', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'terrasuite_agency_load_textdomain' );

/**
 * This theme builds its header from the Menu screen.
 *
 * Saying so is what makes that screen do anything: a theme that keeps its menu
 * in a wp_navigation post is edited a different way, and a screen that edited
 * a list nobody read would be worse than no screen.
 *
 * @return bool
 */
function terrasuite_agency_uses_menu() {
	return true;
}
add_filter( 'terrasuite_theme_uses_menu', 'terrasuite_agency_uses_menu' );

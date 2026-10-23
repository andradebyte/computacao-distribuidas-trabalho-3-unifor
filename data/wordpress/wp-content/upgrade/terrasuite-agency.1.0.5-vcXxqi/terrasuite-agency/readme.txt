=== TerraSuite Agency ===

Contributors: havenswift
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: block-patterns, block-styles, full-site-editing, style-variations, wide-blocks, grid-layout, one-column, custom-colors, custom-menu, editor-style, featured-images, threaded-comments, translation-ready

A block theme for estate and letting agencies, with twenty-four named designs
that change the arrangement of the page as well as its colours.

== Description ==

TerraSuite Agency is a block theme for the kind of site an estate or letting
agency actually needs: one clear thing to do on the front page, navigation
split by what a visitor came for, and a way of getting in touch that is never
more than a scroll away.

= Twenty-four designs, not twenty-four palettes =

Most themes that offer variations offer one layout painted several ways. This
one separates the two. A **layout** changes the arrangement: whether the hero
is a photograph, a split panel, a moving showcase or a plain colour block;
whether listings are a grid, a list, a masonry or a picture with the detail
over it; whether the header sits left, centred, or under a contact bar. A
**style** changes the tokens: colour, type, spacing and radius. Nine layouts
and nine palettes are paired into twenty-four named designs, chosen in
Appearance > Designs from a drawn preview of each. Switching design never
touches a template and never touches your content, so you can try one and
change your mind.

The nine palettes are also registered as ordinary style variations, so anybody
who prefers to work in Appearance > Editor > Styles can mix and match there
instead.

= Built for the trade, not adapted to it =

* A hero committed to one decision, with the search as the dominant element
  rather than a small box in a corner.
* Navigation split by intent: buying, renting, selling, area guides, advice.
* A valuation call to action in the header of every page, and a two-button
  bar fixed to the foot of the screen on a telephone.
* Listing cards and a wide property layout, so the most important page on the
  site is not a narrow column with the photograph in it twice.
* An editor stylesheet, so what you see while writing is what visitors get.

= Nothing loaded from anywhere else =

No fonts, scripts, styles, icons or images are fetched from a third party. The
type is set in system stacks that are already on the reader's device, the front
page showcase is about a hundred lines of the theme's own JavaScript, and the
theme weighs under 300KB in total. Nothing about a visitor is sent anywhere.

= Works with TerraSuite, and works without it =

The theme is a complete, standalone WordPress block theme: install it on its
own and you get a working site with a front page, blog, archives, search,
single posts, pages and a 404.

It is also the reference theme for the free
[TerraSuite](https://wordpress.org/plugins/terrasuite/) plugin, which adds the
estate agency side of things: property listings, a filterable search, a
self-hosted map, enquiries, applicant matching and a viewings calendar. When
that plugin is present the theme adds a property layout, a featured-property
showcase and browse-by-area tiles. When it is not, those simply do not appear
and everything else behaves normally. Neither one requires the other.

== Installation ==

1. In your admin, go to Appearance > Themes > Add New.
2. Search for "TerraSuite Agency", then click Install and Activate.
3. Go to Appearance > Designs and pick one of the twenty-four.

That is the whole setup. To add property listings, install the free TerraSuite
plugin from Plugins > Add New.

== Frequently Asked Questions ==

= Do I need the TerraSuite plugin? =

No. The theme is a complete block theme on its own. The plugin adds property
listings and the agency features; the theme notices when it is there and adds
a property layout to match.

= Where do I change the colours? =

Appearance > Designs changes the whole design, arrangement included.
Appearance > Editor > Styles changes only the colours and type, and gives you
the nine palettes as ordinary style variations to edit however you like.

= Does the theme call out to Google Fonts or any other service? =

No. Nothing is loaded from a third party, and nothing about your visitors
leaves your server.

= Is it translated? =

The theme's own strings are in the languages folder and translate through
WordPress.org like any other theme. Header and footer wording is generated in
PHP patterns rather than static HTML precisely so that it can be translated.

== Copyright ==

TerraSuite Agency WordPress Theme, (C) 2026 Havenswift Hosting.
TerraSuite Agency is distributed under the terms of the GNU GPL v2 or later.

This theme bundles no third-party code, fonts, icons or images.

The theme screenshot shows the theme's own front page. The photograph in it is
released under Creative Commons CC0:

    "Goddards country house from the west" by Suspensa Vix
    Source: https://commons.wikimedia.org/w/index.php?curid=83752876
    License: CC0 1.0 Universal
    License URI: https://creativecommons.org/publicdomain/zero/1.0/

== Changelog ==

= 1.0.5 =
* The Theme URI header has been removed. The page it pointed at is not public yet, and a header that leads to a sign-in screen is no use to anybody. It will come back when the page is live. The footer credit, which appears only if you switch it on, falls through to the author's address in the meantime.

= 1.0.4 =
* The property template is renamed, because the plugin's property post type is. TerraSuite 1.0.2 renames it from ts_property to terrasuite_property, at WordPress.org's request for a longer prefix, and a template is named after the type it draws. Without this the theme stops matching property pages on that version of the plugin.

= 1.0.3 =
* The Theme URI points at a page about this theme rather than at a general one.
* The Spanish translation covers the whole theme: the Designs screen, the 404 and no-results pages, the blog list and the footer. A catalogue file that nothing read has been removed.

= 1.0.2 =
* The credit link in the footer, which appears only if you switch it on, now points at the theme's own address as recorded in style.css rather than at a hardcoded one.

= 1.0.1 =
* The property template carried a developer note written as a plain comment. It has been taken out: it was being printed into the page source that visitors receive, and it belongs in the project's own notes.
* Footer column headings are now third-level rather than fourth, so the heading order on every page runs without a gap.
* The header and footer no longer offer a menu item for a part of the site that is not switched on.
* The footer copyright line is translated into Spanish.

= 1.0.0 =
* Initial release.

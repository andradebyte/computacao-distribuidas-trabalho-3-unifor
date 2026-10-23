<?php
/**
 * Title: Front page
 * Slug: terrasuite-agency/front-page
 * Inserter: no
 * Description: The body of the front page, which is a page's own content or the list of posts depending on what the site is set to show.
 *
 * WHY THIS BRANCHES.
 *
 * WordPress gives front-page.html priority over home.html for the front page
 * whether that front page is a static page or the blog. The template was
 * written for the first case - an agency's front page, whose hero and search
 * need the full width - and printed nothing but wp:post-content. On a site
 * left at the default of showing posts, that rendered the body of one post
 * with no title above it and no way to reach any other. It is the first screen
 * of a fresh install, so it was also the first thing anybody reviewing this
 * theme would see.
 *
 * A block template cannot branch. A pattern is PHP, so it can.
 *
 * @package TerraSuiteAgency
 */

if ( 'page' === get_option( 'show_on_front' ) ) : ?>
<!-- wp:post-content {"layout":{"type":"constrained","contentSize":"1360px"}} /-->
<?php else : ?>
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1360px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:pattern {"slug":"terrasuite-agency/posts"} /-->
</div>
<!-- /wp:group -->
<?php endif; ?>

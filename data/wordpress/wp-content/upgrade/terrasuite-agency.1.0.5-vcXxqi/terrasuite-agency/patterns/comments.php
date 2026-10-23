<?php
/**
 * Title: Comments
 * Slug: terrasuite-agency/comments
 * Inserter: no
 * Description: The comment thread and the reply form, for posts and pages that have comments open.
 *
 * A PHP pattern rather than markup repeated in two templates, for the reason
 * set out in header.php: a block theme's .html is static and gettext never
 * sees it, so the one heading that is not written by a visitor would be
 * English on a Spanish site for ever.
 *
 * The whole thing renders nothing at all when comments are closed and there
 * are none, which is the usual state of an agency's pages.
 *
 * @package TerraSuiteAgency
 */

?>
<!-- wp:comments {"className":"ts-comments","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} -->
<div class="wp-block-comments ts-comments" style="margin-top:var(--wp--preset--spacing--60)">

<!-- wp:comments-title {"level":2,"fontSize":"x-large"} /-->

<!-- wp:comment-template -->
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"48px"} -->
		<div class="wp-block-column" style="flex-basis:48px">
			<!-- wp:avatar {"size":48,"style":{"border":{"radius":"999px"}}} /-->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:comment-author-name {"fontSize":"small","style":{"typography":{"fontWeight":"600"}}} /-->
			<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"bottom":"0.5rem"}}}} -->
			<div class="wp-block-group" style="margin-bottom:0.5rem">
				<!-- wp:comment-date {"fontSize":"small","textColor":"muted"} /-->
				<!-- wp:comment-edit-link {"fontSize":"small","textColor":"muted"} /-->
			</div>
			<!-- /wp:group -->
			<!-- wp:comment-content /-->
			<!-- wp:comment-reply-link {"fontSize":"small"} /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<!-- wp:comments-pagination-previous /-->
	<!-- wp:comments-pagination-numbers /-->
	<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} /-->

</div>
<!-- /wp:comments -->

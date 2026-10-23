/*
 * Carousels, without a library.
 *
 * Two of them: the showcase on the front page of the layouts that use one, and
 * the gallery on a property page. Both are the same mechanism - a flex track
 * that gets translated - and both are written so that the markup is already
 * correct before any of this runs. If the script fails or is blocked, the
 * first slide is on screen, the links work, and the page is not broken. That
 * is the whole reason not to reach for a library here.
 *
 * Deliberate choices:
 *
 *   - It stops on hover, on focus, and when the tab is not visible. A carousel
 *     that keeps moving while somebody is reading a price is a carousel that
 *     loses the enquiry.
 *   - It does not auto-advance at all for a visitor who has asked for reduced
 *     motion. They still get the arrows and the dots.
 *   - Arrow keys work when the carousel has focus, and every control is a real
 *     button with a real label.
 */
( function () {
	'use strict';

	var still = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function build( root, options ) {
		var track = root.querySelector( options.track );
		var slides = track ? track.children : [];

		if ( ! track || slides.length < 2 ) {
			return;
		}

		var index = 0;
		var timer = null;
		var dots = root.querySelectorAll( options.dot );

		function show( next ) {
			index = ( next + slides.length ) % slides.length;
			track.style.transform = 'translateX(-' + ( index * 100 ) + '%)';

			for ( var i = 0; i < slides.length; i++ ) {
				// Anything off screen is out of the tab order, or a keyboard
				// user tabs into links they cannot see.
				slides[ i ].setAttribute( 'aria-hidden', i === index ? 'false' : 'true' );

				if ( slides[ i ].tagName === 'A' ) {
					slides[ i ].tabIndex = i === index ? 0 : -1;
				}
			}

			for ( var d = 0; d < dots.length; d++ ) {
				dots[ d ].setAttribute( 'aria-current', d === index ? 'true' : 'false' );
			}
		}

		function start() {
			if ( still || timer || ! options.interval ) {
				return;
			}

			timer = window.setInterval( function () {
				show( index + 1 );
			}, options.interval );
		}

		function stop() {
			window.clearInterval( timer );
			timer = null;
		}

		root.addEventListener( 'mouseenter', stop );
		root.addEventListener( 'mouseleave', start );
		root.addEventListener( 'focusin', stop );
		root.addEventListener( 'focusout', start );

		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stop();
			} else {
				start();
			}
		} );

		root.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'ArrowLeft' ) {
				stop();
				show( index - 1 );
			} else if ( event.key === 'ArrowRight' ) {
				stop();
				show( index + 1 );
			}
		} );

		var prev = root.querySelector( options.prev );
		var next = root.querySelector( options.next );

		if ( prev ) {
			prev.addEventListener( 'click', function () {
				stop();
				show( index - 1 );
			} );
		}

		if ( next ) {
			next.addEventListener( 'click', function () {
				stop();
				show( index + 1 );
			} );
		}

		for ( var d = 0; d < dots.length; d++ ) {
			( function ( n ) {
				dots[ n ].addEventListener( 'click', function () {
					stop();
					show( n );
				} );
			} )( d );
		}

		// Swipe, without a gesture library: one touchstart, one touchend, and
		// a threshold big enough that a scroll is not read as a swipe.
		var startX = null;

		root.addEventListener( 'touchstart', function ( event ) {
			startX = event.changedTouches[ 0 ].clientX;
			stop();
		}, { passive: true } );

		root.addEventListener( 'touchend', function ( event ) {
			if ( startX === null ) {
				return;
			}

			var delta = event.changedTouches[ 0 ].clientX - startX;

			if ( Math.abs( delta ) > 45 ) {
				show( index + ( delta < 0 ? 1 : -1 ) );
			}

			startX = null;
			start();
		}, { passive: true } );

		show( 0 );
		start();
	}

	function init() {
		var showcases = document.querySelectorAll( '.ts-showcase' );

		for ( var i = 0; i < showcases.length; i++ ) {
			build( showcases[ i ], {
				track: '.ts-showcase__track',
				dot: '.ts-showcase__dot',
				prev: '.ts-showcase__arrow--prev',
				next: '.ts-showcase__arrow--next',
				interval: 6000
			} );
		}

		/*
		 * There was a second block here for `.ts-gallery`, the property page
		 * gallery. Nothing has ever emitted that class - the gallery markup
		 * belongs to the plugin and is `.terrasuite-gallery` - so it selected
		 * nothing and returned, on every site, for as long as it existed. The
		 * gallery behaviour now lives in the plugin beside the markup it acts
		 * on, so an agency running any other theme gets it too.
		 *
		 * What is left here is the front page showcase, which really is this
		 * theme's own: it is part of a layout choice, not part of a property.
		 */
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

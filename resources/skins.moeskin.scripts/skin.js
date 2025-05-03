const
	checkboxHack = require( './checkbox.js' ),
	CHECKBOX_HACK_CONTAINER_SELECTOR = '.mw-checkbox-hack-container',
	CHECKBOX_HACK_CHECKBOX_SELECTOR = '.mw-checkbox-hack-checkbox',
	CHECKBOX_HACK_BUTTON_SELECTOR = '.mw-checkbox-hack-button',
	CHECKBOX_HACK_TARGET_SELECTOR = '.mw-checkbox-hack-target';

	url = window.location.href;
	header = document.querySelector('.moeskin-header');
	dummy = document.createElement('input');
	search = require( './search.js' );

/**
 * Add a class to indicate that page title is outside of viewport
 *
 * @param {Document} document
 * @return {void}
 */
function adjustHeader() {
	try {
		header.classList.toggle('is-at-top', window.pageYOffset <= 100);
		header.classList.toggle('not-at-top', window.pageYOffset > 100);
	} catch (error) {
		location.reload();
	}
}

addEventListener('scroll', adjustHeader);
adjustHeader();

/**
 * Add the ability for users to toggle dropdown menus using the enter key (as
 * well as space) using core's checkboxHack.
 *
 * Based on Vector
 */
function bind() {
	// Search for all dropdown containers using the CHECKBOX_HACK_CONTAINER_SELECTOR.
	const containers = document.querySelectorAll( CHECKBOX_HACK_CONTAINER_SELECTOR );

	containers.forEach( ( container ) => {
		const
			checkbox = container.querySelector( CHECKBOX_HACK_CHECKBOX_SELECTOR ),
			button = container.querySelector( CHECKBOX_HACK_BUTTON_SELECTOR ),
			target = container.querySelector( CHECKBOX_HACK_TARGET_SELECTOR );

		if ( !( checkbox && button && target ) ) {
			return;
		}

		checkboxHack.bind( window, checkbox, button, target );
	} );
}

/**
 * T295085: Close all dropdown menus when page is unloaded to prevent them from
 * being open when navigating back to a page.
 *
 * Based on Vector
 */
function bindCloseOnUnload() {
	addEventListener( 'beforeunload', () => {
		const checkboxes = document.querySelectorAll( CHECKBOX_HACK_CHECKBOX_SELECTOR + ':checked' );

		checkboxes.forEach( ( checkbox ) => {
			/** @type {HTMLInputElement} */ ( checkbox ).checked = false;
		} );
	} );
}

/**
 * Click Event
 */
window.addEventListener('click', function(event) {
	const { target } = event;

	// Make the red link do not jump to the creation page.
	if (target.matches('a.new')) {
		event.preventDefault();
		const href = target.getAttribute('href').replace('&action=edit&redlink=1', '');
		target.setAttribute('href', href);
		window.location.href = href;
  	}

	// Execute open toc for toctoggle.
	else if (target.matches('#toctoggle')) {
		document.getElementById("toctogglecheckbox").checked = true;
	}

	// Share current page to clipboard.
	else if (target.matches('#sharedtoggle')) {
		const dummy = document.createElement('textarea');
		dummy.value = url;
		document.body.appendChild(dummy);
		dummy.select();
		document.execCommand('copy');
		document.body.removeChild(dummy);
	}
});

/**
 * @param {Window} window
 * @return {void}
 */
function main( window ) {
	bind();
	bindCloseOnUnload();
	search.init( window );

	// Handle ToC
	// TODO: There must be a cleaner way to do this
	const tocContainer = document.getElementById( 'toc' );

	if ( tocContainer ) {
		const toc = require( './tableOfContents.js' );
		toc.init();

		checkboxHack.bind(
			window,
			document.getElementById( 'toctogglecheckbox' ),
			tocContainer.querySelector( '.toctogglelabel' ),
			tocContainer.querySelector( 'ul' )
		);
	}

	mw.loader.load( 'skins.moeskin.preferences' );
}

main( window );

/**
 * Wait for first paint before calling this function.
 *
 * @param {Document} document
 * @return {void}
 */
function enableCssAnimations( document ) {
	document.documentElement.classList.add( 'moeskin-animations-ready' );
}

if ( document.readyState === 'interactive' || document.readyState === 'complete' ) {
	enableCssAnimations( window.document );
} else {
	document.addEventListener( 'DOMContentLoaded', function () {
		enableCssAnimations( window.document );
	} );
}


/**
 * New Search Page
 */
if ( mw.config.get( "wgCanonicalSpecialPageName" ) === "Search" ) {
	const
		searchInput = document.getElementById( 'searchInput' );
		oouiText = document.querySelector( "#ooui-php-1" );

	searchInput.value = oouiText.value;
	searchInput.style.visibility = 'inherit';

	function performSearch() {
		oouiText.value = searchInput.value;
		oouiText.form.submit();
	}

	searchInput.addEventListener('keydown', function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			performSearch();
		}
	});

	window.addEventListener('click', function(event) {
		const searchIcon = document.querySelector("#searchform .moeskin-search__icon");
		const typeaheadLink = document.querySelector("#moeskin-typeahead-fulltext a");
	
		if (event.target === searchIcon || event.target === typeaheadLink) {
			event.preventDefault();
			performSearch();
		}
	});
}

/**
 * Set up loading indicator
 */
window.addEventListener('beforeunload', () => {
    document.documentElement.classList.add('moeskin-loading');
}, false);

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        document.documentElement.classList.remove('moeskin-loading');
    }
});

window.onload = function() {
    document.documentElement.classList.remove('moeskin-loading');
};
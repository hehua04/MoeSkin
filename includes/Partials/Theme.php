<?php
/**
 * MoeSkin - A responsive skin developed for the Moegirlpedia
 *
 * This file is part of MoeSkin.
 *
*/

declare( strict_types=1 );

namespace MediaWiki\Skins\MoeSkin\Partials;

/**
 * Theme switcher partial of Skin MoeSkin
*/
final class Theme extends Partial {

	/**
	 * Sets the corresponding theme class on the <html> element
	 * If the theme is set to auto, the theme switcher script will be added
	 *
	 * @param array &$options
	*/
	public function setSkinTheme( array &$options ) {
		$out = $this->out;

		// Set theme to site theme
		$theme = $this->getConfigValue( 'MoeSkinThemeDefault' ) ?? 'auto';

		// Add HTML class based on theme set
		$out->addHtmlClasses( 'skin-moeskin-' . $theme );
	}
}

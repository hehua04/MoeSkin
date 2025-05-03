<?php
/**
 * MoeSkin - A responsive skin developed for the Moegirlpedia
 *
 * This file is part of MoeSkin.
 *
*/

declare( strict_types=1 );

namespace MediaWiki\Skins\MoeSkin\Partials;

use ResourceLoaderSkinModule;

/**
 * Drawer partial of Skin MoeSkin
 * Generates the following partials:
 * - Logo
 * - Drawer
 *   + Special Pages Link
 *   + Upload Link
*/
final class Logos extends Partial {
	/**
	 * Get and pick the correct logo based on types and variants
	 * Based on getLogoData() in MW 1.36
	 *
	 * @return array
	*/
	public function getLogoData(): array {
		$skin = $this->skin;

		$logoData = ResourceLoaderSkinModule::getAvailableLogos( $skin->getConfig() );

		// check if the logo supports variants
		$variantsLogos = $logoData['variants'] ?? null;

		if ( $variantsLogos ) {
			$title = $this->title;
			$preferred = $title->getPageViewLanguage()->getCode();
			$variantOverrides = $variantsLogos[$preferred] ?? null;
			// Overrides the logo
			if ( $variantOverrides ) {
				foreach ( $variantOverrides as $key => $val ) {
					$logoData[$key] = $val;
				}
			}
		}

		return $logoData;
	}
}

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
 * Footer partial of Skin MoeSkin
*/
final class Footer extends Partial {

	/**
	 * Get rows that make up the footer
	 * @return array for use in Mustache template describing the footer elements.
	*/
	public function getFooterData(): array {
		$skin = $this->skin;

		$data = [];
		$footerLinks = $skin->getFooterLinksPublic();

		// Get last modified message
		if ( $footerLinks['info']['lastmod'] && isset( $footerLinks['info']['lastmod'] ) ) {
			$data['html-lastmodified'] = $footerLinks['info']['lastmod'];
			unset( $footerLinks['info']['lastmod'] );
		}

		// Based on SkinMustache
		// Backported because of 1.35 support
		foreach ( $footerLinks as $category => $links ) {
			$items = [];
			$rowId = "footer-$category";

			foreach ( $links as $key => $link ) {
				if ( $link ) {
					$items[] = [
						'id' => "$rowId-$key",
						'html' => $link,
					];
				}
			}

			$data['data-moeskin-' . $category] = [
				'id' => $rowId,
				'className' => null,
				'array-items' => $items
			];
		}

		$footerIcons = $skin->getFooterIconsPublic();

		if ( count( $footerIcons ) > 0 ) {
			$icons = [];
			foreach ( $footerIcons as $blockName => $blockIcons ) {
				$html = '';
				foreach ( $blockIcons as $key => $icon ) {
					$html .= $skin->makeFooterIcon( $icon );
				}
				if ( $html ) {
					$block = htmlspecialchars( $blockName );
					$icons[] = [
						'id' => 'footer-' . $block . 'ico',
						'html' => $html,
					];
				}
			}

			if ( count( $icons ) > 0 ) {
				$data['data-moeskin-icons'] = [
					'id' => 'footer-icons',
					'className' => 'noprint',
					'array-items' => $icons,
				];
			}
		}

		return $data;
	}
}

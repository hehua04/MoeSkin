<?php
/**
 * MoeSkin - A responsive skin developed for the Moegirlpedia
 *
 * This file is part of MoeSkin.
 *
*/

declare( strict_types=1 );

namespace MediaWiki\Skins\MoeSkin\Partials;

use Action;
use Exception;
use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use Skin;
use SkinTemplate;

final class PageTools extends Partial {
	/** @var null|array for caching purposes */
	private $languages;

	/**
	 * Render page-related tools
	 *
	 * Possible visibility conditions:
	 * * true: always visible (bool)
	 * * false: never visible (bool)
	 * * 'login': only visible if logged in (string)
	 * * 'permission-*': only visible if user has permission
	 *   e.g. permission-edit = only visible if user can edit pages
	 *
	 * @param array $parentData
	 * @return array html
	*/
	public function buildPageTools( $parentData ): array {
		$skin = $this->skin;
		$user = $this->user;

		$contentNavigation = $skin->buildContentNavigationUrlsPublic();
		$portals = $skin->buildSidebar();
		$props = [];

		if ( !method_exists( SkinTemplate::class, 'runOnSkinTemplateNavigationHooks' ) ) {
			$viewshtml = $skin->getPortletData( 'views', $contentNavigation[ 'views' ] ?? [] );
			$actionshtml = $skin->getPortletData( 'actions', $contentNavigation[ 'actions' ] ?? [] );
			$watchhtml = $skin->getPortletData( 'watch', $contentNavigation[ 'watch' ] ?? [] );
			$namespaceshtml = $skin->getPortletData( 'namespaces', $contentNavigation[ 'namespaces' ] ?? [] );
			$variantshtml = $skin->getPortletData( 'variants', $contentNavigation[ 'variants' ] ?? [] );
			$toolboxhtml = $skin->getPortletData( 'tb',  $portals['TOOLBOX'] ?? [] );
			$languageshtml = $skin->getPortletData( 'lang',  $portals['LANGUAGES'] ?? [] );
		} else {
			$viewshtml = $parentData['data-portlets']['data-views'];
			$actionshtml = $parentData['data-portlets']['data-actions'];
			$namespaceshtml = $parentData['data-portlets']['data-namespaces'];
			$variantshtml = $parentData['data-portlets']['data-variants'];
			// data-languages can be undefined index
			$languageshtml = $parentData['data-portlets']['data-languages'] ?? [];
			// For some reason core does not set this
			if ( empty( $languageshtml ) ) {
				$languageshtml['is-empty'] = true;
			}
			// Finds the toolbox in the sidebar.
			// The reason we do this is because:
			// 1. We removed some site-wide tools from the toolbar in Drawer.php,
			// now we just want the leftovers
			// 2. Toolbox is not currently avaliable as data-portlet, have to wait
			// till Desktop Improvements
			$toolboxhtml = [
				'is-empty' => true,
			];
			foreach ( $parentData['data-portlets-sidebar']['array-portlets-rest'] as $portlet ) {
				if ( $portlet['id'] === 'p-tb' ) {
					$toolboxhtml = $portlet;
					$toolboxhtml['is-empty'] = false;
					break;
				}
			}

			// Toggle label for toolbox
			if ( $toolboxhtml ) {
				$toolboxhtml['has-label'] = true;
			}

			// Toggle label for variants
			if ( $variantshtml ) {
				$variantshtml['has-label'] = true;
			}

			$props = [
				'data-page-views' => $viewshtml,
				'data-page-actions' => $actionshtml,
				'data-page-watch' => $watchhtml,
				'data-namespaces' => $namespaceshtml,
				'has-languages' => $this->shouldShowLanguages( $languageshtml, $variantshtml ),
				'is-uls-ready' => $this->shouldShowULS( $variantshtml ),
				'data-languages' => $languageshtml,
				'data-variants' => $variantshtml,
				'data-page-toolbox' => $toolboxhtml,
				'html-language-count' => $this->getLanguagesCount(),
			];
		}

		return $props;
	}

	/**
	 * Calls getLanguages with caching.
	 *
	 * Based on Vector
	 *
	 * @return array
	*/
	private function getLanguagesCached(): array {
		$skin = $this->skin;

		if ( $this->languages === null ) {
			$this->languages = $skin->getLanguages();
		}
		return $this->languages;
	}

	/**
	 * This should be upstreamed to the Skin class in core once the logic is finalized.
	 * Returns false if the page is a special page without any languages, or if an action
	 * other than view is being used.
	 *
	 * Based on Vector
	 *
	 * @return bool
	*/
	private function canHaveLanguages(): bool {
		$skin = $this->skin;

		if ( $skin->getContext()->getActionName() !== 'view' ) {
			return false;
		}

		$title = $this->title;
		// Defensive programming - if a special page has added languages explicitly, best to show it.
		if ( $title && $title->isSpecialPage() && empty( $this->getLanguagesCached() ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Show or hide the language button
	 *
	 * @param array $languageshtml
	 * @param array $variantshtml
	 * @return bool
	*/
	private function shouldShowLanguages( $languageshtml, $variantshtml ): bool {
		if ( !$this->canHaveLanguages() ) {
			return false;
		}
		// If both language and variant menu contains nothing
		if ( $languageshtml['is-empty'] && $variantshtml['is-empty'] ) {
			return false;
		}
		return true;
	}

	/**
	 * Check if UniversalLanguageSelector can be used to replace the language menu
	 *
	 * @param array $variantshtml
	 * @return bool
	*/
	private function shouldShowULS( $variantshtml ): bool {
		// ULS does not support variants
		if ( !$variantshtml['is-empty'] ) {
			return false;
		}

		return ExtensionRegistry::getInstance()->isLoaded( 'UniversalLanguageSelector' );
	}

	/**
	 * Count languages avaliable
	 * TODO: Consider having an option to count for variants?
	 *
	 * @return int
	*/
	private function getLanguagesCount(): int {
		return count( $this->getLanguagesCached() );
	}
}

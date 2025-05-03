<?php
/**
 * MoeSkin - A responsive skin developed for the Moegirlpedia
 *
 * This file is part of MoeSkin.
 *
*/

declare( strict_types=1 );

namespace MediaWiki\Skins\MoeSkin\Hooks;

use Config;
use ResourceLoaderContext;

/**
 * Hooks to run relating to the resource loader
*/
class ResourceLoaderHooks {

	/**
	 * Passes config variables to skins.moeskin.scripts ResourceLoader module.
	 * @param ResourceLoaderContext $context
	 * @param Config $config
	 * @return array
	*/
	public static function getMoeSkinResourceLoaderConfig(
		ResourceLoaderContext $context,
		Config $config
	) {
		return [
			'wgMoeSkinEnableSearch' => $config->get( 'MoeSkinEnableSearch' ),
		];
	}

	/**
	 * Passes config variables to skins.moeskin.preferences ResourceLoader module.
	 * @param ResourceLoaderContext $context
	 * @param Config $config
	 * @return array
	*/
	public static function getMoeSkinPreferencesResourceLoaderConfig(
		ResourceLoaderContext $context,
		Config $config
	) {
		return [
			'wgMoeSkinThemeDefault' => $config->get( 'MoeSkinThemeDefault' ),
		];
	}

	/**
	 * Passes config variables to skins.moeskin.search ResourceLoader module.
	 * @param ResourceLoaderContext $context
	 * @param Config $config
	 * @return array
	*/
	public static function getMoeSkinSearchResourceLoaderConfig(
		ResourceLoaderContext $context,
		Config $config
	) {
		return [
			'wgMoeSkinSearchGateway' => $config->get( 'MoeSkinSearchGateway' ),
			'wgMoeSkinSearchDescriptionSource' => $config->get( 'MoeSkinSearchDescriptionSource' ),
			'wgMoeSkinMaxSearchResults' => $config->get( 'MoeSkinMaxSearchResults' ),
			'wgScriptPath' => $config->get( 'ScriptPath' ),
			'wgSearchSuggestCacheExpiry' => $config->get( 'SearchSuggestCacheExpiry' ),
		];
	}
}

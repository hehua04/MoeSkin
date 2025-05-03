<?php
/**
 * MoeSkin - A responsive skin developed for the Moegirlpedia
 *
 * This file is part of MoeSkin.
 *
*/

declare( strict_types=1 );

namespace MediaWiki\Skins\MoeSkin\Partials;

use MediaWiki\MediaWikiServices;
use Title;
use User;
use Wikimedia\IPUtils;

/**
 * Tagline partial of Skin MoeSkin
*/
final class Tagline extends Partial {

	/**
	 * Get tagline message
	 *
	 * @return string
	*/
	public function getTagline() {
		$skin = $this->skin;
		$out = $this->out;
		$title = $this->title;

		$shortdesc = $out->getProperty( 'shortdesc' );
		$tagline = '';

		if ( $title ) {
			// Use short description if there is any
			// from Extension:ShortDescription
			if ( $shortdesc ) {
				$tagline = $shortdesc;
			} else {
				$namespaceText = $title->getNsText();
				// Check if namespaceText exists
				// Return null if main namespace or not defined
				if ( $namespaceText ) {
					$msg = $skin->msg( 'moeskin-tagline-ns-' . strtolower( $namespaceText ) );
					// Use custom message if exists
					if ( !$msg->isDisabled() ) {
						$tagline = $msg->text();
					} else {
						if ( $title->isSpecialPage() ) {
							// No tagline if special page
							$tagline = '';
						} elseif ( $title->isTalkPage() ) {
							// Use generic talk page message if talk page
							$tagline = $skin->msg( 'moeskin-tagline-ns-talk' )->text();

						} elseif ( $title->inNamespace( NS_USER ) && !$title->isSubpage() ) {
							// Build user tagline if it is a top-level user page
							$tagline = $this->buildUserTagline( $title );
						} else {
							// Fallback to site tagline
							$tagline = $skin->msg( 'tagline' )->text();
						}
					}
				} else {
					$tagline = $skin->msg( 'tagline' )->text();
				}
			}
		}

		// Apply language variant conversion
		if ( !empty( $tagline ) ) {
			$services = MediaWikiServices::getInstance();
			$langConv = $services
					->getLanguageConverterFactory()
					->getLanguageConverter( $services->getContentLanguage() );
			$tagline = $langConv->convert( $tagline );
		}

		return $tagline;
	}

	/**
	 * Return user tagline message
	 *
	 * @param Title $title
	 * @return string
	 */
	private function buildUserTagline( $title ) {
		$user = $this->buildPageUserObject( $title );
		if ( $user ) {
			$editCount = $user->getEditCount();
			if ( $editCount ) {
				$skin = $this->skin;
				// TODO: Figure out a waw to get registration duration,
				//	like Langauge::getHumanTimestamp()
				//$registration = $user->getRegistration();
				$msgEditCount = $skin->msg( 'usereditcount' )
					->numParams( sprintf( '%s', number_format( $editCount, 0 ) ) );
				return $msgEditCount;
			}
		}
		return null;
	}

	/**
	 * Return new User object based on username or IP address.
	 * Based on MinervaNeue
	 *
	 * @param Title $title
	 * @return User|null
	 */
	private function buildPageUserObject( $title ) {
		$titleText = $title->getText();
		$user = $this->user;

		if ( IPUtils::isIPAddress( $titleText ) ) {
			return $user->newFromAnyId( null, $titleText, null );
		}

		$userIdentity = MediaWikiServices::getInstance()->getUserIdentityLookup()->getUserIdentityByName( $titleText );
		if ( $userIdentity && $userIdentity->isRegistered() ) {
			return $user->newFromId( $userIdentity->getId() );
		}

		return null;
	}
}

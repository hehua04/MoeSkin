<?php
/**
 * MoeSkin - A responsive skin developed for the Moegirlpedia
 *
 * This file is part of MoeSkin.
 *
*/

declare( strict_types=1 );

namespace MediaWiki\Skins\MoeSkin\Partials;

use MediaWiki\Skins\MoeSkin\GetConfigTrait;
use MediaWiki\Skins\MoeSkin\SkinMoeSkin;
use OutputPage;
use Title;

/**
 * The base class for all skin partials
*/
abstract class Partial {

	use GetConfigTrait;

	/**
	 * @var SkinMoeSkin
	*/
	protected $skin;

	/**
	 * Needed for trait
	 *
	 * @var OutputPage
	*/
	protected $out;

	/**
	 * @var Title
	*/
	protected $title;

	/**
	 * @var User
	*/
	protected $user;

	/**
	 * Constructor
	 * @param SkinMoeSkin $skin
	*/
	public function __construct( SkinMoeSkin $skin ) {
		$this->skin = $skin;
		$this->out = $skin->getOutput();
		$this->title = $this->out->getTitle();
		$this->user = $this->out->getUser();
	}
}

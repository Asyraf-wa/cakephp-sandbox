<?php

namespace Sandbox\Controller;

class HelpersController extends SandboxAppController {

	/**
	 * @var string|false
	 */
	public $modelClass = false;

	/**
	 * @return void
	 */
	public function index() {
	}

	/**
	 * @return void
	 */
	public function googleMapV3() {
		$this->Common->loadHelper(['Geo.GoogleMap']);
	}

}

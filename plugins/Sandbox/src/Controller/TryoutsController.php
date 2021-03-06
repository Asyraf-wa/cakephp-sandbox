<?php

namespace Sandbox\Controller;

class TryoutsController extends SandboxAppController {

	/**
	 * @var string|false
	 */
	public $modelClass = false;

	/**
	 * @return void
	 */
	public function index() {
		$actions = $this->_getActions($this);

		$this->set(compact('actions'));
	}

	/**
	 * @see http://fontawesome.io/
	 *
	 * @return void
	 */
	public function fontawesome() {
	}

	/**
	 * @return void
	 */
	public function fontello() {
	}

}

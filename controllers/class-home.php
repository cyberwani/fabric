<?php
/**
 * =======================================
 * Home Controller
 * =======================================
 */

namespace Fabric\Controllers;

class Home extends Base
{

	public function __construct()
	{
		parent::__construct();

		$this->show_sidebar = false;
	}

}

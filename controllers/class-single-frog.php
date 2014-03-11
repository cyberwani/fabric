<?php
/**
 * =======================================
 * Single Frog Controller
 * =======================================
 */

namespace Fabric\Controllers;

class Single_Frog extends Single
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_color()
	{
		echo frogcolor();
	}

	public function demo()
	{
		echo 'showing thiery';
	}

}
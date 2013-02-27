<?php

abstract class See_Form {

	abstract protected function display();
	abstract protected function process();
	abstract protected function run();

	private $template_args = array();
}
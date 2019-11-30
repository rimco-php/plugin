<?php

namespace Rimco\Plugin;

abstract class Extension
{
	/**
	* Return information about the plugin: title, version, description, author, url
	* @return array
	*/
	abstract function info();

	/**
	* @var array declare specific plugin hook priorities
	* @see \Rimco\Plugin\Call::DEFAULT_PRIORITY
	*/
 	public $priority = array();
}

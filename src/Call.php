<?php

namespace Rimco\Plugin;

class Call
{
	/**
	* Execute all the $action plugin hooks
	*
	* @param mixed $args...
	* @return boolean false if no action hooks are found, true if there are
	*/
	static function action($action)
	{
		$method = 'action_' . $action;
		if (!$extensions = Register::methods($method))
		{
			return false;
		}

		$args = func_get_args();
		array_shift($args);
		foreach ($extensions as $plugin)
		{
			// disabled hook ?
			//
			if (!empty(static::$disabled[ $method ][ get_class($plugin) ]))
			{
				continue;
			}

			call_user_func_array( array($plugin, $method), $args);
		}

		return true;
	}

	/**
	* Apply all the $filter plugin hooks to $value
	*
	* @param string $filter
	* @param mixed $value
	* @param mixed $args...
	* @return mixed filtered value
	*/
	static function filter($filter, $value)
	{
		$method = 'filter_' . $filter;
		if (!$extensions = Register::methods($method))
		{
			return $value;
		}

		$args = func_get_args();
		array_shift($args);
		foreach ($extensions as $plugin)
		{
			// disabled hook ?
			//
			if (!empty(static::$disabled[ $method ][ get_class($plugin) ]))
			{
				continue;
			}

			$value = call_user_func_array(
				array($plugin, $method),
				$args);

			// put the filtered value as argument
			// for the next plugin to use
			//
			$args[0] = $value;
		}

		return $value;
	}

	/**
	* @var array List of disabled hooks, methods and classes
	* @see \Rimco\Plugin\Call::toggle()
	*/
	protected static $disabled = array();

	/**
	* Enable\Disable $method ("acton_something", or "filter_something") for $class
	* @param string $method
	* @param string $class
	* @param boolean $enabled
	*/
	protected static function toggle($method, $class, $enabled = true)
	{
		$method = filter_var($method, FILTER_SANITIZE_STRING);
		$class = is_object($class)
			? get_class($class)
			: filter_var($class, FILTER_SANITIZE_STRING);

		static::$disabled[ $method ][ $class ] = (boolean) $enabled;
	}

	/**
	* Disable $method ("acton_something", or "filter_something") for $class
	* @param string $method
	* @param string $class
	* @uses \Rimco\Plugin\Call::toggle()
	*/
	static function disable($method, $class)
	{
		return static::toggle($method, $class, false);
	}

	/**
	* Enable $method ("acton_something", or "filter_something") for $class
	* @param string $method
	* @param string $class
	* @uses \Rimco\Plugin\Call::toggle()
	*/
	static function enable($method, $class)
	{
		return static::toggle($method, $class, true);
	}
}

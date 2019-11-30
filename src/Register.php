<?php

namespace Rimco\Plugin;

class Register
{
	/**
	* @var array list of loaded extensions, key\value pairs
	*/
	protected static $loaded;

	/**
	* Load a plugin extension
	* @param \Rimco\Plugin\Extension $ext
	* @throws \RuntimeException
	*/
	static function load(Extension $ext)
	{
		$_class = get_class($ext);
		if (!is_subclass_of($ext, Extension::class))
		{
			throw new \RuntimeException(
				"Extension class \"{$_class}\" does not extend \Rimco\Plugin\Extension"
			);
		}

		self::$loaded[ $_class ] = $ext;
	}

	/**
	* Get a list of all loaded plugin extensions
	* @return array
	*/
	static function loaded()
	{
		return self::$loaded;
	}

	const DEFAULT_PRIORITY = 100;

	/**
	* Get what priority is assigned to the $method from $ext
	*
	* @param Rimco\Plugin\Extension $ext
	* @param string $method
	* @return integer
	*/
	static protected function priority(Extension $ext, $method)
	{
		// explicitly declared in $ext->priority ?
		//
		if (isset($ext->priority[ $method ]))
		{
			return (int) $ext->priority[ $method ];
		}

		// look in docblock for "%priority"
		//
		try {
			$r = new \ReflectionMethod($ext, $method);
			$dc = substr($r->getDocComment(), 2); // cut first "/*"

			if (preg_match('~(?:^|\n\s*)\*\s+%\s*priority\s+(\d+)\s*~', $dc, $R))
			{
				return (int) $R[1];
			}
		}
		catch (\ReflectionException $e) {}

		return static::DEFAULT_PRIORITY;
	}

      	/**
      	* Returns list of plugin extensions that have the $method implemented
      	*
      	* @param string $method method name to look for
      	* @return array list of plugins
      	*/
      	static function methods($method)
      	{
      		$result = array();

      		// collect all the plugin objects that have such hook
      		//
      		foreach(self::$loaded as $class => $ext)
      		{
			if (!is_callable(array($ext, $method)))
			{
				continue;
			}

			// check if there is a different priority
			// set for the order in which the hooks will
			// be executed
			//
			$result[ $class ] = static::priority($ext, $method);
      		}

      		// push all the plugin objects in
      		// the places of their priorities
      		//
      		arsort($result);
      		foreach ($result as $class => $p)
      		{
      			$result[ $class ] = self::$loaded[ $class ];
      		}

      		return $result;
      	}
}

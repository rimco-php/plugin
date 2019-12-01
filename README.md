# Rimco\\Plugin

**Rimco\\Plugin** is a small PHP library for implementing plugin extensions in your project.

It is meant to be very easy to use. If you have any experience with trying to allow external modules to extend your project, change certain behaviour and filter different values, you know how helpful it is to have a system to manage all that. ***Rimco\\Plugin*** is meant to help with that.

## How it works ?

Similar to WordPress hooks, the plugin extensions from ***Rimco\\Plugin*** supports both **action** and **filter** methods.

### Action Methods
Action methods are meant to help you introduce new behaviour or change existing one at the places in your code where the action is executed.
To execute the plugin action, you call ``\Rimco\Plugin\Call::action()`` and you pass the name of the action and all arguments needed:
```php
\Rimco\Plugin\Call::action('after_login', $username);
```
Action methods do not return values.

### Filter Methods
Filter methods are used to help you to apply different filters on the passed argument value.
To execute the plugin filter, you call ``\Rimco\Plugin\Call::filter()``, you pass the name of the filter, the value you want filtered, and then all other arguments if needed:
```php
// use the "login_request" filter on $request
$request = \Rimco\Plugin\Call::filter('login_request', $request, $username, $password);
```
Filter methods return the filtered value argument.

## Extensions
The plugin extensions is where you put your action methods and methods. Each extension is a class that extends ``\Rimco\Plugin\Extension``, and there is one abstract method that needs to be implemented called ``\Rimco\Plugin\Extension::info()``. It returns some details about the extension as name, author, purpose, etc.
```php
class ProbaExtension extends \Rimco\Plugin\Extension
{
	function info()
	{
		return array(
			'title' => 'Demo Rimco\Plugin Extension',
			'version' => '0.1',
			'description' => 'Demonstrate what a plugin extension looks like',
			'author' => 'Rimco',
		);
	}

	...
}
```

The extension class must also implement all the action and filter methods you need.
All the **action** methods have ``action_`` prefix and all the **filters** have ``filter_`` prefix. Methods must be public and non-static since they are called from the ``\Rimco\Plugin\Call`` class.
```php
class ProbaExtension extends \Rimco\Plugin\Extension
{
	...

	// ... will be called by \Rimco\Plugin\Call::action('after_login', $username);
	function action_after_login($username)
	{
		error_log("Login OK: {$username}", 3, '/my/app/logs/auth.log');
	}

	// ... will be called by \Rimco\Plugin\Call::filter('login_request', $request, $username, $password);
	function filter_login_request(array $request, $username, $password)
	{
		$request['login'] = strtoupper($username);
		return $request;
	}
}
```

### Method Priorities
Not all methods are equal and some needs to be called earlier than others, or - in some cases - later than others. The way to control who is executed earlier and who later is using the **method priorities**. This is an integer number associated with each method. Bigger numbers are executed first, or in other words the priority sorting is **descending**. Default priority is ``100``.

You can control these method priorities in two ways.

First, inside your extension there is the ``\Rimco\Plugin\Extension::$priority`` property. It is an array in which you can explicitly declare the priority for a method, like this:
```php
	/**
	* @internal Declare priority "210" for the "action_after_login" method
	*/
	public $priority = array(
		'action_after_login' => 210
		);
```

The other method is using ``docBlock`` declarations of the method, where you write the priority like this
```php
	/** %priority 123 */
	function filter_login_request(array $request, $username, $password)
	{
		$request['login'] = strtoupper($username);
		return $request;
	}
```

If by any chance you are using both ways, the ``\Rimco\Plugin\Extension::$priority`` declaration is used first, and then the docBlocks are used as fallback.

### Disabling Methods
In some rare occasions you might need to disable a certain method: like when some plugin extensions are colliding with each other, or like when certain action should not be performed. In these odd cases, use ``\Rimco\Plugin\Call::disable()`` and ``\Rimco\Plugin\Call::enable()`` methods.
```php

	// disables ProbaExtension::action_after_login()
	\Rimco\Plugin\Call::disable('action_after_login', ProbaExtension::class);
```

## Extension Loading
Before being able to use a plugin extension, you must load it up. You do that by passing the extension object to ``\Rimco\Plugin\Register::load()``:
```php
	\Rimco\Plugin\Register::load(new ProbaExtension);
```

## Sample Extension

Here's what a plugin extension looks like
```php
class ProbaExtension extends \Rimco\Plugin\Extension
{
	function info()
	{
		return array(
			'title' => 'Demo Rimco\Plugin Extension',
			'version' => '0.1',
			'description' => 'Demonstrate what a plugin extension looks like',
			'author' => 'Rimco',
		);
	}

	/**
	* @internal Declare priority "210" for the "action_test" method
	*/
	public $priority = array('action_test' => 210);

	/**
	* Sample action method, will be triggered by "\Rimco\Plugin\Call::action('test')"
	*/
	function action_test()
	{
		echo __METHOD__;
	}

	/**
	* Sample filter method, will be triggered by "\Rimco\Plugin\Call::filter('test')"
	*
	* @internal this is an example of using the docBlock to declare the priority of "123"
	* %priority 123
	*/
	function filter_test($value)
	{
		return __METHOD__ . '.' . $value;
	}
}
```

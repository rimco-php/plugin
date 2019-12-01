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

## Sample Extension

Here's what a plugin extension looks like
```php
class proba extends \Rimco\Plugin\Extension
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

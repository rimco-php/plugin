# Rimco\\Plugin

Quick and simple plugin extension system, with action and filter hooks.

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

Pusher for WordPress
====================

This plugin offers a simple interface for the Pusher API for WordPress themes and plugins.

Installation
------------

1. Upload the plugin to your plugins directory as normal
2. Run `composer install`
3. Activate the plugin
4. Navigate to Settings > Pusher API Settings and enter your API credentials

Usage
-----

Once installed, you can trigger events using the `pusher_trigger_event` action.

For instance,

```php
do_action(
	'pusher_trigger_event',
	'my-channel',
	'my-event',
	[
		'message' => 'hello world',
	]
);
```

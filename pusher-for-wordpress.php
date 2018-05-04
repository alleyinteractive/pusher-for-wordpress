<?php
/**
 * Plugin Name:     Pusher for WordPress
 * Plugin URI:      https://github.com/alleyinteractive/pusher-for-wordpress
 * Description:     WordPressy integration of Pusher
 * Author:          Matthew Boynes
 * Author URI:      https://www.alley.co/
 * Text Domain:     pusher-for-wordpress
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Pusher_For_Wordpress
 */

namespace Alley\Pusher;

require __DIR__ . '/vendor/autoload.php';

add_action( 'after_setup_theme', function() {
	if ( function_exists( 'fm_register_submenu_page' ) ) {
		fm_register_submenu_page( 'pusher_settings', 'options-general.php', __( 'Pusher API Settings', 'pusher-for-wordpress' ), __( 'Pusher API Settings', 'pusher-for-wordpress' ) );
	}
} );

/**
 * `pusher_settings` Fieldmanager fields.
 */
function fm_pusher_settings() {
	$fm = new \Fieldmanager_Group( [
		'name'     => 'pusher_settings',
		'children' => [
			'app_id'    => new \Fieldmanager_TextField( __( 'App ID', 'pusher-for-wordpress' ) ),
			'key'       => new \Fieldmanager_TextField( __( 'Key', 'pusher-for-wordpress' ) ),
			'secret'    => new \Fieldmanager_Password( __( 'Secret', 'pusher-for-wordpress' ) ),
			'cluster'   => new \Fieldmanager_TextField( __( 'Cluster', 'pusher-for-wordpress' ) ),
			'encrypted' => new \Fieldmanager_Checkbox( [
				'label'         => __( 'Encrypted', 'pusher-for-wordpress' ),
				'default_value' => '1',
			] ),
		],
	] );
	$fm->activate_submenu_page();
}
add_action( 'fm_submenu_pusher_settings', __NAMESPACE__ . '\fm_pusher_settings' );

/**
 * Trigger an event in Pusher.
 *
 * Don't call this function directly, you should interact with it via the
 * 'pusher_trigger_event' action, e.g.
 *
 *     do_action(
 *         'pusher_trigger_event',
 *         'my-channel',
 *         'my-event',
 *         [
 *             'message' => 'hello world',
 *         ]
 *     );
 *
 * @param string $channel Channel to which to push.
 * @param string $event   Event to trigger.
 * @param array  $data    Data to send.
 * @return bool True on success, false on failure.
 */
function trigger_event( string $channel, string $event, array $data = [] ) {
	static $pusher;
	if ( ! isset( $pusher ) ) {
		$options = get_option( 'pusher_settings', [] );
		$options = wp_parse_args( $options, [
			'encrypted' => '1',
		] );
		if (
			empty( $options['app_id'] )
			|| empty( $options['key'] )
			|| empty( $options['secret'] )
			|| empty( $options['cluster'] )
		) {
			return false;
		}

		$pusher = new \Pusher\Pusher(
			$options['key'],
			$options['secret'],
			$options['app_id'],
			[
				'cluster'   => $options['cluster'],
				'encrypted' => (bool) $options['encrypted'],
			]
		);
	}

	if ( $pusher instanceof \Pusher\Pusher ) {
		return $pusher->trigger( $channel, $event, $data );
	}
	return false;
}
add_action( 'pusher_trigger_event', __NAMESPACE__ . '\trigger_event', 10, 3 );

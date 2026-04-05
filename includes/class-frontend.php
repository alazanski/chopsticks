<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Chopsticks_Frontend {

	public function __construct() {
		add_action( 'wp_head', [ $this, 'inject_script' ], 1 );
	}

	public function inject_script() {
		$settings = get_option( 'chopsticks_settings', [] );

		if ( empty( $settings['enabled'] ) || empty( $settings['website_id'] ) ) {
			return;
		}

		$script_url = ! empty( $settings['script_url'] ) ? $settings['script_url'] : 'https://cloud.umami.is/script.js';
		$website_id = $settings['website_id'];
		$domains    = ! empty( $settings['domains'] ) ? $settings['domains'] : '';

		$tag = '<script defer src="' . esc_url( $script_url ) . '" data-website-id="' . esc_attr( $website_id ) . '"';
		if ( $domains ) {
			$tag .= ' data-domains="' . esc_attr( $domains ) . '"';
		}
		$tag .= '></script>' . "\n";

		echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fully escaped above
	}
}

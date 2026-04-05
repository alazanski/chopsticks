<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Chopsticks_Frontend {

	private $website_id = '';
	private $domains    = '';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_script' ] );
		add_filter( 'script_loader_tag', [ $this, 'add_script_attributes' ], 10, 3 );
	}

	public function enqueue_script() {
		$settings = get_option( 'chopsticks_settings', [] );

		if ( empty( $settings['enabled'] ) || empty( $settings['website_id'] ) ) {
			return;
		}

		$this->website_id = $settings['website_id'];
		$this->domains    = ! empty( $settings['domains'] ) ? $settings['domains'] : '';
		$script_url       = ! empty( $settings['script_url'] ) ? $settings['script_url'] : 'https://cloud.umami.is/script.js';

		wp_register_script( 'chopsticks-umami', $script_url, [], false, false );
		wp_enqueue_script( 'chopsticks-umami' );
	}

	public function add_script_attributes( $tag, $handle, $src ) {
		if ( 'chopsticks-umami' !== $handle ) {
			return $tag;
		}

		$attrs = 'defer src="' . esc_url( $src ) . '" data-website-id="' . esc_attr( $this->website_id ) . '"';
		if ( $this->domains ) {
			$attrs .= ' data-domains="' . esc_attr( $this->domains ) . '"';
		}

		return '<script ' . $attrs . '></script>' . "\n";
	}
}

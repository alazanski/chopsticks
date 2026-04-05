<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Chopsticks_Admin {

	private $settings = null;

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	public function add_menu() {
		add_options_page(
			__( 'Chopsticks', 'chopsticks' ),
			__( 'Chopsticks', 'chopsticks' ),
			'manage_options',
			'chopsticks',
			[ $this, 'render_page' ]
		);
	}

	public function register_settings() {
		register_setting( 'chopsticks', 'chopsticks_settings', [
			'type'              => 'array',
			'default'           => [
				'enabled'             => false,
				'website_id'          => '',
				'script_url'          => 'https://cloud.umami.is/script.js',
				'domains'             => '',
				'woocommerce_enabled' => false,
			],
			'sanitize_callback' => [ $this, 'sanitize' ],
		] );

		add_settings_section( 'chopsticks_general', __( 'Analytics', 'chopsticks' ), null, 'chopsticks' );

		add_settings_field( 'enabled', __( 'Enable Umami', 'chopsticks' ), [ $this, 'field_enabled' ], 'chopsticks', 'chopsticks_general' );
		add_settings_field( 'website_id', __( 'Website ID', 'chopsticks' ), [ $this, 'field_website_id' ], 'chopsticks', 'chopsticks_general' );
		add_settings_field( 'script_url', __( 'Script URL', 'chopsticks' ), [ $this, 'field_script_url' ], 'chopsticks', 'chopsticks_general' );
		add_settings_field( 'domains', __( 'Domains', 'chopsticks' ), [ $this, 'field_domains' ], 'chopsticks', 'chopsticks_general' );

		if ( class_exists( 'WooCommerce' ) ) {
			add_settings_section( 'chopsticks_woocommerce', __( 'WooCommerce', 'chopsticks' ), null, 'chopsticks' );
			add_settings_field( 'woocommerce_enabled', __( 'Enable WooCommerce Tracking', 'chopsticks' ), [ $this, 'field_woocommerce_enabled' ], 'chopsticks', 'chopsticks_woocommerce' );
		}
	}

	public function sanitize( $input ) {
		$output = [];

		$output['enabled'] = ! empty( $input['enabled'] );

		$website_id          = sanitize_text_field( $input['website_id'] ?? '' );
		$output['website_id'] = preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $website_id )
			? $website_id
			: '';

		if ( ! $output['website_id'] && ! empty( $website_id ) ) {
			add_settings_error( 'chopsticks_settings', 'invalid_website_id', __( 'Website ID must be a valid UUID.', 'chopsticks' ) );
		}

		$script_url          = esc_url_raw( $input['script_url'] ?? '' );
		$output['script_url'] = $script_url ?: 'https://cloud.umami.is/script.js';

		$output['domains'] = sanitize_text_field( $input['domains'] ?? '' );

		$output['woocommerce_enabled'] = ! empty( $input['woocommerce_enabled'] );

		return $output;
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'chopsticks' );
				do_settings_sections( 'chopsticks' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	private function get( $key, $default = '' ) {
		if ( $this->settings === null ) {
			$this->settings = get_option( 'chopsticks_settings', [] );
		}
		return $this->settings[ $key ] ?? $default;
	}

	public function field_enabled() {
		$val = $this->get( 'enabled', false );
		echo '<input type="checkbox" name="chopsticks_settings[enabled]" value="1" ' . checked( 1, $val, false ) . '>';
	}

	public function field_website_id() {
		echo '<input type="text" name="chopsticks_settings[website_id]" value="' . esc_attr( $this->get( 'website_id' ) ) . '" class="regular-text" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">';
		echo '<p class="description">' . esc_html__( 'Your Umami website UUID. Found in Umami under Settings → Websites.', 'chopsticks' ) . '</p>';
	}

	public function field_script_url() {
		echo '<input type="url" name="chopsticks_settings[script_url]" value="' . esc_attr( $this->get( 'script_url', 'https://cloud.umami.is/script.js' ) ) . '" class="regular-text">';
		echo '<p class="description">' . esc_html__( 'Umami Cloud: https://cloud.umami.is/script.js — Self-hosted: your own script URL.', 'chopsticks' ) . '</p>';
	}

	public function field_domains() {
		echo '<input type="text" name="chopsticks_settings[domains]" value="' . esc_attr( $this->get( 'domains' ) ) . '" class="regular-text" placeholder="example.com,www.example.com">';
		echo '<p class="description">' . esc_html__( 'Comma-separated list of domains to track. Leave empty to track all domains.', 'chopsticks' ) . '</p>';
	}

	public function field_woocommerce_enabled() {
		$val = $this->get( 'woocommerce_enabled', false );
		echo '<label><input type="checkbox" name="chopsticks_settings[woocommerce_enabled]" value="1" ' . checked( 1, $val, false ) . '> ';
		echo esc_html__( 'Send view_item, add_to_cart, remove_from_cart, begin_checkout, and purchase events to Umami.', 'chopsticks' ) . '</label>';
	}
}

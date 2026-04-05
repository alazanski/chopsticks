<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Chopsticks_WooCommerce {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'woocommerce_thankyou', [ $this, 'track_purchase' ], 10, 1 );
	}

	public function enqueue() {
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			return;
		}

		wp_enqueue_script(
			'chopsticks-tracking',
			CHOPSTICKS_URL . 'assets/js/tracking.js',
			[],
			CHOPSTICKS_VERSION,
			true
		);

		$data = $this->get_page_data();
		if ( $data ) {
			wp_localize_script( 'chopsticks-tracking', 'chopsticksWC', $data );
		}
	}

	private function get_page_data() {
		if ( is_product() ) {
			$product = wc_get_product( get_queried_object_id() );
			if ( ! $product ) {
				return null;
			}
			return [
				'event' => 'view_item',
				'data'  => [
					'product_id'   => (string) $product->get_id(),
					'product_name' => $product->get_name(),
					'price'        => (float) $product->get_price(),
					'currency'     => get_woocommerce_currency(),
				],
			];
		}

		if ( is_cart() && WC()->cart ) {
			return [
				'event' => 'view_cart',
				'data'  => [
					'value'     => (float) WC()->cart->total,
					'currency'  => get_woocommerce_currency(),
					'num_items' => (int) WC()->cart->get_cart_contents_count(),
				],
			];
		}

		if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && WC()->cart ) {
			return [
				'event' => 'begin_checkout',
				'data'  => [
					'value'     => (float) WC()->cart->total,
					'currency'  => get_woocommerce_currency(),
					'num_items' => (int) WC()->cart->get_cart_contents_count(),
				],
			];
		}

		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			if ( is_shop() ) {
				$list_name = get_the_title( wc_get_page_id( 'shop' ) );
			} else {
				$term      = get_queried_object();
				$list_name = ( $term && isset( $term->name ) ) ? $term->name : '';
			}
			return [
				'event' => 'view_item_list',
				'data'  => [
					'list_name' => $list_name,
				],
			];
		}

		return null;
	}

	public function track_purchase( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		if ( $order->get_meta( '_chopsticks_tracked' ) ) {
			return;
		}

		$order->update_meta_data( '_chopsticks_tracked', 1 );
		$order->save();

		$data = wp_json_encode( [
			'order_id' => (string) $order_id,
			'revenue'  => (float) $order->get_total(),
			'currency' => $order->get_currency(),
			'items'    => (int) $order->get_item_count(),
		] );

		echo '<script>(function(){';
		echo 'var d=' . $data . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output
		echo 'var r=0;function t(){if(window.umami&&window.umami.track){window.umami.track("purchase",d);}else if(++r<50){setTimeout(t,100);}}t();';
		echo '})();</script>' . "\n";
	}
}

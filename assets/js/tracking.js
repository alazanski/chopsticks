(function () {
	'use strict';

	var config = window.chopsticksWC;

	function waitForUmami(callback) {
		if (window.umami && typeof window.umami.track === 'function') {
			callback();
			return;
		}
		var elapsed = 0;
		var interval = setInterval(function () {
			elapsed += 100;
			if (window.umami && typeof window.umami.track === 'function') {
				clearInterval(interval);
				callback();
			} else if (elapsed >= 5000) {
				clearInterval(interval);
			}
		}, 100);
	}

	function track(eventName, data) {
		waitForUmami(function () {
			window.umami.track(eventName, data);
		});
	}

	// view_item / begin_checkout — data passed from PHP via wp_localize_script
	if (config && config.event && config.data) {
		track(config.event, config.data);
	}

	// add_to_cart — Classic single product page (form submission, non-AJAX)
	// The added_to_cart jQuery event only fires for AJAX adds (shop/archive).
	// On product pages we already have the product data from view_item.
	if (config && config.event === 'view_item' && config.data) {
		var cartForm = document.querySelector('form.cart');
		if (cartForm) {
			cartForm.addEventListener('submit', function () {
				var qtyInput = cartForm.querySelector('input[name="quantity"]');
				track('add_to_cart', {
					product_id:   config.data.product_id,
					product_name: config.data.product_name,
					price:        config.data.price,
					currency:     config.data.currency,
					quantity:     Number((qtyInput && qtyInput.value) || 1),
				});
			});
		}
	}

	// add_to_cart — WooCommerce Blocks
	document.body.addEventListener('wc-blocks_added_to_cart', function (e) {
		var detail = (e && e.detail) ? e.detail : {};
		track('add_to_cart', {
			product_id: String(detail.productId || detail.product_id || ''),
			quantity: Number(detail.quantity) || 1,
		});
	});

	// add_to_cart — Classic (jQuery)
	if (window.jQuery) {
		jQuery(document.body).on('added_to_cart', function (e, fragments, hash, btn) {
			var $btn = jQuery(btn);
			track('add_to_cart', {
				product_id: String($btn.data('product_id') || $btn.data('product-id') || ''),
				quantity: Number($btn.data('quantity')) || 1,
			});
		});
	}

	// remove_from_cart — WooCommerce Blocks
	document.body.addEventListener('wc-blocks_removed_from_cart', function (e) {
		var detail = (e && e.detail) ? e.detail : {};
		track('remove_from_cart', {
			product_id: String(detail.productId || detail.product_id || ''),
			quantity: Number(detail.quantity) || 1,
		});
	});

	// remove_from_cart — Classic (jQuery)
	if (window.jQuery) {
		jQuery(document.body).on('removed_from_cart', function (e, fragments, hash, btn) {
			var $btn = jQuery(btn);
			track('remove_from_cart', {
				product_id: String($btn.data('product_id') || $btn.data('product-id') || ''),
			});
		});
	}
})();

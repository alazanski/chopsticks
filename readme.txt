=== Chopsticks — Umami Analytics ===
Contributors: aleksejski
Tags: analytics, umami, privacy, woocommerce, ecommerce
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-focused Umami analytics for WordPress with full WooCommerce ecommerce event tracking.

== Description ==

Chopsticks connects your WordPress site to [Umami](https://umami.is), a simple, privacy-focused analytics platform. It injects the Umami tracking script and optionally sends WooCommerce ecommerce events so you can track your store's full purchase funnel.

**Tracked WooCommerce events:**

* `view_item` — single product page views
* `view_item_list` — shop, category, tag, and taxonomy archive pages
* `view_cart` — cart page
* `add_to_cart` — works with classic themes (form submit and AJAX) and WooCommerce Blocks
* `remove_from_cart` — works with classic themes and WooCommerce Blocks
* `begin_checkout` — checkout page
* `purchase` — order completion, including revenue and currency for monetary reporting

Purchase events are deduplicated server-side — refreshing the thank-you page never double-counts an order.

**External service:**

This plugin connects to your Umami instance to deliver tracking data. For Umami Cloud users, data is sent to Umami's servers (`cloud.umami.is`). For self-hosted users, data is sent to your own server. Chopsticks itself does not collect, store, or transmit any data independently.

* Umami Cloud privacy policy: https://umami.is/privacy
* Umami Cloud terms of service: https://umami.is/terms

== Installation ==

1. Download the plugin ZIP from the [releases page](https://github.com/alazanski/chopsticks/releases)
2. In WP Admin go to **Plugins → Add New → Upload Plugin**
3. Upload the ZIP and click **Install Now**, then **Activate**
4. Go to **Settings → Chopsticks** and enter your Umami Website ID and Script URL

== Frequently Asked Questions ==

= Where do I find my Website ID? =

In your Umami dashboard go to **Settings → Websites**. The Website ID is the UUID shown under your site name.

= What Script URL should I use? =

For Umami Cloud: `https://cloud.umami.is/script.js`

For self-hosted Umami: use the URL of the `script.js` file on your own server, e.g. `https://analytics.example.com/script.js`.

= Does this plugin store any visitor data? =

No. Chopsticks only injects a script tag and passes event data to your Umami instance. No visitor data is stored in your WordPress database by this plugin.

= What data is sent to Umami? =

Umami collects page URLs, referrers, browser/device information, and any custom events fired by this plugin (product views, cart actions, purchases). For WooCommerce purchase events, the order total and currency are included. No personally identifiable information (names, emails, addresses) is sent. For full details see the [Umami privacy policy](https://umami.is/privacy).

= Is this plugin GDPR compliant? =

Umami is designed to be privacy-friendly and does not use cookies. However, compliance with GDPR, CCPA, and other regulations depends on your specific setup and jurisdiction. You are responsible for ensuring your use of analytics complies with applicable laws and for informing your visitors of data collection in your privacy policy.

= Does it work with WooCommerce Blocks? =

Yes. Cart and checkout events handle both the classic shortcode-based cart/checkout and the WooCommerce Blocks (block-based) cart/checkout.

= What happens to plugin settings if I delete the plugin? =

All plugin settings stored in the WordPress database are removed automatically when the plugin is deleted.

== Screenshots ==

1. The Chopsticks settings page under Settings → Chopsticks.

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.

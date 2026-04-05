# Chopsticks — Umami Analytics for WordPress

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-GPL--2.0%2B-green)

A lightweight WordPress plugin that integrates [Umami](https://umami.is) analytics — cloud or self-hosted — with optional WooCommerce ecommerce event tracking.

---

## Features

- Injects the Umami tracking script into every page
- Works with **Umami Cloud** and **self-hosted** instances
- Optional **domain restriction** (`data-domains`) to prevent cross-domain noise
- Full **WooCommerce ecommerce tracking** with no configuration beyond a checkbox:

| Event | Trigger |
|---|---|
| `view_item` | Single product page |
| `view_item_list` | Shop, category, tag, and taxonomy archive pages |
| `view_cart` | Cart page |
| `add_to_cart` | Add to cart — classic form submit and AJAX (shop pages), and WooCommerce Blocks |
| `remove_from_cart` | Remove from cart — classic and WooCommerce Blocks |
| `begin_checkout` | Checkout page |
| `purchase` | Order thank-you page — includes `revenue` and `currency` for monetary reporting in Umami |

Purchase events are deduplicated server-side so refreshing the thank-you page never double-counts an order.

---

## Requirements

- WordPress 6.0+
- PHP 7.4+
- An [Umami](https://umami.is) account (cloud) or a self-hosted Umami instance
- WooCommerce 7.0+ *(optional, required for ecommerce tracking)*

---

## Installation

1. Download the latest `chopsticks.zip` from [Releases](../../releases)
2. In WP Admin go to **Plugins → Add New → Upload Plugin**
3. Upload the ZIP and click **Install Now**, then **Activate**

---

## Configuration

Go to **Settings → Chopsticks**.

| Field | Description |
|---|---|
| **Enable Umami** | Master switch. Uncheck to stop all tracking without deactivating the plugin. |
| **Website ID** | Your Umami website UUID — found under Umami **Settings → Websites**. |
| **Script URL** | `https://cloud.umami.is/script.js` for Umami Cloud. For self-hosted, enter your own script URL. |
| **Domains** | Comma-separated list of domains to track (e.g. `example.com,www.example.com`). Leave empty to track all domains. Maps to Umami's `data-domains` attribute. |
| **Enable WooCommerce Tracking** | Enables all ecommerce events listed above. Only shown when WooCommerce is active. |

---

## How it works

**Script injection** — When enabled, Chopsticks outputs a `<script defer>` tag in `<head>` pointing to the Umami tracking script. Umami handles pageview tracking automatically once loaded.

**WooCommerce tracking** — Page-level events (`view_item`, `view_item_list`, `view_cart`, `begin_checkout`) are detected server-side via WordPress conditional tags. Product and cart data is passed to the browser via `wp_localize_script` and fired via `umami.track()` once the Umami script is ready.

Cart mutation events (`add_to_cart`, `remove_from_cart`) are captured via JavaScript — listening to WooCommerce Blocks DOM events (`wc-blocks_added_to_cart`, `wc-blocks_removed_from_cart`) and legacy jQuery events (`added_to_cart`, `removed_from_cart`) for full theme compatibility. On single product pages, the add-to-cart form submit is also intercepted to capture non-AJAX submissions.

**Purchase tracking** — The `woocommerce_thankyou` PHP hook captures the completed order and outputs an inline script with the order's `revenue`, `currency`, and item count. A `_chopsticks_tracked` flag is written to the order before the script is output, so duplicate tracking is prevented even if the user refreshes the page.

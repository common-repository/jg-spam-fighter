<?php

/* JG Spam Fighter -- Version: 0.5 */

/*
  Plugin Name: JG Spam Fighter
  Plugin URI: https://blacklisted.gauracreative.com
  Description: A simple to use plugin that enables you to leverage the power of the user community to keep your WordPress site clean of spam comments.
  Author: Jay Gaura
  Version: 0.5
  Author URI: https://www.gauracreative.com/
  License: GPLv2 or later
  Text Domain: jg_spamfighter
 */

namespace JG\SF;

use \JG\SF\inc\Model as Model;

defined('\WPINC') or die;

global $wpdb;

//error_log("\nbeginning\n", 3, \WP_CONTENT_DIR . '/debug.log');

define('JGSF_VERSION', '0.5');
define('JGSF_MINIMUM_WP_VERSION', '3.7');
define('JGSF_MINIMUM_PHP_VERSION', '5.3');
define('JGSF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JGSF_PLUGIN_URL', plugins_url('/', __FILE__));
define('JGSF_REPORT_ERRORS', true);
define('JGSF_SAPI_DOMAIN', 'https://blacklisted.gauracreative.com/');
define('JGSF_API_EP', JGSF_SAPI_DOMAIN . 'api/jgsf/v1.0/');
define('JGSF_STATS', JGSF_SAPI_DOMAIN . 'stats');
define('JGSF_NEWS', JGSF_SAPI_DOMAIN . 'news');
define('JGSF_FAQ', JGSF_SAPI_DOMAIN . 'faq');
define('JGSF_CONTACT', JGSF_SAPI_DOMAIN . 'contact');
define('JGSF_SUPPORT', JGSF_SAPI_DOMAIN . 'https://www.paypal.me/pools/c/85ZCH8JM6J');
define('JGSF_PLUGIN_NAME', esc_html__('JG Spam Fighter', 'jg_spamfighter'));

// Include the autoloader so we can dynamically include the rest of the classes.
require_once(JGSF_PLUGIN_DIR . 'inc/autoloader.php');

define('JGSF_NONCE_NAME', 'kow90u45f');
define('JGSF_REQUIRED_ADMIN_CAP', 'manage_options');
define('JGSF_DB_TABLE_TRUSTED', $wpdb->prefix . 'jgsf_trusted_ips');
define('JGSF_DB_TABLE_BL', $wpdb->prefix . 'jgsf_bl_ips');
define('JGSF_COOKIE_LIFE', 365 * DAY_IN_SECONDS);
define('JGSF_COOKIE_DOMAIN', Model::get_cookie_domain());
define('JGSF_LOG_ROWS', 10);
// Register plugin activation & de-activation hooks
register_activation_hook(__FILE__, array('JG\SF\inc\Init', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('JG\SF\inc\Init', 'plugin_deactivation'));

if (is_admin()) {
  add_action('plugins_loaded', array('JG\SF\inc\Admin', 'init'));
}
// this filter blocks out comments made from blacklisted IP addresses
add_filter('pre_comment_approved', array('JG\SF\inc\Init', 'check_comment'), 99, 2);

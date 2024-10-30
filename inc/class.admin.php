<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

use \JG\SF\inc\Model as Model;
use \JG\SF\inc\Debug as Bug;

class Admin {

  private static $initiated = false;
  private static $admin_pages = array();
  private static $do = 'log';
  private static $ip = '';

  public static function init() {
    if (!self::$initiated) {
      self::init_hooks();
      self::$do = isset($_REQUEST['do']) ? sanitize_text_field($_REQUEST['do']) : 'log';
      self::$ip = isset($_REQUEST['for']) ? sanitize_text_field($_REQUEST['for']) : '';
    }
  }

  public static function init_hooks() {
    self::$initiated = true;
    add_action('admin_init', array('\JG\SF\inc\Admin', 'admin_init'));
    add_action('admin_menu', array('\JG\SF\inc\Admin', 'admin_menu'), 5);
    add_action('admin_enqueue_scripts', array('\JG\SF\inc\Admin', 'enqueue_scripts'), 10, 1);
    add_filter('admin_bar_menu', array('\JG\SF\inc\Admin', 'customize_toolbar'), 999);
    add_action('admin_notices', array('\JG\SF\inc\Admin', 'activation_notice'));
  }

  public static function admin_init() {
    load_plugin_textdomain('jg_spamfighter', false, JGSF_PLUGIN_DIR . 'languages');
    self::show_welcome();
    self::set_cookies();
  }

  /*
   * Show About screen once upon plugin activation
   */

  private static function show_welcome() {
    if (get_option('jgsf_show_welcome', 0) && !isset($_GET['activate-multi'])) {
      update_option('jgsf_show_welcome', 0);
      wp_redirect(admin_url('edit-comments.php?page=jg-spam-fighter&do=about'));
    }
  }

  /*
   * cookies are used to ensure user returns to appropriate tab after certain actions on an IP address (like remove, block, trust, etc.)
   */

  private static function set_cookies() {
    if (!isset($_REQUEST['page']) || $_REQUEST['page'] != 'jg-spam-fighter') {
      return;
    }
    $do = isset($_REQUEST['do']) ? sanitize_text_field($_REQUEST['do']) : '';
    if ($do == 'trusted') {
      Model::set_cookie('jgsf_samples_ref', 'trusted', 600);
    } else if (false === strstr($do, 'samples')) {
      Model::set_cookie('jgsf_samples_ref', 'else', 600);
    }
    if (false === strstr($do, 'samples') && isset($_REQUEST['paged'])) {
      Model::set_cookie('jgsf_samples_paged', intval($_REQUEST['paged']), 600);
    }
  }

  /*
   * register admin menus and contextual help tabs
   */

  public static function admin_menu() {
    self::$admin_pages['page'] = add_comments_page(Model::menu_title(esc_html__('JG Spam Fighter', 'jg_spamfighter')), esc_html__('JG Spam Fighter', 'jg_spamfighter'), JGSF_REQUIRED_ADMIN_CAP, 'jg-spam-fighter', array('\JG\SF\inc\Admin', 'main_page'));
    if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
      if (in_array(self::$do, array('log', 'dashboard'))) {
        add_action('load-' . self::$admin_pages['page'], array('\JG\SF\inc\Help', 'dash'));
      } else if (in_array(self::$do, array('samples'))) {
        add_action('load-' . self::$admin_pages['page'], array('\JG\SF\inc\Help', 'samples'));
      } else if (in_array(self::$do, array('trusted'))) {
        add_action('load-' . self::$admin_pages['page'], array('\JG\SF\inc\Help', 'trusted'));
      } else if (in_array(self::$do, array('blacklisted'))) {
        add_action('load-' . self::$admin_pages['page'], array('\JG\SF\inc\Help', 'blacklisted'));
      } else {
        add_action('load-' . self::$admin_pages['page'], array('\JG\SF\inc\Help', 'general'));
      }
//add_action('load-' . self::$admin_pages['settings'], array('\JG\SF\inc\Help', 'general'));
    }
  }

  /*
   * register custom styles & scripts
   */

  public static function enqueue_scripts($hook) {
//wp_enqueue_style('jgsf-common', JGSF_PLUGIN_URL . 'css/common.css', false);
    if (isset(self::$admin_pages['page']) && $hook == self::$admin_pages['page']) {
      if (self::$do == 'about') {
        wp_enqueue_style('jgsf-grid', JGSF_PLUGIN_URL . 'css/grid.css', false);
        wp_enqueue_style('jgsf-spam', JGSF_PLUGIN_URL . 'css/common.css', false);
      }
      wp_enqueue_style('jgsf-spam', JGSF_PLUGIN_URL . 'css/spam.css', false);
      wp_enqueue_script('jgsf-spam', JGSF_PLUGIN_URL . 'js/spam.js', array('jquery'), false, true);
    }
  }

  public static function main_page() {
    if (!(is_user_logged_in() && current_user_can(JGSF_REQUIRED_ADMIN_CAP))) {
      return;
    }
//$status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all';
    if (self::$do == 'block') {
      new \JG\SF\view\Block(array('do' => 'block', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'trust') {
      new \JG\SF\view\Trust(array('do' => 'trust', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'trusted') {
      new \JG\SF\view\Trusted(array('do' => 'trusted', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'untrust') {
      new \JG\SF\view\UnTrust(array('do' => 'untrust', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'ur') {
      new \JG\SF\view\UnTrust(array('do' => 'remove', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'urb') {
      new \JG\SF\view\UnTrust(array('do' => 'block', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'unblock') {
      new \JG\SF\view\UnBlock(array('do' => 'unblock', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (in_array(self::$do, array('samples', 'approved-samples', 'held-samples')) && self::$ip) {
      new \JG\SF\view\Samples(array('do' => self::$do, 'title' => sprintf(esc_html__('Sample comments for %s', 'jg_spamfighter'), self::$ip), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'blacklisted') {
      new \JG\SF\view\Blacklisted(array('do' => 'blacklisted', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'stats') {
      new \JG\SF\view\Stats(array('do' => 'stats', 'title' => esc_html__('Unlimited Grow Potential', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    } else if (self::$do == 'about') {
      new \JG\SF\view\About(array('do' => 'about', 'title' => sprintf(esc_html__('About %s', 'jg_spamfighter'), JGSF_PLUGIN_NAME), 'dash_icon' => 'dashicons-shield'));
    } else { // dashboard
      new \JG\SF\view\Dashboard(array('do' => 'dashboard', 'title' => esc_html__('JG Spam Fighter', 'jg_spamfighter'), 'dash_icon' => 'dashicons-shield'));
    }
  }

  /*
   * admin tool bar
   */

  public static function customize_toolbar($admin_bar) {
    if (current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      $admin_bar->add_node(array(
          'id' => 'jgsf_bar',
          'title' => '<span class="ab-icon"></span>' . esc_html__(JGSF_PLUGIN_NAME, 'jg_spamfighter'),
          'parent' => false,
          'href' => admin_url('admin.php?page=jg-spam-fighter')
      ));
      $admin_bar->add_node(array(
          'id' => 'jgsf_bar_dashboard',
          'title' => esc_html__('All', 'jg_spamfighter'),
          'parent' => 'jgsf_bar',
          'href' => admin_url('admin.php?page=jg-spam-fighter')
      ));
      $admin_bar->add_node(array(
          'id' => 'jgsf_bar_trusted',
          'title' => esc_html__('Trusted', 'jg_spamfighter'),
          'parent' => 'jgsf_bar',
          'href' => admin_url('admin.php?page=jg-spam-fighter&do=trusted')
      ));
      $admin_bar->add_node(array(
          'id' => 'jgsf_bar_blacklisted',
          'title' => esc_html__('Blacklisted', 'jg_spamfighter'),
          'parent' => 'jgsf_bar',
          'href' => admin_url('admin.php?page=jg-spam-fighter&do=blacklisted')
      ));
      $admin_bar->add_node(array(
          'id' => 'jgsf_bar_about',
          'title' => esc_html__('About', 'jg_spamfighter'),
          'parent' => 'jgsf_bar',
          'href' => admin_url('admin.php?page=jg-spam-fighter&do=about')
      ));
    }
  }

  /*
   * show admin notices ad an attempt to smooth out (rare) cases when plugin of un-supported PHP/WP versions
   * some checks/actions are also done during plugin activation
   */

  public static function activation_notice() {
    $screen = get_current_screen();
    if (((isset(self::$admin_pages['page']) && $screen->id == self::$admin_pages['page']) || $screen->id == 'plugins') && current_user_can('install_plugins')) {
      if (version_compare($GLOBALS['wp_version'], JGSF_MINIMUM_WP_VERSION, '<')) {
        $message = sprintf(esc_html__('Plugin requires WordPress %s or higher (%s is being used).', 'jg_spamfighter'), JGSF_MINIMUM_WP_VERSION, get_bloginfo('version'));
        $message .= '</p><p>';
        $message .= sprintf(esc_html__('Please %s to a current version to ensure %s works properly.', 'jg_spamfighter'), sprintf('<a href="https://codex.wordpress.org/Upgrading_WordPress" target="_blank">%s</a>', esc_html__('upgrade WordPress', 'jg_spamfighter')), '<strong>' . JGSF_PLUGIN_NAME . '</strong>');
        Model::admin_message($message, 'error', true);
      } else if (empty(get_option('jgsf_consent', 0)) && self::$do !== 'about') {
        $message = sprintf(esc_html__('Your %s is needed for the plugin to be fully functional and protect your blog from spam comments.', 'jg_spamfighter'), sprintf('<a href="%1$s" title="%2$s">%2$s</a>', admin_url('admin.php?page=jg-spam-fighter&do=about'), esc_html__('consent', 'jg_spamfighter')));
        Model::admin_message($message, 'error', true);
      }
    }
  }

}

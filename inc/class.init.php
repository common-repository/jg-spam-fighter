<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

use JG\SF\inc\Model as Model;
use \JG\SF\inc\Debug as Bug;

class Init {
  /*
   * a callback function for 'pre_comment_approved' filter, activated in jg_spam_fighter/jg_spam_fighter.php
   * blocks comments from a blacklisted IP address
   * @return boolean: false if the IP address is blacklisted, true otherwise
   * False blocks the comment from being inserted into the database
   */

  public static function check_comment($approved, $commentdata) {
    global $wpdb;
    try {
      $blocked = $wpdb->get_var("SELECT COUNT(*) FROM " . JGSF_DB_TABLE_BL . " WHERE ip = '" . $commentdata['comment_author_IP'] . "'");
      if (false === $blocked) {
        throw new Bug('Comment Data: ' . print_r($commentdata, 1));
      }
    } catch (Bug $bug) {
      $bug->report();
    }
    return empty($blocked);
  }

  /*
   * Plugin activation function
   * 2 custom database tables are created
   */

  public static function plugin_activation() {
    load_plugin_textdomain('jg_spamfighter', false, JGSF_PLUGIN_DIR . 'languages');
    $v = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
    if (version_compare($v, JGSF_MINIMUM_PHP_VERSION, '<')) {
      $message = sprintf(esc_html__('%s requires PHP %s or higher. You’re still on %s. Sorry. %s', 'jg_spamfighter'), '<strong>' . JGSF_PLUGIN_NAME . '</strong>', JGSF_MINIMUM_PHP_VERSION, $v, sprintf('<a href="%s">%s</a>', admin_url('/plugins.php'), esc_html__('Back to Plugins', 'jg_spamfighter')));
      deactivate_plugins('jg_spam_fighter/jg_spam_fighter.php', true);
      wp_die($message);
    } else {
      update_option('jgsf_show_welcome', 1);
      self::createTables();
    }
  }

  /*
   * Plugin deactivation function
   * the 2 custom database tables are removed
   */

  public static function plugin_deactivation() {
    update_option('jgsf_show_welcome', 0);
    update_option('jgsf_consent', 0);
    self::dropTable();
  }

  private static function createTables() {
    global $wpdb;
    $charset_collate = self::createCommentsCollate();
    //$charset_collate = 'SET utf8mb4 COLLATE utf8mb4_unicode_ci';
    $sql1 = "CREATE TABLE IF NOT EXISTS " . JGSF_DB_TABLE_TRUSTED . " (
          id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          ip varchar(64) NOT NULL,
          user bigint(20) UNSIGNED NOT NULL DEFAULT '0',
          note varchar(64) NULL,
          time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY id (id)
     ) $charset_collate;";
    $wpdb->query($sql1);
    $sql2 = "CREATE TABLE IF NOT EXISTS " . JGSF_DB_TABLE_BL . " (
          id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          ip varchar(64) NOT NULL,
          time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY id (id)
     ) $charset_collate;";
    $wpdb->query($sql2);
    //require_once(\ABSPATH . 'wp-admin/includes/upgrade.php');
    //dbDelta($sql1);
    //dbDelta($sql2);
  }

  private static function dropTable() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS " . JGSF_DB_TABLE_TRUSTED);
    $wpdb->query("DROP TABLE IF EXISTS " . JGSF_DB_TABLE_BL);
  }

  private static function createCommentsCollate() {
    global $wpdb;
    $comments_table = $wpdb->get_row("SHOW TABLE STATUS WHERE NAME LIKE '$wpdb->comments'");
    if (isset($comments_table->Collation) && $comments_table->Collation) {
      $collates = explode('_', $comments_table->Collation);
      return sprintf('DEFAULT CHARACTER SET %s COLLATE %s', $collates[0], $comments_table->Collation);
    }
    return $wpdb->get_charset_collate();
  }

}

<?php
/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

use \JG\SF\inc\Sapi as Sapi;
use \JG\SF\inc\Debug as Bug;

/*
 * This class holds a bunch of methods that do work typical to Model in MVC design pattern (hens the name)
 */

class Model {

  public function __construct() {
//
  }

  /*
   * Static Method for quick printing out contents of a value for debugging purposes
   * not used in production
   */

  public static function show($data, $exit = false) {
    ?>
    <tt><pre><?php print_r($data); ?></pre></tt>
    <?php
    if ($exit) {
      exit;
    }
  }

  /*
   * Static Method for formatting admin messages
   * @param string $message - message to be displayed
   * @param string $class - options are success, error, warning, info - those make classes notice-success, notice-error, notice-warning, notice-info
   * @param boolean $dismissible - whether to make the message dismissible (close-button)
   * @return prints out WP admin message formatted HTML
   */

  public static function admin_message($message = '', $class = 'success', $dismissible = false) {
    if (empty($message)) {
      return;
    }
    $class = $dismissible ? $class . ' is-dismissible' : $class;
    $message = '<strong>' . JGSF_PLUGIN_NAME . ':</strong> ' . $message;
    ?>
    <div id="message" class="notice-<?php echo $class; ?> notice fade"><p><?php echo $message; ?></p></div>
    <?php
  }

  /*
   * this is a sort of default admin error message based on the above
   */

  public static function error_message($dismissible = false) {
    Model::admin_message(esc_html__('Sorry, there was an error. Operation failed.', 'jg_spamfighter'), 'error', $dismissible);
  }

  /*
   * this static method is used to format admin menu pages' titles
   */

  public static function menu_title($title = '') {
    if (empty($title) || empty($title)) {
      return JGSF_PLUGIN_NAME;
    }
    return sprintf('%s %s', JGSF_PLUGIN_NAME . ' &rsaquo; ', $title);
  }

  /*
   * Get comments to be displayed in the log
   * filters out the trusted IP's
   */

  public static function get_comments() {
    $log = array();
    $args = array(
        'status' => 'all', // all, approve, hold
        'type' => 'comment',
        'user_id' => 0
    );
    $comments = get_comments($args);
    $sort = array();
    $ips = array();
    $trusted = Model::get_trusted_array();
    foreach ($comments as $comment) {
      $ip = $comment->comment_author_IP;
      if (!in_array($ip, $trusted)) {
        $approved = $comment->comment_approved;
        if (array_key_exists($ip, $sort)) {
          $sort[$ip] ++;
          if ($approved) {
            $ips[$ip]['a'] ++;
          } else {
            $ips[$ip]['h'] ++;
          }
        } else {
          $sort[$ip] = 1;
          $counts = array('a' => 0, 'h' => 0);
          if ($approved) {
            $counts['a'] = 1;
          } else {
            $counts['h'] = 1;
          }
          $ips[$ip] = $counts;
        }
      }
    }
    arsort($sort);
    $log = array();
    foreach ($sort as $ip => $count) {
      $data = $ips[$ip];
      $log[] = array('ip' => $ip, 'total' => $count, 'approve' => $ips[$ip]['a'], 'hold' => $ips[$ip]['h']);
    }
    return $log;
  }

  public static function get_trusted() {
    global $wpdb;
    $log = array();
    $trusted = $wpdb->get_results("SELECT * FROM " . JGSF_DB_TABLE_TRUSTED);
    foreach ($trusted as $item) {
      $a = array(
          'status' => 'approve', // all, approve, hold
          'type' => 'comment',
          'search' => $item->ip
      );
      $approve = get_comments($a);
      $h = array(
          'status' => 'hold', // all, approve, hold
          'type' => 'comment',
          'search' => $item->ip
      );
      $hold = get_comments($h);
      $log[] = array('ip' => $item->ip, 'total' => (count($approve) + count($hold)), 'approve' => count($approve), 'hold' => count($hold));
    }
    return $log;
  }

  public static function get_trusted_array() {
    global $wpdb;
    $trusted = array();
    $query = $wpdb->get_results('SELECT ip FROM ' . JGSF_DB_TABLE_TRUSTED);
    foreach ($query as $row) {
      $trusted[] = $row->ip;
    }
    return $trusted;
  }

  public static function get_blacklisted() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM " . JGSF_DB_TABLE_BL);
  }

  /*
   * remove comments from IP address
   * @param string $ip - the IP address
   * @param string $status - the status of the comments to be removed (all, approve, hold)
   * @param boolean $block - do we also need to block (blacklist) the IP address?
   */

  public static function remove_by_IP($ip = '', $status = 'all', $block = false) {
    global $wpdb;
    if (empty($ip)) {
      return 0;
    }
    $args = array(
        'status' => $status, // all, approve, hold
        'type' => 'comment',
        'search' => $ip
    );
    $comments = get_comments($args);
    $count = 0;
    foreach ($comments as $comment) {
      if ($comment->comment_author_IP == $ip) {
        $count += wp_delete_comment($comment->comment_ID, true);
      }
    }
    if ($block) {
      global $wpdb;
      $ip_exists = $wpdb->get_var("SELECT COUNT(*) FROM " . JGSF_DB_TABLE_BL . " WHERE ip = '" . $ip . "'");
      if (empty($ip_exists)) {
        $wpdb->insert(
                JGSF_DB_TABLE_BL, array(
            'ip' => $ip,
            'time' => date('Y-m-d H:i:s')
                ), array(
            '%s',
            '%s'
                )
        );
      }
    }
    Model::report_ip($ip, $count);
    return $count;
  }

  /*
   * trust (whitelist) IP address
   * @param string $ip - the IP address
   */

  public static function trust($ip = '') {
    global $wpdb;
    if (empty($ip)) {
      return false;
    }
// upprove all comments
    $args = array(
        'status' => 'hold',
        'type' => 'comment',
        'search' => $ip
    );
    $comments = get_comments($args);
    foreach ($comments as $comment) {
      if ($comment->comment_author_IP == $ip) {
        wp_set_comment_status($comment->comment_ID, 'approve');
      }
    }
// add to trusted
    $count = $wpdb->get_var("SELECT COUNT(*) FROM " . JGSF_DB_TABLE_TRUSTED . " WHERE ip = '" . $ip . "'");
    if ($count) {
      return false;
    }
    return $wpdb->insert(
                    JGSF_DB_TABLE_TRUSTED, array(
                'ip' => $ip,
                'user' => 0,
                'note' => '',
                'time' => date('Y-m-d H:i:s')
                    ), array(
                '%s',
                '%d',
                '%s',
                '%s'
                    )
    );
  }

  public static function untrust($ip = '') {
    global $wpdb;
    if (empty($ip)) {
      return false;
    }
    return $wpdb->delete(JGSF_DB_TABLE_TRUSTED, array('ip' => $ip), array('%s'));
  }

  public static function unblock($ip = '') {
    global $wpdb;
    if (empty($ip)) {
      return false;
    }
    return $wpdb->delete(JGSF_DB_TABLE_BL, array('ip' => $ip), array('%s'));
  }

  /*
   * this static method reports spammy IP address (that has just been blocked) to the cloud-base global database
   * @param string $ip - the IP address
   * @param int $strength - the total number of spammy comments from this IP address that have just been removed
   * the strength value is used as one of the measurements to evaluate the 'spamminess' of an IP address
   */

  public static function report_ip($ip = null, $strength = 1) {
    try {
      if (empty($ip)) {
        throw new Bug('IP empty');
      }
    } catch (Bug $bug) {
      $bug->report();
      return false;
    }
// report IP
    $args = array(
        'ip' => $ip,
        'strength' => $strength
    );
    $sapi = new Sapi();
    try {
      if (false === $sapi->post('report', $args)) {
        throw new Bug('Status: ' . $sapi->get_status() . ', Body: ' . print_r($sapi->get_body(), 1));
      }
    } catch (Bug $bug) {
      $bug->report();
    }
  }

  /*
   * check cloud-based global resource for any important new/updates
   * the external server call is only done once a week - very effeciently
   * and then transients are used for 'cache' the information
   */

  public static function have_news() {
    $version = get_transient('jgsf_news');
    if (false === $version) {
      try {
        $sapi = new Sapi();
        $res = $sapi->get('news');
        if (false === $res) {
          throw new Bug('Status: ' . $sapi->get_status() . ', Body: ' . print_r($sapi->get_body(), 1));
        } else {
          $body = $sapi->get_body();
          $version = isset($body['version']) ? intval($body['version']) : '';
          $news = isset($body['news']) ? $body['news'] : '';
        }
      } catch (Bug $bug) {
        $bug->report();
      }
      $current_news = get_option('jgsf_news', '');
      if ($version > 0 && (empty($current_news) || ((isset($current_news['version']) && $version > $current_news['version']) || (isset($current_news['seen']) && $current_news['seen'] == 0)))) {
        set_transient('jgsf_news', $version, WEEK_IN_SECONDS);
        update_option('jgsf_news', array('version' => $version, 'news' => $news, 'seen' => 0));
      } else {
        $version = 0;
        set_transient('jgsf_news', $version, WEEK_IN_SECONDS);
      }
    }
    return ($version > 0);
  }

  /*
   * This static method gets the counts of trusted and blacklisted IP addresses, as well as total number of IP addresses
   * the counts are displayed next to the tab titles, such as All (123) | Trusted (45) | Blacklisted (67)
   */

  public static function get_counts() {
    global $wpdb;
    $all = $wpdb->get_var("SELECT COUNT(DISTINCT comment_author_IP) FROM $wpdb->comments WHERE user_id = 0 AND comment_author_IP NOT IN (SELECT ip FROM " . JGSF_DB_TABLE_TRUSTED . ")");
    $trusted = $wpdb->get_var("SELECT COUNT(*) FROM " . JGSF_DB_TABLE_TRUSTED);
    $bl = $wpdb->get_var("SELECT COUNT(*) FROM " . JGSF_DB_TABLE_BL);
    return array('all' => $all, 'trusted' => $trusted, 'bl' => $bl);
  }

  public static function back_url() {
    $paged = isset($_COOKIE['jgsf_samples_paged']) ? '&paged=' . intval($_COOKIE['jgsf_samples_paged']) : '';
    if (isset($_COOKIE['jgsf_samples_ref']) && $_COOKIE['jgsf_samples_ref'] == 'trusted') {
      $url = 'edit-comments.php?page=jg-spam-fighter&do=trusted' . $paged;
    } else {
      $url = 'edit-comments.php?page=jg-spam-fighter' . $paged;
    }
    return $url;
  }

  public static function rm_cookie($cookie = 'cookie', $time = JGSF_COOKIE_LIFE) {
//$cookie_value = $_COOKIE[$cookie];
    setcookie($cookie, '', time() - $time, '/');
    setcookie($cookie, '', time() - $time);
    unset($_COOKIE[$cookie]);
  }

  public static function set_cookie($cookie = 'cookie', $value = '', $time = JGSF_COOKIE_LIFE) {
    setcookie($cookie, $value, time() + $time, '/', JGSF_COOKIE_DOMAIN);
  }

  public static function get_cookie_domain() {
    if (isset($_SERVER['HTTP_HOST'])) {
      return $_SERVER['HTTP_HOST'];
    } else if (isset($_SERVER['SERVER_NAME'])) {
      return $_SERVER['SERVER_NAME'];
    } else {
      return rtrim(preg_filter('@https?://(www\.)?@', '', home_url()), '/');
    }
  }

}

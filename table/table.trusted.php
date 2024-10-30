<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\table;

use JG\SF\inc\Model as Model;

if (!class_exists('\WP_List_Table')) {
  require_once( \ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Trusted extends \WP_List_Table {

  private $status;

  public function __construct() {
    parent::__construct(
            array(
                'singular' => 'log',
                'plural' => 'logs',
                'ajax' => false
            )
    );
    $this->status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all';
  }

  public function get_columns() {
    $columns = array(
        'cb' => '<label class="screen-reader-text" for="cb-select-all">Select All</label><input id="cb-select-all-1" type="checkbox">',
        'ip' => esc_html__('Commenter\'s IP address', 'jg_spamfighter'),
        'total' => esc_html__('All Comments', 'jg_spamfighter'),
        'held' => esc_html__('Pending', 'jg_spamfighter'),
        'approved' => esc_html__('Approved', 'jg_spamfighter')
    );
    return $columns;
  }

  public function prepare_items() {
    $this->process_bulk_action();
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $per_page = JGSF_LOG_ROWS;
    $current_page = $this->get_pagenum();
    $log = Model::get_trusted();
    $total_items = count($log);
    $data = array_slice($log, (($current_page - 1) * $per_page), $per_page);
    $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page' => $per_page
    ));
    $this->items = $data;
  }

  public function get_bulk_actions() {
    $actions = array(
        'untrust' => esc_html__('Untrust Selected', 'jg_spamfighter'),
        'ur' => esc_html__('Untrust & Remove Selected', 'jg_spamfighter'),
        'urb' => esc_html__('Untrust, Remove & Block Selected', 'jg_spamfighter')
    );
    return $actions;
  }

  private function process_bulk_action() {
    if (!current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      return;
    }
    if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
      $nonce = filter_input(\INPUT_POST, '_wpnonce', \FILTER_SANITIZE_STRING);
      $action = 'bulk-' . $this->_args['plural'];
      if (!wp_verify_nonce($nonce, $action))
        wp_die('Nope! Security check failed!');
    }
    $action = $this->current_action();
    if ('untrust' == $action && isset($_POST['cb'])) {
      $count = 0;
      foreach ($_POST['cb'] as $ip) {
        if (Model::remove_by_IP(sanitize_text_field($ip), $this->status, true)) {
          $count++;
        }
      }
      if ($count = count($_POST['cb'])) {
        Model::admin_message(sprintf(esc_html__('%d IP addresses have been removed, and the IP address has been blocked.', 'jg_spamfighter'), $count));
      }
    }
    return;
  }

  public function column_cb($item) {
    return sprintf('<input type="checkbox" name="cb[]" value="%s" />', esc_attr($item['ip']));
  }

  public function column_ip($item) {
    $name = sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=samples&for=%s">%s</a>', $item['ip'], $item['ip']);
    $actions = array(
        'samples' => sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=samples&for=%1$s" title="%2$s">%2$s</a>', esc_attr($item['ip']), esc_html__('Samples', 'jg_spamfighter')),
        'untrust' => sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=untrust&for=%1$s" title="%2$s">%2$s</a>', esc_attr($item['ip']), esc_html__('UnTrust', 'jg_spamfighter')),
        'ur' => sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=ur&for=%1$s" title="%2$s">%2$s</a>', esc_attr($item['ip']), esc_html__('UnTrust & Remove', 'jg_spamfighter')),
        'urb' => sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=urb&for=%1$s" title="%2$s">%2$s</a>', esc_attr($item['ip']), esc_html__('UnTrust, Remove & Block', 'jg_spamfighter'))
    );
    return sprintf('%s %s', $name, $this->row_actions($actions));
  }

  public function column_total($item) {
    return $item['total'];
  }

  public function column_held($item) {
    return $item['hold'];
  }

  public function column_approved($item) {
    return $item['approve'];
  }

}

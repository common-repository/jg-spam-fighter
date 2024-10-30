<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\table;

use JG\SF\inc\Model as Model;

if (!class_exists('\WP_List_Table')) {
  require_once( \ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Blacklisted extends \WP_List_Table {

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
        'ip' => esc_html__('IP address', 'jg_spamfighter')
    );
    return $columns;
  }

  public function prepare_items() {
    $this->process_bulk_action();
    $columns = $this->get_columns();
    $hidden = array();
    //$sortable = $this->get_sortable_columns();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $per_page = JGSF_LOG_ROWS;
    $current_page = $this->get_pagenum();
    $log = Model::get_blacklisted();
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
        'unbl' => esc_html__('Remove selected from Blacklisted', 'jg_spamfighter')
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
    if ('unbl' == $action && isset($_POST['cb'])) {
      $count = 0;
      foreach ($_POST['cb'] as $ip) {
        if (Model::unblock(sanitize_text_field($ip))) {
          $count++;
        }
      }
      if ($count = count($_POST['cb'])) {
        Model::admin_message(sprintf(esc_html__('%d IP addresses have been removed from Blacklisted.', 'jg_spamfighter'), $count));
      }
    }
    return;
  }

  public function column_cb($item) {
    return sprintf('<input type="checkbox" name="cb[]" value="%s" />', esc_attr($item->ip));
  }

  public function column_ip($item) {
    $name = sprintf('<a href="%s" target="_blank" title="%s">%s</a>', sprintf('%s/%s', JGSF_STATS, $item->ip), sprintf(esc_html__('stats for %s', 'jg_spamfighter'), $item->ip), $item->ip);
    $actions = array(
        'stats' => sprintf('<a href="%s" target="_blank" title="%s">%s</a>', sprintf('%s/%s', JGSF_STATS, $item->ip), sprintf(esc_html__('stats for %s', 'jg_spamfighter'), $item->ip), esc_html__('stats', 'jg_spamfighter')),
        'unbl' => sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=unblock&for=%1$s" title="%2$s">%2$s</a>', esc_attr($item->ip), esc_html__('UnBlacklist', 'jg_spamfighter')));
    return sprintf('%s %s', $name, $this->row_actions($actions));
  }

}

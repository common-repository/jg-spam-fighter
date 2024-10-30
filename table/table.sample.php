<?php
/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\table;

use JG\SF\inc\Model as Model;

if (!class_exists('\WP_List_Table')) {
  require_once( \ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Sample extends \WP_List_Table {

  private $ip;
  private $status = 'all';

  public function __construct() {
    parent::__construct(
            array(
                'singular' => 'sample',
                'plural' => 'samples',
                'ajax' => false
            )
    );
    $this->ip = isset($_REQUEST['for']) ? sanitize_text_field($_REQUEST['for']) : '';
    $do = isset($_REQUEST['do']) ? sanitize_text_field($_REQUEST['do']) : 'all';
    switch ($do) {
      case 'approved-samples':
        $this->status = 'approve';
        break;
      case 'held-samples':
        $this->status = 'hold';
        break;
      default:
        $this->status = 'all';
        break;
    }
  }

  public function get_columns() {
    $columns = array(
        'email' => esc_html__('Commenter\'s email address', 'jg_spamfighter'),
        'web' => esc_html__('Commenter\'s web address', 'jg_spamfighter'),
        'date' => esc_html__('Date', 'jg_spamfighter'),
        'content' => esc_html__('Content', 'jg_spamfighter')
    );
    return $columns;
  }

  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $per_page = JGSF_LOG_ROWS;
    $current_page = $this->get_pagenum();
    $comments = $this->get_comments();
    $total_items = count($comments);
    $data = array_slice($comments, (($current_page - 1) * $per_page), $per_page);
    $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page' => $per_page
    ));
    $this->items = $data;
  }

  public function get_comments() {
    $comments = array();
    $args = array(
        'status' => $this->status, // all, approve, hold
        'search' => $this->ip,
        'user_id' => 0,
        'orderby' => 'comment_date',
        'order' => 'DESC'
    );
    $comments = get_comments($args);
    return $comments;
  }

  public function get_counts() {
    $counts = array();
    foreach (array('all', 'approve', 'hold') as $status) {
      $args = array(
          'status' => $status, // all, approve, hold
          'search' => $this->ip,
          'user_id' => 0,
          'count' => true
      );
      $counts[$status] = get_comments($args);
    }
    return $counts;
  }

  public function extra_tablenav($which) {
    if ('top' == $which) {
      $paged = isset($_COOKIE['jgsf_samples_paged']) ? '&paged=' . intval($_COOKIE['jgsf_samples_paged']) : '';
      if (isset($_COOKIE['jgsf_samples_ref']) && $_COOKIE['jgsf_samples_ref'] == 'trusted') {
        ?>
        <div class="alignleft">
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter&do=untrust&for=%1$s" class="button" title="%2$s">%2$s</a>', $this->ip, esc_html__('UnTrust', 'jg_spamfighter')); ?> 
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter&do=ur&for=%1$s" class="button" title="%2$s">%2$s</a>', $this->ip, esc_html__('UnTrust & Remove', 'jg_spamfighter')); ?> 
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter&do=urb&for=%1$s" class="button" title="%2$s">%2$s</a>', $this->ip, esc_html__('UnTrust, Remove & Block', 'jg_spamfighter')); ?> 
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter&do=trusted%1$s" class="button button-primary" title="%2$s">%2$s</a>', $paged, esc_html__('Cancel (go back)', 'jg_spamfighter')); ?>
        </div>
        <?php
      } else {
        ?>
        <div class="alignleft">
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter&do=block&for=%1$s" class="button" title="%2$s">%2$s</a>', $this->ip, esc_html__('Remove & Block', 'jg_spamfighter')); ?> 
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter&do=trust&for=%1$s" class="button" title="%2$s">%2$s</a>', $this->ip, esc_html__('Trust', 'jg_spamfighter')); ?> 
          <?php printf('<a href="edit-comments.php?page=jg-spam-fighter%1$s" class="button button-primary" title="%2$s">%2$s</a>', $paged, esc_html__('Cancel (go back)', 'jg_spamfighter')); ?>
        </div>
        <?php
      }
    }
  }

  public function column_email($item) {
    return $item->comment_author_email;
  }

  public function column_web($item) {
    return sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', $item->comment_author_url, rtrim(preg_filter('@https?://(www\.)?@', '', $item->comment_author_url), '/'));
  }

  public function column_date($item) {
    $time = new \DateTime($item->comment_date);
    return $time->format('M j, Y');
  }

  public function column_content($item) {
    return $item->comment_content;
  }

}

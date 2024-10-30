<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

use \JG\SF\inc\Model as Model;

class UnBlock extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->top();
    $this->unblock();
    //$this->logTabs();
    //$this->view();
    $this->bottom();
  }

  private function unblock() {
    if (Model::unblock($this->ip)) {
      $back_link = sprintf('<a href="%1$s" title="%2$s">%2$s</a>', admin_url('/edit-comments.php?page=jg-spam-fighter&do=blacklisted'), esc_html__('Back to the list.', 'jg_spamfighter'));
      Model::admin_message(sprintf(esc_html__('%s has been removed from Blacklisted. %s', 'jg_spamfighter'), '<strong>' . $this->ip . '</strong>', $back_link));
    } else {
      Model::error_message();
    }
  }

  protected function view() {
    printf('<a href="%s" title="">%s</a>', admin_url('/edit-comments.php?page=jg-spam-fighter&do=blacklisted'), esc_html__('Go back', 'jg_spamfighter'));
  }

}

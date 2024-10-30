<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

use JG\SF\table\Table_Log as Log;
use JG\SF\inc\Model as Model;

class Block extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->block();
    $this->top();
    $this->logTabs();
    $this->view();
    $this->bottom();
  }

  private function block() {
    if ($this->ip_is_set()) {
      $count = Model::remove_by_IP($this->ip, $this->status, true);
      if ($count) {
        Model::admin_message(sprintf(esc_attr(_n('%d spam comment has been removed, and %s has been blocked.', '%d spam comments have been removed, and %s has been blocked.', $count, 'jg_spamfighter')), $count, sprintf('<a href="%1$s" target="_blank" title="%2$s stats">%2$s</a>', sprintf('%s/%s', JGSF_STATS, $this->ip), $this->ip)));
      } else {
        Model::error_message();
      }
    }
  }

  protected function view() {
    $this->logView();
  }

}

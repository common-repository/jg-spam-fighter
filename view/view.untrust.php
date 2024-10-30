<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

use \JG\SF\inc\Model as Model;

class UnTrust extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->top();
    if ($this->ip_is_set()) {
      if ($this->do == 'untrust') {
        $this->untrust();
      } else if ($this->do == 'remove') {
        $this->untrust();
        $this->remove();
      } else if ($this->do == 'block') {
        $this->untrust();
        $this->block();
      } else {
        $this->view();
      }
    }
    //$this->logTabs();
    //$this->view();
    $this->bottom();
  }

  private function untrust() {
    if (Model::untrust($this->ip)) {
      $back_link = sprintf('<a href="%1$s" title="%2$s">%2$s</a>', admin_url(Model::back_url()), esc_html__('Back to the list.', 'jg_spamfighter'));
      Model::admin_message(sprintf(esc_html__('%s has been untrusted. %s', 'jg_spamfighter'), '<strong>' . $this->ip . '</strong>', $back_link));
    } else {
      Model::error_message();
    }
  }

  private function remove() {
    $count = Model::remove_by_IP($this->ip, $this->status, false);
    if ($count) {
      Model::admin_message(sprintf(esc_attr(_n('%d spam comment has been removed.', '%d spam comments have been removed.', $count, 'jg_spamfighter')), $count));
    } else {
      Model::error_message();
    }
  }

  private function block() {
    $count = Model::remove_by_IP($this->ip, $this->status, true);
    if ($count) {
      Model::admin_message(sprintf(esc_html__('%d spam comments have been removed, and %s has been blocked.', 'jg_spamfighter'), $count, $this->ip));
    } else {
      Model::error_message();
    }
  }

  protected function view() {
    printf('<a href="%s" title="">%s</a>', admin_url(Model::back_url()), esc_html__('Go back', 'jg_spamfighter'));
  }

}

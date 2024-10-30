<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

use \JG\SF\inc\Model as Model;

class Trust extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->top();
    $this->trust();
    //$this->logTabs();
    //$this->view();
    $this->bottom();
  }

  private function trust() {
    if ($this->ip_is_set()) {
      if (Model::trust($this->ip)) {
        $back_link = sprintf('<a href="%1$s" title="%2$s">%2$s</a>', admin_url(Model::back_url()), esc_html__('Back to the list.', 'jg_spamfighter'));
        Model::admin_message(sprintf(esc_html__('%s has been trusted. %s', 'jg_spamfighter'), '<strong>' . $this->ip . '</strong>', $back_link));
      } else {
        Model::error_message();
      }
    }
  }

  protected function view() {
    printf('<a href="%s" title="">%s</a>', admin_url(Model::back_url()), esc_html__('Go back', 'jg_spamfighter'));
  }

}

<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

class Dashboard extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->top();
    $this->logTabs();
    $this->view();
    $this->bottom();
  }

  protected function view() {
    $this->logView();
  }

}

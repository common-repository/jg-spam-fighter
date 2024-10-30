<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

class Samples extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->top();
    $this->view();
    $this->bottom();
  }

  protected function view() {
    $this->sampleView();
  }

}

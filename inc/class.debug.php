<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

use \JG\SF\inc\Sapi as Sapi;

/*
 * This is basically a custom exception class to assist with debugging
 */

class Debug extends \Exception {

  public function __construct($message, $code = 0, Exception $previous = null) {

    if (is_array($message) || is_object($message)) {
      $message = print_r($message, true);
    }
    parent::__construct($message, $code, $previous);
  }

  public function report() {
    $file = str_replace(JGSF_PLUGIN_DIR, '', $this->file);
    if (JGSF_REPORT_ERRORS) {
      $args = array(
          'file' => $file,
          'line' => $this->line,
          'msg' => $this->message
      );
      $sapi = new Sapi();
      if ($sapi->post('rer', $args)) {
        //$sapi->log();
      }
    }
    if (WP_DEBUG_LOG) {
      $msg = sprintf("Time: %s\nFile: %s\nLine: %s, WP: %s, Plugin: %s\nMessage: %s\n\n", date('Y-m-d h:i:s'), $file, $this->line, get_bloginfo('version'), JGSF_VERSION, $this->message);
      error_log($msg, 3, JGSF_PLUGIN_DIR . 'error.log');
    }
  }

}

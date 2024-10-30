<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

use \JG\SF\inc\Model as Model;
use \JG\SF\inc\Debug as Bug;

/*
 * This class is for retrieving/submitting information from/to the cloud-base global database
 * it has 2 main methods such as post and get as well as a couple of helper methods, mostly used for debug purposes in development.
 */

class Sapi {

  private $rsp;
  private $status;
  private $body;

  public function __construct() {
    
  }

  public function post($ep = null, $args = array()) {
    if (empty($ep)) {
      return false;
    }
    $rsp = wp_remote_post(
            JGSF_API_EP . $ep, array(
        'user-agent' => 'JGSF|' . get_bloginfo('version') . '|' . JGSF_VERSION,
        'headers' => array(
//'Content-Type' => 'application/json',
//'Authorization' => 'Basic ' . base64_encode(JGSF_API_USERNAME . ':' . JGSF_API_PASSWORD)
        ),
        'body' => json_encode($args)
    ));
    $success = false;
    try {
      if (is_wp_error($rsp)) {
        throw new Bug('WP Error, Res: ' . $rsp->get_error_message());
      } else {
        $status = intval(wp_remote_retrieve_response_code($rsp));
        $this->rsp = $rsp;
        $this->status = $status;
        $this->body = (array) json_decode(wp_remote_retrieve_body($rsp), true);
        $success = ($status === 200);
      }
    } catch (Bug $bug) {
      $bug->report();
    }
    return $success;
  }

  public function get($ep = null) {
    if (empty($ep)) {
      return false;
    }
    $rsp = wp_remote_get(
            JGSF_API_EP . $ep, array(
        'user-agent' => 'JGSF|' . get_bloginfo('version') . '|' . JGSF_VERSION
    ));
    $success = false;
    try {
      if (is_wp_error($rsp)) {
        throw new Bug('WP Error, Res: ' . $rsp->get_error_message());
      } else {
        $status = intval(wp_remote_retrieve_response_code($rsp));
        $this->rsp = $rsp;
        $this->status = $status;
        $this->body = (array) json_decode(wp_remote_retrieve_body($rsp), true);
        $success = ($status === 200);
      }
    } catch (Bug $bug) {
      $bug->report();
    }
    return $success;
  }

  public function get_rsp($flat = false) {
    if ($flat) {
      return print_r($this->rsp, true);
    }
    return $this->rsp;
  }

  public function get_status() {
    return $this->status;
  }

  public function get_body($flat = false, $message_only = false) {
    if ($message_only) {
      $body = $this->body;
      if (isset($body['message'])) {
        $body = $body['message'];
      }
    } else {
      $body = $this->body;
    }
    if ($flat) {
      return print_r($body, true);
    }
    return $body;
  }

  public function show($exit = false) {
    echo '<pre>';
    echo 'Status:<br />';
    var_dump($this->status);
    echo '<br /><hr />';
    echo 'Body:<br />';
    var_dump($this->body);
    echo '<br /><hr />';
    echo 'Response:<br />';
    var_dump($this->rsp);
    echo '</pre>';
    if ($exit) {
      exit;
    }
  }

  public function log($message_only = false) {
    $error = sprintf("Time: %s\nstatus: %s\nbody: %s\n\n", date('Y-m-d h:i:s'), $this->status, $this->get_body(true, $message_only));
    error_log($error, 3, JGSF_PLUGIN_DIR . 'error.log');
  }

}

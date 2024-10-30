<?php

/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

/*
 * A class for formating contextual help tabs
 * this works with the 'template' files in jg_spam_fighter/help/*
 * help tab ID is used for the template file name
 */

class Help {

  public static function general() {
    $current_screen = get_current_screen();
    if (current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      $current_screen->add_help_tab(
              array(
                  'id' => 'problem',
                  'title' => esc_html__('The Problem', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      $current_screen->add_help_tab(
              array(
                  'id' => 'solution',
                  'title' => esc_html__('The Solution', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      self::sidebar($current_screen);
    }
  }

  public static function dash() {
    $current_screen = get_current_screen();
    if (current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      $current_screen->add_help_tab(
              array(
                  'id' => 'dash',
                  'title' => esc_html__('How to USE', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      $current_screen->add_help_tab(
              array(
                  'id' => 'problem',
                  'title' => esc_html__('The Problem', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      $current_screen->add_help_tab(
              array(
                  'id' => 'solution',
                  'title' => esc_html__('The Solution', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      self::sidebar($current_screen);
    }
  }

  public static function samples() {
    $current_screen = get_current_screen();
    if (current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      $current_screen->add_help_tab(
              array(
                  'id' => 'samples',
                  'title' => esc_html__('How to USE', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      self::sidebar($current_screen);
    }
  }

  public static function trusted() {
    $current_screen = get_current_screen();
    if (current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      $current_screen->add_help_tab(
              array(
                  'id' => 'trusted',
                  'title' => esc_html__('How to USE', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      self::sidebar($current_screen);
    }
  }

  public static function blacklisted() {
    $current_screen = get_current_screen();
    // Screen Content
    if (current_user_can(JGSF_REQUIRED_ADMIN_CAP)) {
      $current_screen->add_help_tab(
              array(
                  'id' => 'blacklisted',
                  'title' => esc_html__('How to USE', 'jg_spamfighter'),
                  'callback' => array('\JG\SF\inc\Help', 'printHelpContent')
              )
      );
      self::sidebar($current_screen);
    }
  }

  public static function sidebar($current_screen = '') {
    $current_screen = empty($current_screen) ? get_current_screen() : $current_screen;
    $current_screen->set_help_sidebar(self::getContent('sidebar'));
  }

  public static function getContent($filename = 'general') {
    $file = JGSF_PLUGIN_DIR . 'help/' . $filename . '.php';
    $content = esc_html__('no content', 'jg_spamfighter');
    if (file_exists($file)) {
      ob_start();
      include($file);
      $content = ob_get_contents();
      ob_end_clean();
    }
    return $content;
  }

  public static function printHelpContent($screen, $tab) {
    $file = JGSF_PLUGIN_DIR . 'help/' . $tab['id'] . '.php';
    if (file_exists($file)) {
      include($file);
    }
  }

}

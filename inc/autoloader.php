<?php

/* JG Spam Fighter -- Version: 0.5 */

/* nullify any existing autoloads */
spl_autoload_register(null, false);

/* specify extensions that may be loaded */
spl_autoload_extensions('.php');

spl_autoload_register('JGSF_classLoader');

/* class Loader */

function JGSF_classLoader($className) {
  if (false === strpos($className, 'JG\SF')) {
    return;
  }
  $className = str_replace('JG\SF\\', '', $className);
  $className = str_ireplace('_', '-', $className);
  $className = strtolower($className);
  list($dir, $class) = explode('\\', $className);
  if ($dir == 'inc') {
    $pref = '/class.';
  } else {
    $pref = '/' . $dir . '.';
  }
  $file = JGSF_PLUGIN_DIR . $dir . $pref . $class . '.php';
  if (file_exists($file)) {
    require_once($file);
    return;
  } else {
    wp_die(esc_html(sprintf(esc_html__('The file attempting to be loaded at %s does not exist.', 'jg_spamfighter'), $file)));
  }
}

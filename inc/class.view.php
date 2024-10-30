<?php
/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\inc;

use JG\SF\table\Log as Log;
use JG\SF\table\Sample as Sample;
use JG\SF\table\Trusted as Trusted;
use JG\SF\table\Blacklisted as Blacklisted;
use \JG\SF\inc\Model as Model;

/*
 * This abstract class is used to format HTML content for the admin menu pages, much like traditional vies in MVC design pattrerns
 * this works with 'template' files found in jg_spam_fighter/view/*
 */

abstract class View {

  protected $do;
  protected $ip;
  protected $page;
  protected $status;
  protected $args;
  private $title;
  private $dash_icon;

  public function __construct($args = array()) {
    $this->do = isset($args['do']) ? sanitize_text_field($args['do']) : 'log';
    $this->ip = isset($_REQUEST['for']) ? sanitize_text_field($_REQUEST['for']) : '';
    $this->page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : 'jg-spam-fighter';
    $this->status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all';
    $this->args = $args;
    $this->title = isset($this->args['title']) ? $this->args['title'] : '';
    $this->dash_icon = isset($this->args['dash_icon']) ? $this->args['dash_icon'] : '';
  }

  protected function set_title($title = '') {
    if ($title) {
      $this->title = $title;
    }
  }

  protected function set_dash_icon($dash_icon = '') {
    if ($dash_icon) {
      $this->dash_icon = $dash_icon;
    }
  }

  protected function top($form_url = '') {
    printf('<div class="wrap">
  <h2 class="dashicons-before %s"> %s</h2>
    %s
    <form method="post" action="%s" enctype="multipart/form-data">
      ', $this->dash_icon, $this->title, $this->below_title(), $form_url);
  }

  protected function below_title() {
    // overwrite
  }

  protected function tabs($tabs = array()) {
    if ($tabs) {
      ?>
      <h2 class="nav-tab-wrapper">
        <?php
        foreach ($tabs as $tab):
          $url = empty($this->ip) ? 'edit-comments.php?page=jg-spam-fighter&do=' . $tab['dos'][0] : 'edit-comments.php?page=jg-spam-fighter&do=' . $tab['dos'][0] . '&for=' . $this->ip;
          ?>
          <a href="<?php echo $url; ?>" title="<?php echo $tab['title']; ?>" class="nav-tab<?php echo in_array($this->do, $tab['dos']) ? ' nav-tab-active' : ''; ?>"><?php echo $tab['title']; ?></a>
        <?php endforeach; ?>
      </h2>
      <?php
    }
  }

  protected function logTabs() {
    $counts = Model::get_counts();
    $about_title = Model::have_news() ? sprintf(esc_html__('About %s', 'jg_spamfighter'), '<span style=\'color:red\'>new!</span>') : esc_html__('About', 'jg_spamfighter');
    $tabs = array(
        array('dos' => array('dashboard', 'remove', 'block', 'trust'), 'title' => sprintf(esc_html__('All (%d)', 'jg_spamfighter'), $counts['all'])),
        array('dos' => array('trusted'), 'title' => sprintf(esc_html__('Trusted (%d)', 'jg_spamfighter'), $counts['trusted'])),
        array('dos' => array('blacklisted', 'unblock'), 'title' => sprintf(esc_html__('Blacklisted (%d)', 'jg_spamfighter'), $counts['bl'])),
        array('dos' => array('about', 'news'), 'title' => $about_title)
    );
    $this->tabs($tabs);
  }

  protected function bottom() {
    echo wp_nonce_field('jgsf', JGSF_NONCE_NAME, true, false) . '
    </form>
    </div><!-- /.wrap -->';
  }

  protected function view() {
    ?>
    <p>
      <a href="<?php echo admin_url('edit-comments.php?page=jg-spam-fighter'); ?>" class="button button-secondary" title="<?php esc_html_e('Go back', 'jg_spamfighter'); ?>"><?php esc_html_e('Go back', 'jg_spamfighter'); ?></a>
    </p>
    <?php
  }

  protected function logView() {
    $log = new Log();
    $log->prepare_items();
    $log->display();
  }

  protected function sampleView() {
    $samples = new Sample();
    $counts = $samples->get_counts();
    $tabs = array(
        array('dos' => array('samples'), 'title' => sprintf(esc_html__('All (%s)', 'jg_spamfighter'), $counts['all'])),
        array('dos' => array('approved-samples'), 'title' => sprintf(esc_html__('Approved (%s)', 'jg_spamfighter'), $counts['approve'])),
        array('dos' => array('held-samples'), 'title' => sprintf(esc_html__('Held for Moderation (%s)', 'jg_spamfighter'), $counts['hold']))
    );
    $this->tabs($tabs);
    ?>
    <style>
      th#email {
        width: 250px;
      }
      th#web {
        width: 250px;
      }
      th#time {
        width: 200px;
      }
    </style>
    <?php
    $samples->prepare_items();
    $samples->display();
  }

  protected function trustedView() {
    $trusted = new Trusted();
    $trusted->prepare_items();
    $trusted->display();
  }

  protected function blacklistedView() {
    $blacklisted = new Blacklisted();
    $blacklisted->prepare_items();
    $blacklisted->display();
  }

  protected function isGo() {
    $go = false;
    if (!empty($_POST) && isset($_POST['go']) && wp_verify_nonce($_POST[JGSF_NONCE_NAME], 'jgsf')) {
      $go = true;
    }
    return $go;
  }

  protected function ip_is_set() {
    if (!empty($this->ip) && preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $this->ip)) {
      return true;
    } else {
      Model::admin_message(esc_html__('IP address is not set.', 'jg_spamfighter'), 'Error');
      return false;
    }
  }

}

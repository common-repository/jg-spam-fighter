<?php
/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

class Stats extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    $this->top();
    $this->view();
    $this->bottom();
  }

  protected function view() {
    ?>
    <h3><?php esc_html_e('The Power of the Community', 'jg_spamfighter'); ?></h3>
    <?php include_once JGSF_PLUGIN_DIR . 'partials/grow-potential.php'; ?>
    <p>
      <a href="<?php echo admin_url('edit-comments.php?page=jg-spam-fighter'); ?>" class="button button-secondary" title="<?php esc_html_e('Go back', 'jg_spamfighter'); ?>"><?php esc_html_e('Go back', 'jg_spamfighter'); ?></a>
    </p>
    <?php
  }

}

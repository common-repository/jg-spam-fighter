<?php
/* JG Spam Fighter -- Version: 0.5 */

namespace JG\SF\view;

use \JG\SF\inc\Model as Model;

class About extends \JG\SF\inc\View {

  public function __construct($args) {
    parent::__construct($args);
    if ($this->isGo() && isset($_POST['consent']) && intval($_POST['consent']) == 1) {
      update_option('jgsf_consent', 1);
      $message = esc_html__('Thank you for your consent!', 'jg_spamfighter');
      Model::admin_message($message, 'success', true);
    }
    $this->top();
    $this->set_seen();
    $this->logTabs();
    $this->view();
    $this->bottom();
  }

  protected function view() {
    ?>
    <div class="container">
      <div class="row">
        <div class="col-6">
          <br />
          <div class="video">
            <iframe src="https://www.youtube.com/embed/Rc0gO7h7ir8" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
          </div>
        </div><!-- /.col -->
        <div class="col-6">
          <h3><?php printf(esc_html__('Thank you for choosing %s!', 'jg_spamfighter'), JGSF_PLUGIN_NAME); ?></h3>
          <p><?php esc_html_e('Hi, my name is Jay Gaura.', 'jg_spamfighter'); ?></p>
          <p><?php printf(esc_html__('I am the developer behind %s and I would like to thank you for using my plugin or at least for trying it out!', 'jg_spamfighter'), '<strong>' . JGSF_PLUGIN_NAME . '</strong>'); ?></p>
          <p><strong><?php printf(esc_html__('I hope you are or will have a good experience using %s. If there is ever anything wrong with it or for any other reason, please feel free to %s. I will like your feedback and I always appreciate your support.', 'jg_spamfighter'), JGSF_PLUGIN_NAME, sprintf('<a href="%s" target="_blank">%s</a>', JGSF_CONTACT, esc_html__('contact me', 'jg_spamfighter'))); ?></strong></p>
          <?php if (empty(get_option('jgsf_consent', 0))): ?>
            <div class="consent">
              <p><?php esc_html_e('Oh, one more thing since you seem to be a new user here. Welcome!', 'jg_spamfighter'); ?></p>
              <p>
                <?php printf(esc_html__('To comply with WordPress plugin publishing rules I need to ask your consent to gather two pieces of information from your usage of the plugin. This is absolutely needed to make the plugin all that much more powerful and useful in the upcoming versions. Please watch the video on the left (if you haven\'t) to understand how and why. But if not, then&hellip; all I need to know is the spammy IP addresses that you will be blocking using the plugin (1) and the number of spammy comments you will be removing (2). That\'s all, and yes, that will make a big difference. You will see!', 'jg_spamfighter'), sprintf('<a href="%s" target="_blank">%s</a>', JGSF_CONTACT, esc_html__('contact me', 'jg_spamfighter'))); ?></p>
              <fieldset>
                <legend class="screen-reader-text"><span><?php esc_html_e('I consent', 'jg_spamfighter'); ?></span></legend>
                <label for="consent">
                  <input name="consent" type="checkbox" id="consent" value="1">
                  <?php esc_html_e('I give my consent.', 'jg_spamfighter'); ?>
                </label>
                <p class="submit"><input type="submit" name="go" id="submit" class="button button-primary" value="Submit"></p>
              </fieldset>
            </div><!-- /.consent -->
          <?php endif; ?>
          <?php
          $news = get_option('jgsf_news', '');
          echo!isset($news['news']) || empty($news['news'] || empty(get_option('jgsf_consent', 0))) ? '' : $news['news'];
          ?>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.grid -->
    <?php
  }

  private function set_seen() {
    $current_news = get_option('jgsf_news', '');
    $current_news['seen'] = 1;
    set_transient('jgsf_news', 0, WEEK_IN_SECONDS);
    update_option('jgsf_news', $current_news);
  }

}

<?php
/* JG Spam Fighter -- Version: 0.5 */
?>
<p><strong><?php esc_html_e('Blacklisted', 'jg_spamfighter'); ?></strong></p>
<ul>
  <li>
    <?php esc_html_e('This is where all the blocked or blacklisted IP addresses go.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php printf(esc_html__('If you hover your mouse over an IP address you will see 2 options: %s and %s.', 'jg_spamfighter'), '<strong>' . esc_html__('stats', 'jg_spamfighter') . '</strong>', '<strong>' . esc_html__('UnBlacklist', 'jg_spamfighter') . '</strong>'); ?>
    <p><img src="<?php echo JGSF_PLUGIN_URL . 'images/blacklisted-options.png'; ?>" alt="<?php esc_html_e('Blacklisted Options', 'jg_spamfighter'); ?>" /></p>
    <?php esc_html_e('Below you will find instructions and explanations for each option.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: Click on the link or on the IP address itself and you should be able to see some information about this IP address from the global database.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('STATS', 'jg_spamfighter'))); ?>
    <p class="description">
      <?php esc_html_e('this function is currently in a beta state (under testing and development)', 'jg_spamfighter'); ?>
    </p>
  </li>
  <li>
    <?php printf(esc_html__('%s: Choose this option if you want to remove this IP address from the "black list". The IP address will again be able to post comments on your site and you will be able to see those comments in the main list. From there you will be able to trust it, should you desire to do so.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('UNBLACKLIST', 'jg_spamfighter'))); ?>
  </li>
</ul>
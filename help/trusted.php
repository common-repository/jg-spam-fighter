<?php
/* JG Spam Fighter -- Version: 0.5 */
?>
<p><strong><?php esc_html_e('Trusted', 'jg_spamfighter'); ?></strong></p>
<ul>
  <li>
    <?php esc_html_e('Here you see a list of IP addresses that you or another Administrator have previously "trusted". The IP addresses shown here will be excluded from the list of potentially spammy IP addresses.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php esc_html_e('If you hover your mouse over an IP address you will see a bunch of options.', 'jg_spamfighter'); ?>
    <p><img src="<?php echo JGSF_PLUGIN_URL . 'images/trusted-options.png'; ?>" alt="<?php esc_html_e('Trusted Options', 'jg_spamfighter'); ?>" /></p>
    <?php esc_html_e('Below you will find instructions and explanations for each option.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: To doublecheck wherther an IP address "makes" good comments click on it. You will see a list of comments made from this IP address. If at least one of them looks sketchy you should probably untrust that IP address.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('SAMPLES', 'jg_spamfighter'))); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: Choose this option if you want to "unstrust" this IP address. That is it will again be shown on the list of potentially spammy IP addresses.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('UNTRUST', 'jg_spamfighter'))); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: by choosing this option you will untrust the IP address and remove all the comments made from this IP address.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('UNTRUST & REMOVE', 'jg_spamfighter'))); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: by choosing this option you will untrust the IP address, as well as remove all the comments made from this IP address and block the IP address itself to prevent it from further being able to post comments on your website.', 'jg_spamfighter'), sprintf('<strong>%s</strong>: ', esc_html__('UNTRUST, REMOVE & BLOCK', 'jg_spamfighter'))); ?>
  </li>
</ul>
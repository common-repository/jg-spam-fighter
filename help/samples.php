<?php
/* JG Spam Fighter -- Version: 0.5 */
?>
<p><strong><?php esc_html_e('Sample Comments', 'jg_spamfighter'); ?></strong></p>
<ul>
  <li>
    <?php esc_html_e('Here you see a list of comments made from the IP address.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php printf(esc_html__('If at least one of them looks sketchy (that is usually very easy to see), it\'s almost certain that they all are... You can (and should) %s that IP address.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('Remove & Block', 'jg_spamfighter'))); ?>
    <p><img src="<?php echo JGSF_PLUGIN_URL . 'images/remove-block.png'; ?>" alt="<?php esc_html_e('Remove & Block', 'jg_spamfighter'); ?>" /></p>
    <p class="description">
      <?php printf(esc_html__('That will put the IP address in %s, and any further attempts to post comments on your website from this IP address will be blocked.', 'jg_spamfighter'), sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=blacklisted" title="%s">%s</a>', esc_html__('Blacklisted', 'jg_spamfighter'), esc_html__('here', 'jg_spamfighter'))); ?>
    </p>
  </li>
  <li>
    <?php printf(esc_html__('Click on the %s button if the comments look good and you know this is trustworthy IP address.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('Trust', 'jg_spamfighter'))); ?>
    <p><img src="<?php echo JGSF_PLUGIN_URL . 'images/trust.png'; ?>" alt="<?php esc_html_e('Remove & Block', 'jg_spamfighter'); ?>" /></p>
    <p class="description">
      <?php printf(esc_html__('That will put the IP address in %s, and the IP address will not be listed in the "All" list any longer.', 'jg_spamfighter'), sprintf('<a href="edit-comments.php?page=jg-spam-fighter&do=trusted" title="%s">%s</a>', esc_html__('Trusted', 'jg_spamfighter'), esc_html__('here', 'jg_spamfighter'))); ?>
    </p>
  </li>
</ul>
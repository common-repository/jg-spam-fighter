<?php
/* JG Spam Fighter -- Version: 0.5 */
?>
<p><strong><?php esc_html_e('How to USE', 'jg_spamfighter'); ?></strong></p>
<p><?php esc_html_e('Follow these simple guidelines:', 'jg_spamfighter'); ?></p>
<ul>
  <li>
    <?php esc_html_e('Visit this page once in a while (when you feel the number of comments held for moderation is high enough).', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php esc_html_e('Here you see a list of IP addresses from which both good and bad (spammy) comments on your site were made. The IP addresses will be ordered by the number of comments - higher numbers on top.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php esc_html_e('Your goal here should be to discern the spammy IP addresses - responsible for spammy comments on your site. You will want to remove the bad comments made from the IP address and block the IP address from being able to make futher comments on your blog.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php printf(esc_html__('If you hover your mouse over an IP address you will see 3 options: %s, %s, and %s.', 'jg_spamfighter'), '<strong>' . esc_html__('Samples', 'jg_spamfighter') . '</strong>', '<strong>' . esc_html__('Remove & Block', 'jg_spamfighter') . '</strong>', '<strong>' . esc_html__('Trust', 'jg_spamfighter') . '</strong>'); ?>
    <p><img src="<?php echo JGSF_PLUGIN_URL . 'images/dash-options.png'; ?>" alt="<?php esc_html_e('Dashboard Options', 'jg_spamfighter'); ?>" /></p>
    <?php esc_html_e('Below you will find instructions and explanations for each option.', 'jg_spamfighter'); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: To determine wherther an IP address "makes" good comments click on it. You will see a list of %s - comments made from this IP address. If at least one of them looks sketchy (that is usually very easy to see), it\'s almost certain that they all are... That should be a good enough indication that you can (and should) safely remove and even block that IP address.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('SAMPLES', 'jg_spamfighter')), sprintf('<strong>%s</strong>', esc_html__('sample comments', 'jg_spamfighter'))); ?>
    <p class="description">
      <?php printf(esc_html__('By the way, the big number of the comments on the previous page was already a "red flag". That is why you are checking this one. Just to double make sure.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('Remove', 'jg_spamfighter')), sprintf('<strong>%s</strong>', esc_html__('Remove & Block', 'jg_spamfighter'))); ?>
    </p>
  </li>
  <li>
    <?php printf(esc_html__('%s: by choosing this option you will remove all the comments made from this IP address and block the IP address itself to prevent it from further being able to post submit comments on your website.', 'jg_spamfighter'), sprintf('<strong>%s</strong>: ', esc_html__('REMOVE & BLOCK', 'jg_spamfighter'))); ?>
  </li>
  <li>
    <?php printf(esc_html__('%s: Choose this option if you want to "white list" this IP address. That is it will not be shown on the list ever again. You want to trust an IP address if you are sure it belongs to a legit blog reader/commenter.', 'jg_spamfighter'), sprintf('<strong>%s</strong>', esc_html__('TRUST', 'jg_spamfighter'))); ?>
  </li>

</ul>
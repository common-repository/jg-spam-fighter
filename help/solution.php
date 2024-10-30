<?php
/* JG Spam Fighter -- Version: 0.5 */
?>
<h3><strong><?php esc_html_e('The Solution', 'jg_spamfighter'); ?></strong></h3>
<p><?php esc_html_e('When someone makes a comment on your blog the Wordpress takes a note of the IP (computer) address from which the comment was made. Spammers, whether robots or real persons usually make more than one comment from one such address. More like dozens if not hundreds of them.', 'jg_spamfighter'); ?></p>
<p><?php esc_html_e('Based on the above, if you were able to see what IP addresses make the most of comments on your website, and then if you were able to quickly examine some of the comments, you should be able to quickly tell who is responsible for all the mess on your website. Then you could easily isolate and block those spammy IP addresses and remove all their comments. Blocking the IP address would prevent them from being able to further spam your blog. That would be awesome, wouldn\'t it? And that is, in essence, how the plugin works.', 'jg_spamfighter'); ?></p>
<p><img src="<?php echo JGSF_PLUGIN_URL . 'images/solution.gif'; ?>" alt="<?php printf(esc_html__('Removing spam comments with %s', 'jg_spamfighter'), JGSF_PLUGIN_NAME); ?>" /></p>
<h3><strong><?php esc_html_e('The Possibilities are endless', 'jg_spamfighter'); ?></strong></h3>
<p><?php printf(esc_html__('Upcoming versions of the plugin will let you, the plugin user, to leverage %1$s of the entire %2$s user community. Every time a user, such as you, blocks an IP address and removes the spammy comments (that\'s all accomplished by a single click here), the spammy IP address will be reported into a %3$s. As the database grows new features will soon be available. For example, you may be able to have spammy comments automatically removed and even outright blocked based on some user-defined conditional settings. Say, if an IP address has been reported by other users a 100 times, go ahead and block it on my site too. As the developer I am open to %4$s here! The possibilities are endless! Please feel free to %5$s with any ideas and functionality requests.', 'jg_spamfighter'), '<strong>' . esc_html__('the power', 'jg_spamfighter') . '</strong>', '<strong>' . JGSF_PLUGIN_NAME . '</strong>', '<strong>' . sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', JGSF_SAPI_DOMAIN, esc_html__('cloud-based global system', 'jg_spamfighter')) . '</strong>', '<strong>' . sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', JGSF_CONTACT, esc_html__('community input', 'jg_spamfighter')) . '</strong>', '<strong>' . sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', JGSF_CONTACT, esc_html__('contact me', 'jg_spamfighter')) . '</strong>'); ?></p>
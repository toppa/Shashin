<div style="float: right; font-weight: bold;">
    <form class="paypal-form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_xclick" />
        <input type="hidden" name="business" value="YPVBWNFVNRGB4" />
        <input type="hidden" name="no_note" value="0" />
        <input type="hidden" name="cn" value="<?php _e('Would you like to say anything to me?', 'shashin'); ?>" />
        <input type="hidden" name="no_shipping" value="1" />
        <input type="hidden" name="rm" value="1" />
        <input type="hidden" name="return" value="http://www.toppa.com/wordpress-plugins/" />
        <input type="hidden" name="currency_code" value="USD" />
        <input type="hidden" name="item_name" value="<?php _e('Support Shashin', 'shashin'); ?>" />

        <div style="float: left; margin-right: 5px;">
            <?php _e('Tip your pizza delivery person?', 'shashin'); ?><br />
            <?php _e('Tip your plugin developer too!', 'shashin'); ?><br />

            <select name="amount">
                <option value="5.00"><?php _e('$5.00', 'shashin'); ?></option>
                <option value="10.00" selected="selected"><?php _e('$10.00', 'shashin'); ?></option>
                <option value="25.00"><?php _e('$25.00', 'shashin'); ?></option>
                <option value="50.00"><?php _e('$50.00', 'shashin'); ?></option>
                <option value="">&#8230; <?php _e('or any amount!', 'shashin'); ?></option>
            </select>
        </div>

        <div style="float: left;">
            <input type="image" src="<?php echo $this->functionsFacade->getPluginsUrl('/images/paypal_donate_button.png', __FILE__); ?>" border="0" name="submit" alt="<?php _e('Support Shashin', 'shashin'); ?>" title="<?php _e('Support Shashin', 'shashin'); ?>" />
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" />
        </div>
    </form>
</div>

<div class="wrap">
    <?php
        require_once('donate.php');
        screen_icon();
    ?>
    <h2><?php _e('Shashin', 'shashin');?></h2>
    <?php
        if ($this->successMessage) {
            echo '<div id="message" class="updated"><p>' . $this->successMessage .'</p></div>';
            $this->successMessage = null;
        }

        elseif ($this->errorMessage) {
            echo '<div id="message" class="error"><p>' . $this->errorMessage .'</p></div>';
            $this->errorMessage = null;
        }
    ?>
    <form method="post">
        <?php settings_fields('shashin'); ?>
        <input type="hidden" name="shashinAction" value="updateSettings" />
        <table class="form-table">
        <?php
            foreach ($this->settingsGroups as $groupName=>$groupData) {
                echo $this->createHtmlForSettingsGroupHeader($groupData);

                foreach ($this->refData as $k=>$v) {
                    if ($v['group'] == $groupName) {
                        echo $this->createHtmlForSettingsField($k);
                    }
                }
            }
        ?>
        </table>
        <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e('Save Settings', 'shashin'); ?>" /></p>
    </form>

</div>

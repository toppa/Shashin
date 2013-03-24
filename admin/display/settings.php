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
        <?php $this->functionsFacade->createAdminHiddenInputFields('shashin'); ?>
        <input type="hidden" name="shashinAction" value="updateSettings" />
        <div id="shashinTabs">
        <?php
            echo '<ul>' . PHP_EOL;

            foreach($this->settingsGroups as $groupData) {
                $label = strtolower(str_replace(" ", "_", $groupData['label']));
                echo "<li><a href='#shashinTabs-$label'>{$groupData['label']}</a></li>" . PHP_EOL;
            }

            echo '</ul>' .PHP_EOL;

            foreach ($this->settingsGroups as $groupName=>$groupData) {
                $label = strtolower(str_replace(" ", "_", $groupData['label']));

                echo "<div id='shashinTabs-$label'>" . PHP_EOL;
                echo "<table class='form-table'>" . PHP_EOL;
                echo $this->createHtmlForSettingsGroupHeader($groupData);

                foreach ($this->refData as $k=>$v) {
                    if ($v['group'] == $groupName) {
                        echo $this->createHtmlForSettingsField($k);
                    }
                }

                echo '</table></div>' . PHP_EOL;
            }
        ?>
        </div>
        <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e('Save Settings', 'shashin'); ?>" /></p>
    </form>
</div>


<?php

class Admin_ShashinWidgetWp extends WP_Widget {
    private $shashinFormFields;
    private $shashinDefaults = array(
        'limit' => null,
        'size' => 'small',
        'id' => null,
        'caption' => 'n',
        'columns' => 1,
        'order' => 'date',
        'reverse' => 'n',
        'crop' => 'n',
        'thumbnail' => null,
        'position' => 'center',
        'clear' => 'none'
    );

    public function Admin_ShashinWidgetWP() {
        $widgetSettings = array(
            'classname' => null,
            'description' => __('display Shashin Photos. Review your photos in the Shashin Tools menu to get their Photo IDs.', 'shashin')
        );

        $widgetTitle = __('Shashin Photos', 'shashin');
        parent::WP_Widget('ShashinWidgetWp', $widgetTitle, $widgetSettings);
    }

    public function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->shashinDefaults);
        $this->setFormFields();

        echo '<p><a href="http://www.toppa.com/shashin-wordpress-plugin/#widget" target="_blank">';
        echo __('Shashin widget help', 'shashin');
        echo '</a></p>' . PHP_EOL;

        if ($_SESSION['shashinError']) {
            echo '<div class="error"><p><strong>'
                . __('Error', 'shashin')
                . ':</strong> '
                . $_SESSION['shashinError']
                . '</p></div>' . PHP_EOL;
            unset($_SESSION['shashinError']);
        }

        echo '<table>' . PHP_EOL;
        foreach ($this->shashinFormFields as $k=>$v) {
            $inputName = $this->get_field_name($k);
            echo '<tr><td>' . $v['label'] . ':</td><td>';
            echo Lib_ShashinHtmlFormField::quickBuild($inputName, $v, $instance[$k]);
            echo '</td></tr>';
        }

        echo '</table>' . PHP_EOL;
    }

    public function setFormFields() {
        $this->shashinFormFields = array(
            'title' => array(
                'input' => array('type' => 'text', 'size' => 10),
                'label' => __('Title', 'shashin')
            ),
            'id' => array(
                'input' => array('type' => 'text', 'size' => 10),
                'label' => __('Photo IDs', 'shashin')
            ),
            'size' => array(
                'input' => array('type' => 'text', 'size' => 10),
                'label' => __('Size', 'shashin')
            ),
            'columns' => array(
                'input' => array('type' => 'text', 'size' => 10),
                'label' => __('Columns', 'shashin')
            ),
            'limit' => array(
                'input' => array('type' => 'text', 'size' => 10),
                'label' => __('Limit', 'shashin')
            ),
            'position' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'center' => __('Center', 'shashin'),
                        'left' => __('Float Left', 'shashin'),
                        'right' => __('Float Right', 'shashin'),
                        'none' => __('Float None', 'shashin'),
                        'inherit' => __('Float Inherit', 'shashin'),
                    )
                ),
                'label' => __('Position', 'shashin')
            ),
            'clear' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'left' => __('Left', 'shashin'),
                        'right' => __('Right', 'shashin'),
                        'both' => __('Both', 'shashin'),
                        'none' => __('None', 'shashin'),
                        'inherit' => __('Inherit', 'shashin'),
                    )
                ),
                'label' => __('CSS Clear', 'shashin')
            ),
            'order' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'date' => __('Date', 'shashin'),
                        'random' => __('Random', 'shashin'),
                        'user' => __('User (ID List)', 'shashin'),
                        'title' => __('Title', 'shashin'),
                        'location' => __('Location', 'shashin'),
                        'count' => __('Photo Count', 'shashin'),
                        'source' => __('Source Order', 'shashin'),
                        'filename' => __('Filename', 'shashin'),

                    )
                ),
                'label' => __('Order By', 'shashin')
            ),
            'reverse' => array(
                'input' => array(
                    'type' => 'radio',
                    'subgroup' => array(
                        'y' => __('Yes', 'shashin'),
                        'n' => __('No', 'shashin')
                    )
                ),
                'label' => __('Rev. Order', 'shashin')
            ),
            'caption' => array(
                'input' => array(
                    'type' => 'radio',
                    'subgroup' => array(
                        'y' => __('Yes', 'shashin'),
                        'n' => __('No', 'shashin')
                    )
                ),
                'label' => __('Show Caption', 'shashin')
            ),
            'crop' => array(
                'input' => array(
                    'type' => 'radio',
                    'subgroup' => array(
                        'y' => __('Yes', 'shashin'),
                        'n' => __('No', 'shashin')
                    )
                ),
                'label' => __('Crop Square', 'shashin')
            ),
            'thumbnail' => array(
                'input' => array('type' => 'text', 'size' => 10),
                'label' => __('Alt. Thumb IDs', 'shashin')
            ),
        );
    }

    public function update($newInstance, $oldInstance) {
        try {
            $widgetTitle = $newInstance['title'];
            unset($newInstance['title']);
            unset($oldInstance['title']);
            $arrayShortcode = array_merge($oldInstance, $newInstance);
            $shortcode = $this->getShortcodeForWidget($arrayShortcode);
            $arrayShortcode = $shortcode->cleanAndValidate();
            $arrayShortcode['title'] = $widgetTitle;
        }

        catch (Exception $e) {
            $_SESSION['shashinError'] = $e->getMessage();
            return $oldInstance;
        }

        return $arrayShortcode;
    }

    // yuck - there's no way to do dependency injection with a WP_Widget instance
    public function getShortcodeForWidget($arrayShortcode) {
        $publicContainer = new Public_ShashinContainer();
        return $publicContainer->getShortcode($arrayShortcode);
    }

    public function widget($args, $instance) {
        $before_widget = null;
        $after_widget = null;
        $before_title = null;
        $after_title = null;
        extract($args);
        echo $before_widget;

        if ($instance['title']) {
            $title = apply_filters('widget_title', $instance['title']);
            echo $before_title . $title . $after_title;
        }

        $arrayShortcode = $instance;
        unset($arrayShortcode['title']);
        $arrayShortcode['type'] = 'photo';
        $shashinWp = new ShashinWp();
        echo $shashinWp->handleShortcode($arrayShortcode);
        echo $after_widget;
    }
}

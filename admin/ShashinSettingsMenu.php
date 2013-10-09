<?php

class Admin_ShashinSettingsMenu {
    private $functionsFacade;
    private $settings;
    private $validSettings = array();
    private $invalidSettings = array();
    private $successMessage;
    private $errorMessage;
    private $request;
    private $relativePathToTemplate = 'display/settings.php';
    private $settingsGroups;
    private $refData;

    public function __construct() {
        $this->settingsGroups = array(
            'general' => array(
                'label' => __('General Settings', 'shashin'),
                'description' => ''
            ),
            'albumPhotos' => array(
                'label' => __('Album Photos display', 'shashin'),
                'description' => __('When clicking a Shashin album thumbnail to view its photos, these settings control how the album\'s photos are displayed.', 'shashin')
            ),
            'prettyphoto' => array(
                'label' => __('PrettyPhoto Settings', 'shashin'),
                'description' => __('PrettyPhoto is the recommended photo viewer included with Shashin. These settings apply only if you select "Use PrettyPhoto" under General Settings.', 'shashin')
            ),
            'fancybox' => array(
                'label' => __('Fancybox Settings', 'shashin'),
                'description' => __('Fancybox is included with Shashin, but prettyphoto is recommended, as v1 of Fancybox (the GPL compliant version) is no longer updated. These settings apply only if you select "Use Fancybox" under General Settings.', 'shashin')
            )
        );
        $this->refData = array(
            // General Settings
            'supportOldShortcodes' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('y' => __('Yes', 'shashin'), 'n' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('y', 'n'),
                'label' => __('Support old-style Shashin shortcodes?', 'shashin'),
                'help' => __('Select "yes" only if you have blog posts containing the shortcode format used with previous versions of Shashin (any versions from prior to 2011).', 'shashin'),
                'group' => 'general'
            ),
            'imageDisplay' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'source' => __('display at photo hosting site', 'shashin'),
                        'prettyphoto' => __('Use PrettyPhoto', 'shashin'),
                        'fancybox' => __('Use FancyBox', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('source', 'prettyphoto', 'fancybox'),
                'label' => __('How to display a full-size photo when its thumbnail is clicked', 'shashin'),
                'help' => __('PrettyPhoto is included with Shashin and works "out of the box." FancyBox is also included, but is being phased out.', 'shashin'),
                'group' => 'general'
            ),
            'expandedImageSize' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'xsmall' => __('X-Small (400px)', 'shashin'),
                        'small' => __('Small (640px)', 'shashin'),
                        'medium' => __('Medium (800px)', 'shashin'),
                        'large' => __('Large (912px)', 'shashin'),
                        'xlarge' => __('X-Large (1024px)', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('xsmall', 'small', 'medium', 'large', 'xlarge'),
                'label' => __('Expanded image size', 'shashin'),
                'help' => __('The expanded display size for a photo when its thumbnail is clicked. The actual size of a photo may be smaller if it\'s not available in the selected size.', 'shashin'),
                'group' => 'general'
            ),
            'defaultPhotoLimit' => array(
                'input' => array('type' => 'text', 'size' => 2),
                'validateFunction' => 'is_numeric',
                'label' => __('Default maximum number of photos to show', 'shashin'),
                'help' => __('The maximum number of photos to show if no photo IDs are provided and no limit is specified. This is also used to control the number of photos per set in a display of album photos.', 'shashin'),
                'group' => 'general'
            ),
            'scheduledUpdate' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('y' => __('Yes', 'shashin'), 'n' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('y', 'n'),
                'label' => __('Sync all albums daily?', 'shashin'),
                'help' => __('This will sync all your albums in Shashin once daily. It will not automatically add new albums.', 'shashin'),
                'group' => 'general'
            ),
            'themeMaxSize' => array(
                'input' => array('type' => 'text', 'size' => 4),
                'validateFunction' => 'is_numeric',
                'label' => __('Content width (in pixels) for your theme', 'shashin'),
                'help' => __('This is used in layout calculations when you use "max" as the number of columns or the image size in a Shashin tag. For example, if you set this to 600, and then in a shashin tag specify 3 columns and a "max" image size, each image will be approximately 200 pixels wide.', 'shashin'),
                'group' => 'general'
            ),
            'captionExif' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('date' => 'Date Only', 'all' => 'All', 'none' => 'None')),
                'validateFunction' => 'in_array',
                'validValues' => array('date', 'all', 'none'),
                'label' => __('Add camera EXIF data to expanded view caption?', 'shashin'),
                'help' => __('"All" includes the camera make, model, fstop, focal length, exposure time, and ISO.', 'shashin'),
                'group' => 'general'
            ),
            'thumbnailDisplay' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'rounded' => __('Rounded, overlay captions', 'shashin'),
                        'square' => __('Square, captions underneath', 'shashin'),
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('rounded', 'square'),
                'label' => __('Thumbnail display', 'shashin'),
                'help' => __('Rounded thumbnails have their captions shown in an overlay, and long captions are truncated. Square thumbanils show the full caption under the thumbnail. Note version of Internet Explorer less than 9 will always show square thumbnails, for browser compatibility.', 'shashin'),
                'group' => 'general'
            ),

            // Album Photos settings
            'albumPhotosSize' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'xsmall' => __('X-Small (72px)', 'shashin'),
                        'small' => __('Small (150px)', 'shashin'),
                        'medium' => __('Medium (300px)', 'shashin'),
                        'large' => __('Large (600px)', 'shashin'),
                        'xlarge' => __('X-Large (800px)', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('xsmall', 'small', 'medium', 'large', 'xlarge'),
                'label' => __('Photo thumbnail size', 'shashin'),
                'help' => '',
                'group' => 'albumPhotos'
            ),
            'albumPhotosCrop' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('y' => __('Yes', 'shashin'), 'n' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('y', 'n'),
                'label' => __('Crop thumbnail square if possible?', 'shashin'),
                'help' => __('Certain photo sizes can be cropped square (which ones depends on the photo hosting service; for Picasa the X-Small and Small sizes can be cropped).', 'shashin'),
                'group' => 'albumPhotos'
            ),
            'albumPhotosColumns' => array(
                'input' => array('type' => 'text', 'size' => 2),
                'validateFunction' => 'is_numeric',
                'label' => __('Number of columns', 'shashin'),
                'help' => '',
                'group' => 'albumPhotos'
            ),
            'albumPhotosOrder' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'id' => __('Date added to Shashin', 'shashin'),
                        'date' => __('Date photos taken', 'shashin'),
                        'filename' => __('Filename', 'shashin'),
                        'random' => __('Random', 'shashin'),
                        'source' => __('Order at photo hosting service', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('id', 'date', 'filename', 'random', 'source'),
                'label' => __('Order thumbnails by', 'shashin'),
                'help' => '',
                'group' => 'albumPhotos'
            ),
            'albumPhotosOrderReverse' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('y' => __('Yes', 'shashin'), 'n' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('y', 'n'),
                'label' => __('Reverse the order?', 'shashin'),
                'help' => __('For example, if you order by filename and then select "yes" for reversing the order, the thumbnails will appear in reverse alphabetical order.', 'shashin'),
                'group' => 'albumPhotos'
            ),
            'albumPhotosCaption' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('y' => __('Yes', 'shashin'), 'n' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('y', 'n'),
                'label' => __('Show captions under each thumbnail?', 'shashin'),
                'help' => '',
                'group' => 'albumPhotos'
            ),

            // PrettyPhoto settings
            'prettyPhotoTheme' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'pp_default' => __('Default', 'shashin'),
                        'light_rounded' => __('Light Rounded', 'shashin'),
                        'dark_rounded' => __('Dark Rounded', 'shashin'),
                        'light_square' => __('Light Square', 'shashin'),
                        'dark_square' => __('Dark Square', 'shashin'),
                        'facebook' => __('Facebook', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('pp_default', 'light_rounded', 'dark_rounded', 'light_square', 'dark_square', 'facebook'),
                'label' => __('Theme', 'shashin'),
                'help' => '',
                'group' => 'prettyphoto'
            ),
            'prettyPhotoOverlayGallery' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Overlay gallery?', 'shashin'),
                'help' => __('If "yes", a gallery will overlay the expanded view of an image when you mouse over it.', 'shashin'),
                'group' => 'prettyphoto'
            ),
            'prettyPhotoShowTitle' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Show title?', 'shashin'),
                'help' => __('If "yes", shows the photo caption in large text above the photo.', 'shashin'),
                'group' => 'prettyphoto'
            ),
            'prettyPhotoShowSocialButtons' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Show social connect buttons?', 'shashin'),
                'help' => __('If "yes", shows Twitter, Facebook, Pinterest, and Link buttons in the photo caption.', 'shashin'),
                'group' => 'prettyphoto'
            ),
            'prettyPhotoAutoplaySlideshow' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Auto-play slideshow?', 'shashin'),
                'help' => '',
                'group' => 'prettyphoto'
            ),
            'prettyPhotoSlideshow' => array(
                'input' => array('type' => 'text', 'size' => 5),
                'validateFunction' => 'is_numeric_or_empty',
                'label' => __('Autoplay image display time', 'shashin'),
                'help' => __('Enter a duration in milliseconds (e.g. "5000" for 5 seconds) for your slideshow speed.', 'shashin'),
                'group' => 'prettyphoto'
            ),

            // Fancybox settings
            'fancyboxCyclic' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Repeat slideshows?', 'shashin'),
                'help' => __('When viewing the final photo in slideshow, whether clicking "next" will start the slideshow over again with the first photo.', 'shashin'),
                'group' => 'fancybox'
            ),
            'fancyboxVideoWidth' => array(
                'input' => array('type' => 'text', 'size' => 3),
                'validateFunction' => 'is_numeric',
                'label' => __('Video Width', 'shashin'),
                'help' => __('Unfortunately video dimensions cannot be set dynamically with Fancybox', 'shashin'),
                'group' => 'fancybox'
            ),
            'fancyboxVideoHeight' => array(
                'input' => array('type' => 'text', 'size' => 3),
                'validateFunction' => 'is_numeric',
                'label' => __('Video Height', 'shashin'),
                'help' => '',
                'group' => 'fancybox'
            ),
            'fancyboxTransition' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'fade' => __('Fade', 'shashin'),
                        'elastic' => __('Elastic', 'shashin'),
                        'none' => __('None', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('fade', 'elastic', 'none'),
                'label' => __('Trasition effect', 'shashin'),
                'help' => __('The transition effect to apply when navigating between photos', 'shashin'),
                'group' => 'fancybox'
            ),
            'fancyboxInterval' => array(
                'input' => array('type' => 'text', 'size' => 5),
                'validateFunction' => 'is_numeric_or_empty',
                'label' => __('Autoplay image display time', 'shashin'),
                'help' => __('Enter a duration in milliseconds (e.g. "5000" for 5 seconds) to have all your slideshows run on an automatic timer. Leave blank for manually navigated slideshows.', 'shashin'),
                'group' => 'fancybox'
            ),
            'fancyboxLoadScript' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('y' => __('Yes', 'shashin'), 'n' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('y', 'n'),
                'label' => __('Load Shashin\'s Fancybox script?', 'shashin'),
                'help' => __('If you already have Fancybox installed on your site, select "no" to prevent Shashin from loading its own copy of Fancybox. Note Shashin has been tested only with version 1.3.4 of Fancybox.', 'shashin'),
                'group' => 'fancybox'
            )
        );
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function run() {
        if (isset($this->request['shashinAction']) && $this->request['shashinAction'] == 'updateSettings') {
            $this->validateSettings();
            $this->updateSettingsAndSetSuccessMessageIfNeeded();
            $this->setErrorMessageIfNeeded();
        }

        return $this->displayMenu();
    }

    public function displayMenu() {
        $message = $this->successMessage;
        ob_start();
        require_once($this->relativePathToTemplate);
        $settingsMenu = ob_get_contents();
        ob_end_clean();
        return $settingsMenu;
    }

    public function createHtmlForSettingsGroupHeader($groupData) {
        $html = '<tr>' . PHP_EOL
            . '<th scope="row" colspan="3"><h3>' . $groupData['label'] . "</h3>";

        if ($groupData['description']) {
            $html .= '<p><em>' . $groupData['description'] . '</em></p>' . PHP_EOL;
        }

        $html .= '</th>' . PHP_EOL
            .'</tr>' . PHP_EOL;
        return $html;
    }

    public function createHtmlForSettingsField($setting) {
        $value = array_key_exists($setting, $this->request) ? $this->request[$setting] : $this->settings->$setting;
        $html = '<tr valign="top">' . PHP_EOL
            . '<th scope="row"><label for="' . $setting . '">'
            . $this->refData[$setting]['label']
            . '</label></th>' . PHP_EOL
            . '<td nowrap="nowrap">'
            . Lib_ShashinHtmlFormField::quickBuild($setting, $this->refData[$setting], $value)
            . '</td>' . PHP_EOL
            . '<td>' . $this->refData[$setting]['help'] . '</td>' . PHP_EOL
            . '</tr>' . PHP_EOL;
        return $html;
    }

    public function validateSettings() {
        foreach ($this->refData as $k=>$v) {
            if (array_key_exists($k, $this->request)) {
                switch ($v['validateFunction']) {
                    case 'in_array':
                        $this->validateSettingsForInArray($k, $v);
                    break;
                    case 'is_numeric':
                        $this->validateSettingsForIsNumeric($k);
                    break;
                    case 'is_numeric_or_empty':
                        $this->validateSettingsForIsNumericOrEmpty($k);
                        break;
                    case 'htmlentities':
                        $this->validateSettingsForHtmlEntities($k);
                    break;
                    default:
                        throw New Exception(__('Unrecognized validation function', 'shashin'));
                }
            }

            // a checkbox group with all unchecked will not appear at all in $this->request
            elseif ($v['input']['type'] == 'checkbox') {
                $this->validSettings[$k] = array_fill(0, count($v['input']['subgroup']), null);
            }
        }

        return true;
    }

    public function validateSettingsForInArray($k, $v) {
        if (is_scalar($this->request[$k]) && in_array($this->request[$k], $v['validValues'])) {
            $this->validSettings[$k] = $this->request[$k];
        }
        elseif (is_array($this->request[$k]) && array_intersect($this->request[$k], $v['validValues'])) {
            $this->validSettings[$k] = $this->request[$k];
        }
        else {
            $this->invalidSettings[$k] = $this->request[$k];
        }
    }

    public function validateSettingsForIsNumeric($k) {
        if (is_numeric($this->request[$k])) {
            $this->validSettings[$k] = $this->request[$k];
        }

        else {
            $this->invalidSettings[$k] = $this->request[$k];
        }
    }

    public function validateSettingsForIsNumericOrEmpty($k) {
        if (is_numeric($this->request[$k]) || !$this->request[$k]) {
            $this->validSettings[$k] = $this->request[$k];
        }

        else {
            $this->invalidSettings[$k] = $this->request[$k];
        }
    }

    public function validateSettingsForHtmlEntities($k) {
        $this->validSettings[$k] = htmlentities($this->request[$k], ENT_COMPAT, 'UTF-8');
    }

    public function setErrorMessageIfNeeded() {
        if (!empty($this->invalidSettings)) {
            $this->errorMessage = __('The following settings have invalid values. Please try again.', 'shashin');
            $this->errorMessage .= '<br /><br /><strong>';

            foreach ($this->refData as $k=>$v) {
                if (array_key_exists($k, $this->invalidSettings)) {
                    $this->errorMessage .= $v['label'] . '<br />';
                }
            }
            $this->errorMessage .= '</strong>';
        }

        return $this->errorMessage;
    }

    public function updateSettingsAndSetSuccessMessageIfNeeded() {
        if (empty($this->invalidSettings)) {
            $this->settings->set($this->validSettings);
            $this->successMessage =  __('Settings saved', 'shashin');
        }

        return $this->successMessage;
    }
}

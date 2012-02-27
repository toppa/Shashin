<?php

class Admin_ShashinSettingsMenu {
    private $functionsFacade;
    private $settings;
    private $validSettings = array();
    private $invalidSettings = array();
    private $successMessage;
    private $errorMessage;
    private $request;
    private $relativePathToTemplate = 'Display/settings.php';
    private $settingsGroups;
    private $refData;

    public function __construct() {
        $this->settingsGroups = array(
            'general' => array(
                'label' => __('General Settings', 'shashin'),
                'description' => ''
            ),
            'albumPhotos' => array(
                'label' => __('Album Photos Display', 'shashin'),
                'description' => __('When clicking a Shashin album thumbnail to view its photos, these settings control how the album\'s photos are displayed.', 'shashin')
            ),
            'highslide' => array(
                'label' => __('Highslide Settings', 'shashin'),
                'description' => __('Highslide is the photo viewer included with Shashin. These settings apply only if you select "Use Highslide" above.', 'shashin')
            ),
            'otherViewer' => array(
                'label' => __('Other Viewer Settings', 'shashin'),
                'description' => __('These settings apply only if you select "Use another viewer" above. There are a wide variety of configuration requirements for different viewers. Shashin accommodates them by letting you control the attributes for the link and image tags used for its thumbnails. All links and thumbnails automatically get unique IDs (e.g. "shashinThumbnailLink_24", "shashinThumbnailImage_24").', 'shashin')
            ),
        );
        $this->refData =  array(
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
                        'source' => __('Display at photo hosting site', 'shashin'),
                        'highslide' => __('Use Highslide', 'shashin'),
                        'other' => __('Use another viewer', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('source', 'highslide', 'other'),
                'label' => __('How to display a full-size photo when its thumbnail is clicked', 'shashin'),
                'help' => __('Highslide is included with Shashin and works "out of the box." If you select "Use another viewer," you are responsible for implementing your own image viewer. See "Highslide Settings" and "Other Viewer Settings" below.', 'shashin'),
                'group' => 'general'
            ),
            'expandedImageSize' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'xsmall' => __('X-Small (400px)', 'shashin'),
                        'small' => __('Small (600px)', 'shashin'),
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
            'thumbPadding' => array(
                'input' => array('type' => 'text', 'size' => 2),
                'validateFunction' => 'is_numeric',
                'label' => __('Thumbnail div padding', 'shashin'),
                'help' => __('If you change the ".shashinThumbnailImage" padding value in shashin.css, make this 2x that value', 'shashin'),
                'group' => 'general'
            ),
            'themeMaxSize' => array(
                'input' => array('type' => 'text', 'size' => 4),
                'validateFunction' => 'is_numeric',
                'label' => __('Content width for your theme', 'shashin'),
                'help' => __('The maximum width available in your theme for a Shashin photo (or set of photos). This number is used to determine photo sizes when you use "max" as the number of columns or the size in a Shashin tag. Shashin will use the closest, smaller size available.', 'shashin'),
                'group' => 'general'
            ),
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
            'highslideAutoplay' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('true' => __('Yes', 'shashin'), 'false' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('true', 'false'),
                'label' => __('Autoplay slideshows?', 'shashin'),
                'help' => __('After someone clicks a thumbnail in a slideshow group, this determines whether the slideshow plays automatically.', 'shashin'),
                'group' => 'highslide'
            ),
            'highslideInterval' => array(
                'input' => array('type' => 'text', 'size' => 6),
                'validateFunction' => 'is_numeric',
                'label' => __('Autoplay image display time', 'shashin'),
                'help' => __('How long each photo is displayed in a autoplay slideshow (in milliseconds).', 'shashin'),
                'group' => 'highslide'
            ),
            'highslideRepeat' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Repeat slideshows?', 'shashin'),
                'help' => __('When viewing the final photo in slideshow, whether clicking "next" will start the slideshow over again with the first photo.', 'shashin'),
                'group' => 'highslide'
            ),
            'highslideOutlineType' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'beveled' => __('Beveled', 'shashin'),
                        'glossy-dark' => __('Glossy Dark', 'shashin'),
                        'rounded-black' => __('Rounded Black', 'shashin'),
                        'drop-shadow' => __('Drop Shadow', 'shashin'),
                        'outer-glow' => __('Outer Glow', 'shashin'),
                        'rounded-white' => __('Rounded White', 'shashin'),
                        'none' => __('None', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('beveled', 'glossy-dark', 'rounded-black', 'drop-shadow', 'outer-glow', 'rounded-white', 'none'),
                'label' => __('Expanded view outline style', 'shashin'),
                'help' => __('The graphic outline applied to expanded images.', 'shashin'),
                'group' => 'highslide'
            ),
            'highslideDimmingOpacity' => array(
                'input' => array('type' => 'text', 'size' => 6),
                'validateFunction' => 'is_numeric',
                'label' => __('Background dimming opacity', 'shashin'),
                'help' => __('Enter a number between 0 and 1. Indicates how much to dim the background when an image is expanded (enter 0 for no dimming). The default color is black (you can change it by editing .highslide-dimming in highslide.css.', 'shashin'),
                'group' => 'highslide'
            ),
            'highslideHideController' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('1' => __('Yes', 'shashin'), '0' => __('No', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('1', '0'),
                'label' => __('Hide slideshow controller on mouseout?', 'shashin'),
                'help' => __('Whether the slideshow controller should be hidden when the mouse leaves the expanded image.', 'shashin'),
                'group' => 'highslide'
            ),
            'highslideVPosition' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'top' => __('Top', 'shashin'),
                        'middle' => __('Middle', 'shashin'),
                        'bottom' => __('Bottom', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('top', 'middle', 'bottom'),
                'label' => __('Slideshow controller vertical position', 'shashin'),
                'help' => '',
                'group' => 'highslide'
            ),
            'highslideHPosition' => array(
                'input' => array(
                    'type' => 'select',
                    'subgroup' =>  array(
                        'left' => __('Left', 'shashin'),
                        'center' => __('Center', 'shashin'),
                        'right' => __('Right', 'shashin')
                    )
                ),
                'validateFunction' => 'in_array',
                'validValues' => array('left', 'center', 'right'),
                'label' => __('Slideshow controller horizontal position', 'shashin'),
                'help' => '',
                'group' => 'highslide'
            ),
            'captionExif' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('date' => 'Date Only', 'all' => 'All', 'none' => 'None')),
                'validateFunction' => 'in_array',
                'validValues' => array('date', 'all', 'none'),
                'label' => __('Add camera EXIF data to expanded view caption?', 'shashin'),
                'help' => __('"All" includes the camera make, model, fstop, focal length, exposure time, and ISO.', 'shashin'),
                'group' => 'highslide'
            ),
            'otherRelImage' => array(
                'input' => array('type' => 'text', 'size' => 15),
                'validateFunction' => 'htmlentities',
                'label' => __('Link "rel" for images', 'shashin'),
                'help' => __('The "rel" attribute for image links; e.g. "lightbox" if you are using Lightbox.', 'shashin'),
                'group' => 'otherViewer'
            ),
            'otherRelVideo' => array(
                'input' => array('type' => 'text', 'size' => 15),
                'validateFunction' => 'htmlentities',
                'label' => __('Link "rel" for videos', 'shashin'),
                'help' => __('The "rel" attribute for links if displaying a video; e.g. "vidbox" if you are using Videobox.', 'shashin'),
                'group' => 'otherViewer'
            ),
            'otherRelDelimiter' => array(
                'input' => array('type' => 'radio', 'subgroup' => array('brackets' => __('Brackets', 'shashin'), 'hyphen' => __('Hyphen', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('brackets', 'hyphen'),
                'label' => __('"rel" delimiter for image groups', 'shashin'),
                'help' => __('How to delimit image groups in a rel tag. Some viewers use brackets (e.g. "lightbox[33]") and some use hyphens (e.g. "lightbox-33").', 'shashin'),
                'group' => 'otherViewer'
            ),
            'otherLinkClass' => array(
                'input' => array('type' => 'text', 'size' => 15),
                'validateFunction' => 'htmlentities',
                'label' => __('Class for links', 'shashin'),
                'help' => __('A CSS class to apply to the link tags for thumbnails. Leave blank for none.', 'shashin'),
                'group' => 'otherViewer'
            ),
            'otherImageClass' => array(
                'input' => array('type' => 'text', 'size' => 15),
                'validateFunction' => 'htmlentities',
                'label' => __('Class for thumbnails', 'shashin'),
                'help' => __('A CSS class to apply to the thumbnail image tags. Leave blank for none.', 'shashin'),
                'group' => 'otherViewer'
            ),
            'otherTitle' => array(
                'input' => array('type' => 'checkbox', 'subgroup' => array('links' => __('Links', 'shashin'), 'images' => __('Images', 'shashin'))),
                'validateFunction' => 'in_array',
                'validValues' => array('links', 'images'),
                'label' => __('Use photo caption as "title" for', 'shashin'),
                'help' => __('You can use the photo\'s caption as the "title" for for its link tag, its image tag, or both.', 'shashin'),
                'group' => 'otherViewer'
            ),

        );
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
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
        if ($this->request['shashinAction'] == 'updateSettings') {
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
            . '<th scope="row" colspan="3"><h3>' . $groupData['label'] . '</h3></th>' . PHP_EOL
            . '</tr>' . PHP_EOL;

            if ($groupData['description']) {
                $html .= '<tr>' . PHP_EOL
                    . '<th colspan="3" scope="row">' . $groupData['description'] . '</th>' . PHP_EOL
                    . '</tr>' . PHP_EOL;
            }

        return $html;
    }

    public function createHtmlForSettingsField($setting) {
        $value = array_key_exists($setting, $this->request) ? $this->request[$setting] : $this->settings->$setting;
        $html = '<tr valign="top">' . PHP_EOL
            . '<th scope="row"><label for="' . $setting . '">'
            . $this->refData[$setting]['label']
            . '</label></th>' . PHP_EOL
            . '<td nowrap="nowrap">'
            . ToppaHtmlFormField::quickBuild($setting, $this->refData[$setting], $value)
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
                    case 'htmlentities':
                        $this->validateSettingsForHtmlEntities($k);
                    break;
                    default:
                        throw New Exception(__('Unrecognized validation function', 'shashin'));
                }
            }

            // a checkbox group with all unchecked will not appear at all in $this->request
            elseif ($v['input']['type'] == 'checkbox') {
                $this->validSettings[$k] = array();
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
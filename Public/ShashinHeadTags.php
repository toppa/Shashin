<?php


class Public_ShashinHeadTags {
    private $version;
    private $functionsFacade;
    private $settings;

    public function __construct($version) {
        $this->version = $version;
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function run() {
        $shashinCssUrl = $this->functionsFacade->getUrlforCustomizableFile('shashin.css', __FILE__, 'Display/');
        $this->functionsFacade->enqueueStylesheet('shashinStyle', $shashinCssUrl, false, $this->version);
        $baseUrl = $this->functionsFacade->getPluginsUrl('/Display/', __FILE__);
        $this->functionsFacade->enqueueScript(
            'shashinPhotoGroupsDisplayer',
            $baseUrl . 'photoGroupsDisplayer.js',
            array('jquery'),
            $this->version
        );
        $adminAjax = $this->functionsFacade->getAdminUrl('admin-ajax.php');
        $photoGroupsDisplayerParams = array('ajaxurl' => $adminAjax);
        $this->functionsFacade->localizeScript('shashinPhotoGroupsDisplayer', 'shashinPhotoGroupsDisplayer', $photoGroupsDisplayerParams);

        if ($this->settings->imageDisplay == 'highslide') {
            $highslideCssUrl = $this->functionsFacade->getUrlforCustomizableFile('highslide.css', __FILE__, 'Display/highslide/');
            $this->functionsFacade->enqueueStylesheet('highslideStyle', $highslideCssUrl, false, '4.1.12');
            $this->functionsFacade->enqueueScript('highslide', $baseUrl . 'highslide/highslide.js', false, '4.1.12');
            $this->functionsFacade->enqueueScript('swfobject', $baseUrl . 'highslide/swfobject.js', false, '2.2');
            $this->functionsFacade->enqueueScript('highslideSettings', $baseUrl . 'highslideSettings.js', false, $this->version);
            $this->functionsFacade->localizeScript('highslideSettings', 'highslideSettings', array(
                'graphicsDir' => $baseUrl . 'highslide/graphics/',
                'outlineType' => $this->settings->highslideOutlineType,
                'dimmingOpacity' => $this->settings->highslideDimmingOpacity,
                'interval' => $this->settings->highslideInterval,
                'repeat' => $this->settings->highslideRepeat,
                'position' => $this->settings->highslideVPosition . ' ' . $this->settings->highslideHPosition,
                'hideController' => $this->settings->highslideHideController
            ));
        }
    }
}

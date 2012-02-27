<?php


class Public_ShashinHeadTags {
    private $version;
    private $functionsFacade;
    private $settings;
    private $baseUrl;

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
        $this->baseUrl = $this->functionsFacade->getPluginsUrl('/Display/', __FILE__);
        $this->enqueueForShashin();
        $this->enqueueForFancyBox();
        $this->enqueueForHighslide();

    }

    public function enqueueForShashin() {
        $shashinCssUrl = $this->functionsFacade->getUrlforCustomizableFile('shashin.css', __FILE__, 'Display/');
        $this->functionsFacade->enqueueStylesheet('shashinStyle', $shashinCssUrl, false, $this->version);
        $this->functionsFacade->enqueueScript(
            'shashinPhotoGroupsDisplayer',
            $this->baseUrl . 'photoGroupsDisplayer.js',
            array('jquery'),
            $this->version
        );
        $adminAjax = $this->functionsFacade->getAdminUrl('admin-ajax.php');
        $photoGroupsDisplayerParams = array('ajaxurl' => $adminAjax);
        $this->functionsFacade->localizeScript('shashinPhotoGroupsDisplayer', 'shashinPhotoGroupsDisplayer', $photoGroupsDisplayerParams);
        return true;
    }

    public function enqueueForFancyBox() {
        if ($this->settings->imageDisplay != 'fancybox') {
            return true;
        }

        $fancyboxCssUrl = $this->functionsFacade->getUrlforCustomizableFile('jquery.fancybox.css', __FILE__, 'Display/fancybox/');
        $this->functionsFacade->enqueueStylesheet('shashinFancyboxStyle', $fancyboxCssUrl, false, '1.3.4');
        $this->functionsFacade->enqueueScript(
            'shashinFancybox',
            $this->baseUrl . 'fancybox/jquery.fancybox.js',
            array('jquery'),
            '1.3.4'
        );
        $this->functionsFacade->enqueueScript(
            'shashinFancyboxSettings',
            $this->baseUrl . 'fancyboxSettings.js',
            array('shashinFancybox'),
            $this->version
        );

        $this->functionsFacade->localizeScript('shashinFancyboxSettings', 'shashinFancyboxSettings', array(
            'fancyboxDir' => $this->baseUrl . 'fancybox/',
        ));
    }

    public function enqueueForHighslide() {
        if ($this->settings->imageDisplay != 'highslide') {
            return true;
        }

        $highslideCssUrl = $this->functionsFacade->getUrlforCustomizableFile('highslide.css', __FILE__, 'Display/highslide/');
        $this->functionsFacade->enqueueStylesheet('shashinHighslideStyle', $highslideCssUrl, false, '4.1.12');
        $this->functionsFacade->enqueueScript('shashinHighslide', $this->baseUrl . 'highslide/highslide.js', false, '4.1.12');
        $this->functionsFacade->enqueueScript('shashinSwfobject', $this->baseUrl . 'highslide/swfobject.js', false, '2.2');
        $this->functionsFacade->enqueueScript(
            'shashinHighslideSettings',
            $this->baseUrl . 'highslideSettings.js',
            array('shashinHighslide'),
            $this->version
        );

        $this->functionsFacade->localizeScript('shashinHighslideSettings', 'shashinHighslideSettings', array(
            'graphicsDir' => $this->baseUrl . 'highslide/graphics/',
            'outlineType' => $this->settings->highslideOutlineType,
            'dimmingOpacity' => $this->settings->highslideDimmingOpacity,
            'interval' => $this->settings->highslideInterval,
            'repeat' => $this->settings->highslideRepeat,
            'position' => $this->settings->highslideVPosition . ' ' . $this->settings->highslideHPosition,
            'hideController' => $this->settings->highslideHideController
        ));
    }
}

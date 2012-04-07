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

        if ($this->settings->imageDisplay == 'fancybox' && $this->settings->fancyboxLoadScript != 'n') {
            $fancyboxCssUrl = $this->functionsFacade->getUrlforCustomizableFile('jquery.fancybox.css', __FILE__, 'Display/fancybox/');
            $this->functionsFacade->enqueueStylesheet('shashinFancyboxStyle', $fancyboxCssUrl, false, '1.3.4');
            $this->functionsFacade->enqueueScript(
                'shashinFancybox',
                $this->baseUrl . 'fancybox/jquery.fancybox.js',
                array('jquery'),
                '1.3.4'
            );
        }

        $this->functionsFacade->enqueueScript(
            'shashinJs',
            $this->baseUrl . 'shashin.js',
            array('jquery'),
            $this->version
        );

        // need to load this after the fancybox stylesheet since we are doing some overriding
        $shashinCssUrl = $this->functionsFacade->getUrlforCustomizableFile('shashin.css', __FILE__, 'Display/');
        $this->functionsFacade->enqueueStylesheet('shashinStyle', $shashinCssUrl, array('shashinFancyboxStyle'), $this->version);

        $adminAjax = $this->functionsFacade->getAdminUrl('admin-ajax.php');
        $shashinJsParams = array(
            'ajaxUrl' => $adminAjax,
            'imageDisplayer' => $this->settings->imageDisplay,
            'fancyboxDir' => $this->baseUrl . 'fancybox/',
            'fancyboxCyclic' => $this->settings->fancyboxCyclic,
            'fancyboxVideoWidth' => $this->settings->fancyboxVideoWidth,
            'fancyboxVideoHeight' => $this->settings->fancyboxVideoHeight,
            'fancyboxTransition' => $this->settings->fancyboxTransition,
            'fancyboxInterval' => $this->settings->fancyboxInterval
        );

        $this->functionsFacade->localizeScript('shashinJs', 'shashinJs', $shashinJsParams);
        return true;
    }
}

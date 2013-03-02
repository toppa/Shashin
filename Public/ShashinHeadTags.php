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
        $shashinJsParams = array(
            'ajaxUrl' => $this->functionsFacade->getAdminUrl('admin-ajax.php'),
            'imageDisplayer' => $this->settings->imageDisplay,
        );

        if ($this->settings->imageDisplay == 'prettyphoto') {
            $shashinJsParams['prettyPhotoTheme'] = $this->settings->prettyPhotoTheme;
            $shashinJsParams['prettyPhotoOverlayGallery'] = $this->settings->prettyPhotoOverlayGallery;
            // need to get the desired width - we can get it from any subclass of the PhotoDisplayer
            $photoDisplayerSkeleton = new Public_ShashinPhotoDisplayerPicasaPrettyPhoto();
            $sizesMap = $photoDisplayerSkeleton->getExpandedSizesMap();
            $shashinJsParams['prettyPhotoDefaultWidth'] = $sizesMap[$this->settings->expandedImageSize];
            // assume 4:3 ratio
            $shashinJsParams['prettyPhotoDefaultHeight'] = $sizesMap[$this->settings->expandedImageSize] * .75;
            $shashinJsParams['prettyPhotoShowTitle'] = $this->settings->prettyPhotoShowTitle;
            $shashinJsParams['prettyPhotoAutoplaySlideshow'] = $this->settings->prettyPhotoAutoplaySlideshow;
            $shashinJsParams['prettyPhotoSlideshow'] = $this->settings->prettyPhotoSlideshow;

            $prettyPhotoCssUrl = $this->functionsFacade->getUrlforCustomizableFile('prettyPhoto.css', __FILE__, 'Display/prettyPhoto/');
            $this->functionsFacade->enqueueStylesheet('shashinPrettyPhotoStyle', $prettyPhotoCssUrl, false, '1.3.4');
            $this->functionsFacade->enqueueScript(
                'shashinPrettyPhoto',
                $this->baseUrl . 'prettyPhoto/jquery.prettyPhoto.js',
                array('jquery'),
                '3.1.5'
            );
        }

        elseif ($this->settings->imageDisplay == 'fancybox') {
            $shashinJsParams['fancyboxDir'] = $this->baseUrl . 'fancybox/';
            $shashinJsParams['fancyboxCyclic'] = $this->settings->fancyboxCyclic;
            $shashinJsParams['fancyboxVideoWidth'] = $this->settings->fancyboxVideoWidth;
            $shashinJsParams['fancyboxVideoHeight'] = $this->settings->fancyboxVideoHeight;
            $shashinJsParams['fancyboxTransition'] = $this->settings->fancyboxTransition;
            $shashinJsParams['fancyboxInterval'] = $this->settings->fancyboxInterval;

            if ($this->settings->fancyboxLoadScript != 'n') {
                $fancyboxCssUrl = $this->functionsFacade->getUrlforCustomizableFile(
                    'jquery.fancybox.css',
                    __FILE__,
                    'Display/fancybox/'
                );
                $this->functionsFacade->enqueueStylesheet(
                    'shashinFancyboxStyle',
                    $fancyboxCssUrl,
                    false,
                    '1.3.4'
                );
                $this->functionsFacade->enqueueScript(
                    'shashinFancybox',
                    $this->baseUrl . 'fancybox/jquery.fancybox.js',
                    array('jquery'),
                    '1.3.4'
                );
            }
        }

        $this->functionsFacade->enqueueScript(
            'shashinJs',
            $this->baseUrl . 'shashin.js',
            array('jquery'),
            $this->version,
            true
        );

        // need to load this after the fancybox stylesheet since we are doing some overriding
        $shashinCssUrl = $this->functionsFacade->getUrlforCustomizableFile('shashin.css', __FILE__, 'Display/');
        $this->functionsFacade->enqueueStylesheet('shashinStyle', $shashinCssUrl, false, $this->version);
        $this->functionsFacade->localizeScript('shashinJs', 'shashinJs', $shashinJsParams);
        return true;
    }
}

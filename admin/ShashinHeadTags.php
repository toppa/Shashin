<?php


class Admin_ShashinHeadTags {
    private $version;
    private $functionsFacade;
    private $scriptsObject;

    public function __construct($version) {
        $this->version = $version;
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setScriptsObject($scriptsObject) {
        $this->scriptsObject = $scriptsObject;
        return $this->scriptsObject;
    }

    public function run() {
        $cssUrl = $this->functionsFacade->getUrlforCustomizableFile('admin.css', __FILE__, 'display/');
        $this->functionsFacade->enqueueStylesheet('shashinAdminStyle', $cssUrl, false, $this->version);
        $jsUrl = $this->functionsFacade->getUrlforCustomizableFile('admin.js', __FILE__, 'display/');
        $this->functionsFacade->enqueueScript('shashinAdminScript', $jsUrl, array('jquery', 'jquery-ui-tabs'), $this->version);
        $menuDisplayUrl = $this->functionsFacade->getPluginsUrl('/display/', __FILE__);
        $this->functionsFacade->localizeScript('shashinAdminScript', 'shashinDisplay', array('url' => $menuDisplayUrl));

        // WordPress comes with jquery-ui scripts but not the themes
        // thank you http://snippets.webaware.com.au/snippets/load-a-nice-jquery-ui-theme-in-wordpress/

        /*
         * @todo: in WP 3.4.1 this returns 1.8.20, which returns a 404 from google
         * hardcoding to 1.8.18 for now
         */
        //$jqueryUi = $this->scriptsObject->query('jquery-ui-core');
        $jqueryUi = new stdClass();
        $jqueryUi->ver = '1.8.18';

        if ($jqueryUi) {
            $uiBase = "https://ajax.googleapis.com/ajax/libs/jqueryui/{$jqueryUi->ver}/themes/base";
            $this->functionsFacade->registerStylesheet('jquery-ui-core', "$uiBase/jquery.ui.core.css", FALSE, $jqueryUi->ver);
            $this->functionsFacade->registerStylesheet('jquery-ui-theme', "$uiBase/jquery.ui.theme.css", FALSE, $jqueryUi->ver);
            $this->functionsFacade->registerStylesheet('jquery-ui-tabs', "$uiBase/jquery.ui.tabs.css", array('jquery-ui-core','jquery-ui-theme'), $jqueryUi->ver);
            $this->functionsFacade->enqueueStylesheet('jquery-ui-tabs');
        }

    }
}

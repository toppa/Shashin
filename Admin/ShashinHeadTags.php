<?php


class Admin_ShashinHeadTags {
    private $version;
    private $functionsFacade;

    public function __construct($version) {
        $this->version = $version;
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }
    public function run() {
        $cssUrl = $this->functionsFacade->getUrlforCustomizableFile('admin.css', __FILE__, 'Display/');
        $this->functionsFacade->enqueueStylesheet('shashinAdminStyle', $cssUrl, false, $this->version);
        $jsUrl = $this->functionsFacade->getUrlforCustomizableFile('admin.js', __FILE__, 'Display/');
        $this->functionsFacade->enqueueScript('shashinAdminScript', $jsUrl, array('jquery'), $this->version);
        $menuDisplayUrl = $this->functionsFacade->getPluginsUrl('/Display/', __FILE__);
        $this->functionsFacade->localizeScript('shashinAdminScript', 'shashinDisplay', array('url' => $menuDisplayUrl));
    }
}

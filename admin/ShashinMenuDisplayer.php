<?php

abstract class Admin_ShashinMenuDisplayer {
    protected $functionsFacade;
    protected $request;
    protected $defaultOrderBy;
    protected $defaultReverse = 'n';
    protected $shortcode;
    protected $sortArrow;
    protected $relativePathToTemplate;
    protected $collection;
    protected $album;
    protected $container;

    public function __construct() {
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function setCollection(Lib_ShashinDataObjectCollection $collection) {
        $this->collection = $collection;
        return $this->collection;
    }

    abstract public function setAlbum(Lib_ShashinAlbum $album = null);

    public function setContainer(Public_ShashinContainer $container = null) {
        $this->container = $container;
        return $this->container;
    }

    public function run($message = null) {
        if (array_key_exists('shashinOrderBy', $this->request) && $this->request['shashinOrderBy']) {
            $this->checkOrderByNonce();
        }

        $message .= $this->showUpgradeCleanupNotice($message);
        $shortcodeMimic = $this->mimicShortcode();
        $dataObjects = $this->getDataObjects($shortcodeMimic);
        $refData = $this->collection->getRefData();
        ob_start();
        require_once($this->relativePathToTemplate);
        $toolsMenu = ob_get_contents();
        ob_end_clean();
        return $toolsMenu;
    }

    public function showUpgradeCleanupNotice() {
        if (!$this->functionsFacade->getSetting('shashin_options')) {
            return null;
        }

        $url = '?page=ShashinToolsMenu&amp;shashinAction=cleanupUpgrade';
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, 'shashinNonceCleanupUpgrade');

        $notice = '<p><strong>';
        $notice .= __('Upgrade notice', 'shashin');
        $notice .= ':</strong> ';
        $notice .= __('Please click "Sync All" below to complete the upgrade. Then review your Shashin albums, tags, and photos. If everything looks correct, please', 'shashin');
        $notice .= ' <a href="' . $noncedUrl . '">';
        $notice .= __('click here to remove the old settings and database backup', 'shashin');
        $notice .= '</a> ';
        $notice .= __('(which will remove this nag). Also, if you have posts containing Shashin tags, go to the Shashin settings menu to turn on support for old-style tags.', 'shashin');
        $notice .= '</p>';
        return $notice;
    }

    public function mimicShortcode() {
        if (array_key_exists('shashinOrderBy', $this->request) && is_string($this->request['shashinOrderBy'])) {
            $orderBy = $this->request['shashinOrderBy'];
        }

        else {
            $orderBy = $this->defaultOrderBy;
        }

        if (array_key_exists('shashinReverse', $this->request) && is_string($this->request['shashinReverse'])) {
            $reverse = $this->request['shashinReverse'];
        }

        else {
            $reverse = $this->defaultReverse;
        }

        $shortcodeData = array(
            'order' => $orderBy,
            'reverse' => $reverse,
        );

        if ($this->album) {
            $shortcodeData['id'] = $this->album->id;
            $shortcodeData['type'] = 'albumphotos';
            $shortcodeData['size'] = 'xsmall';
            $shortcodeData['crop'] = 'y';
        }

        return $shortcodeData;
    }

    public function getDataObjects($shortcodeData) {
        $this->shortcode = $this->container->getShortcode($shortcodeData);
        $this->collection->setNoLimit(true);
        return $this->collection->getCollectionForShortcode($this->shortcode);
    }

    abstract public function generateOrderByLink($column, $columnLabel);

    public function checkOrderByNonce() {
        $nonceName = "shashinNonce_" . $this->request['shashinOrderBy'];
        return $this->functionsFacade->checkAdminNonceFields($nonceName);
    }

    public function setSortArrowAndOrderByUrl($column) {
        if (!isset($this->request['shashinReverse'])) {
            $this->request['shashinReverse'] = null;
        }

        switch ($this->request['shashinReverse']) {
        case 'y':
            $reverse = 'n';
            $this->sortArrow = ($this->request['shashinOrderBy'] == $column) ? '&uarr;' : '';
            break;
        case 'n':
            $reverse = 'y';
            $this->sortArrow = ($this->request['shashinOrderBy'] == $column) ? '&darr;' : '';
            break;
        default:
            $reverse = 'y';
            $this->sortArrow = ($this->defaultOrderBy == $column) ? '&darr;' : '';
        }

        return "?page=ShashinToolsMenu&amp;shashinOrderBy=$column&amp;shashinReverse=" . $reverse;
    }

    public function getSortArrow() {
        return $this->sortArrow;
    }

    protected function setOrderByNonce($column, $orderByUrl) {
        $nonceName = "shashinNonce_" . $column;
        return $this->functionsFacade->addNonceToUrl($orderByUrl, $nonceName);
    }
}

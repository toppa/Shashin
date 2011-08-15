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

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function setRequest(array $request) {
        $this->request = $request;
    }

    public function setCollection(Lib_ShashinDataObjectCollection $collection) {
        $this->collection = $collection;
    }

    abstract public function setAlbum(Lib_ShashinAlbum $album = null);

    public function setContainer(Public_ShashinContainer $container = null) {
        $this->container = $container;
    }

    public function run($message = null) {
        if ($this->request['shashinOrderBy']) {
            $this->checkOrderByNonce();
        }

        $shortcodeMimic = $this->mimicShortcode();
        $dataObjects = $this->getDataObjects($shortcodeMimic);
        $refData = $this->collection->getRefData();
        ob_start();
        require_once($this->relativePathToTemplate);
        $toolsMenu = ob_get_contents();
        ob_end_clean();
        return $toolsMenu;
    }

    public function mimicShortcode() {
        if (is_string($this->request['shashinOrderBy'])) {
            $orderBy = $this->request['shashinOrderBy'];
        }

        else {
            $orderBy = $this->defaultOrderBy;
        }

        if (is_string($this->request['shashinReverse'])) {
            $reverse = $this->request['shashinReverse'];
        }

        else {
            $reverse = $this->defaultReverse;
        }

        $shortcodeMimic = array(
            'order' => $orderBy,
            'reverse' => $reverse
        );

        if ($this->album) {
            $shortcodeMimic['id'] = $this->album->id;
            $shortcodeMimic['type'] = 'albumphotos';
        }

        return $shortcodeMimic;
    }

    public function getDataObjects($shortcodeMimic) {
        $this->shortcode = $this->container->getShortcode($shortcodeMimic);
        $this->collection->setNoLimit(true);
        return $this->collection->getCollectionForShortcode($this->shortcode);
    }

    abstract public function generateOrderByLink($column, $columnLabel);

    public function checkOrderByNonce() {
        $nonceName = "shashinNonce_" . $this->request['shashinOrderBy'];
        return $this->functionsFacade->checkAdminNonceFields($nonceName);
    }

    public function setSortArrowAndOrderByUrl($column) {
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

        return "?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=$column&amp;shashinReverse=" . $reverse;
    }

    public function getSortArrow() {
        return $this->sortArrow;
    }

    protected function setOrderByNonce($column, $orderByUrl) {
        $nonceName = "shashinNonce_" . $column;
        return $this->functionsFacade->addNonceToUrl($orderByUrl, $nonceName);
    }
}
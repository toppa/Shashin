<?php

abstract class Admin_ShashinMenuDisplayer {
    protected $functionsFacade;
    protected $request;
    protected $defaultOrderBy;
    protected $defaultReverse = 'n';
    protected $shortcodeMimic = array();
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
    abstract public function setContainer(Public_ShashinContainer $container = null);

    public function run($message = null) {
        if ($this->request['shashinOrderBy']) {
            $this->checkOrderByNonce();
        }

        $this->collection->setLimitNeeded(false);
        $this->collection->setProperties($this->shortcodeMimic);
        $dataObjects = $this->collection->getCollection();
        $refData = $this->collection->getRefData();
        ob_start();
        require_once($this->relativePathToTemplate);
        $toolsMenu = ob_get_contents();
        ob_end_clean();
        return $toolsMenu;
    }

    public function setShortcodeMimic($orderBy = null, $reverse = null, $albumId = null) {
        if (!is_string($orderBy)) {
            $orderBy = $this->defaultOrderBy;
        }

        if (!is_string($reverse)) {
            $reverse = $this->defaultReverse;
        }

        $this->shortcodeMimic = array(
            'order' => $orderBy,
            'reverse' => $reverse
        );

        if ($albumId) {
            $this->shortcodeMimic['id'] = $albumId;
            $this->shortcodeMimic['type'] = 'albumphotos';
        }

        return $this->shortcodeMimic;
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
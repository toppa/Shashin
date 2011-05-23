<?php

abstract class ShashinMenuDisplayer {
    protected $functionsFacade;
    protected $requests;
    protected $defaultOrderBy;
    protected $orderBy;
    protected $defaultSort = 'asc';
    protected $sort;
    protected $sortArrow;
    protected $needToCheckOrderByNonce = false;
    protected $relativePathToTemplate;

    public function __construct(&$functionsFacade, &$requests) {
        $this->functionsFacade = $functionsFacade;
        $this->requests = $requests;
        $this->orderBy = $this->defaultOrderBy;
        $this->sort = $this->defaultSort;
    }

    abstract public function run($message = null);

    abstract public function generateOrderByLink($column, $columnLabel);

    public function checkOrderByNonce() {
        if ($this->needToCheckOrderByNonce === true) {
            $nonceName = "shashinNonce_" . $this->orderBy . "_" . $this->sort;
            $this->functionsFacade->checkAdminNonceFields($nonceName);
        }
    }

    public function setOrderByClause() {
        if ($this->requests['shashinOrderBy']) {
            $this->orderBy = $this->requests['shashinOrderBy'];
            $this->sort = $this->requests['shashinSort'];
            $this->needToCheckOrderByNonce = true;
        }

        $orderByClause = "order by " . $this->orderBy . " " . $this->sort;
        return $orderByClause;
    }

    protected function setSortAndOrderByUrl($column) {
        switch ($this->requests['shashinSort']) {
        case 'asc':
            $this->sort = 'desc';
            $this->sortArrow = ($this->requests['shashinOrderBy'] == $column) ? '&darr;' : '';
            break;
        case 'desc':
            $this->sort = 'asc';
            $this->sortArrow = ($this->requests['shashinOrderBy'] == $column) ? '&uarr;' : '';
            break;
        default:
            $this->sort = 'desc';
            $this->sortArrow = ($this->defaultOrderBy == $column) ? '&darr;' : '';
        }

        return "?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=$column&amp;shashinSort=" . $this->sort;
    }

    protected function setOrderByNonce($column, $orderByUrl) {
        $nonceName = "shashinNonce_" . $column . "_" . $this->sort;
        return $this->functionsFacade->addNonceToUrl($orderByUrl, $nonceName);
    }
}
<?php

require_once('ShashinMenuDisplayer.php');
require_once(dirname(__FILE__) . '/../Photo/ShashinPhotoDisplayerPicasa.php');
require_once(ToppaFunctions::path() . '/ToppaHtmlFormField.php');

class ShashinMenuDisplayerPhotos extends ShashinMenuDisplayer {
    private $photoRef;
    private $album;
    private $photos;
    private $photoDisplayer;

    public function __construct(&$functionsFacade, &$requests, &$photoRef, &$album, &$photoDisplayer) {
        $this->defaultOrderBy = 'userOrder';
        $this->relativePathToTemplate = 'Display/menuPhotos.php';
        $this->photoRef = $photoRef;
        $this->album = $album;
        $this->photoDisplayer = $photoDisplayer;
        parent::__construct($functionsFacade, $requests);
    }

    public function run($message = null) {
        $orderByClause = $this->setOrderByClause();
        $this->checkOrderByNonce();
        $this->photos = $this->album->getAlbumPhotos($orderByClause);
        ob_start();
        require_once($this->relativePathToTemplate);
        $toolsMenu = ob_get_contents();
        ob_end_clean();
        return $toolsMenu;
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortAndOrderByUrl($column);
        $orderByUrl .= "&amp;shashinMenu=photos&amp;albumKey=" . $this->album->albumKey;
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }
}
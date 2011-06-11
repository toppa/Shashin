<?php

class Admin_ShashinMenuDisplayerPhotos extends Admin_ShashinMenuDisplayer {
    private $clonablePhoto;
    private $album;
    private $photos;
    private $photoDisplayer;

    public function __construct(
      ToppaFunctionsFacade &$functionsFacade,
      array &$requests,
      Lib_ShashinPhoto &$clonablePhoto,
      Lib_ShashinAlbum &$album,
      Lib_ShashinPhotoDisplayerPicasa &$photoDisplayer) {
        $this->defaultOrderBy = 'userOrder';
        $this->relativePathToTemplate = 'Display/menuPhotos.php';
        $this->clonablePhoto = $clonablePhoto;
        $this->album = $album;
        $this->photoDisplayer = $photoDisplayer;
        parent::__construct($functionsFacade, $requests);
    }

    public function run($message = null) {
        $orderByClause = $this->setOrderByClause();
        $this->checkOrderByNonce();
        $this->photos = $this->album->getAlbumPhotos($orderByClause);
        $refData = $this->clonablePhoto->getRefData();
        ob_start();
        require_once($this->relativePathToTemplate);
        $toolsMenu = ob_get_contents();
        ob_end_clean();
        return $toolsMenu;
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortArrowAndOrderByUrl($column);
        $orderByUrl .= "&amp;shashinMenu=photos&amp;albumKey=" . $this->album->albumKey;
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }
}
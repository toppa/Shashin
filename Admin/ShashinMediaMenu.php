<?php

class Admin_ShashinMediaMenu {
    private $version;
    private $functionsFacade;
    private $request;
    private $container;

    public function __construct($version) {
        $this->version = $version;
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setContainer(Public_ShashinContainer $container = null) {
        $this->container = $container;
        return $this->container;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function displayPhotoMenu() {
        $this->functionsFacade->useHook('admin_print_styles', array($this, 'displayMediaMenuCss'));
        $this->functionsFacade->addToMediaMenu(array($this, 'buildPhotoMenu'));
    }

    public function displayAlbumMenu() {
        $this->functionsFacade->useHook('admin_print_styles', array($this, 'displayMediaMenuCss'));
        $this->functionsFacade->addToMediaMenu(array($this, 'buildAlbumMenu'));
    }

    public function displayMediaMenuCss() {
        $this->functionsFacade->prepMediaMenuCss('type=shashin');
        $cssUrl = $this->functionsFacade->getPluginsUrl('/Display/', __FILE__) .'media.css';
        $this->functionsFacade->enqueueStylesheet('shashinMediaMenuStyle', $cssUrl, false, $this->version);
    }

    public function buildPhotoMenu() {
        $this->buildMediaMenu('Display/mediaPhotos.php');
    }

    public function buildAlbumMenu() {
        $this->buildMediaMenu('Display/mediaAlbums.php');
    }

    private function buildMediaMenu($templatePath) {
        $this->functionsFacade->useFilter('media_upload_tabs', array($this, 'addMediaMenuTabs'));
        $this->functionsFacade->addMediaMenuHeader();
        $rawShortcode = array('type' => 'album', 'order' => 'date', 'reverse' => 'y');
        $shortcode = $this->container->getShortcode($rawShortcode);
        $albumCollection = $this->container->getClonableAlbumCollection();
        $albumCollection->setNoLimit(true);
        $albums = $albumCollection->getCollectionForShortcode($shortcode);
        $loaderUrl = $this->functionsFacade->getPluginsUrl('/Display/images/', __FILE__) .'loader.gif';
        require_once $templatePath;
    }

    public function addMediaMenuTabs() {
        return array(
            'shashin_photos' => __('Photos', 'shashin'),
            'shashin_albums' => __('Albums', 'shashin')
        );
    }

    public function getPhotosForMenu() {
        $arrayShortcode = $this->getArrayShortcode();
        $totalPages = $this->getTotalPages($arrayShortcode);
        $currentPage = $this->getCurrentPage();
        $collection = $this->getCollectionForCurrentPage($arrayShortcode, $currentPage);
        $photos = $this->getPhotosArrayFromCollection($collection);
        return json_encode(array(
            'photos' => $photos,
            'totalPages' => $totalPages,
            'page' => $currentPage
        ));
    }

    public function getArrayShortcode() {
        $arrayShortcode = array('limit' => 32);

        if ($this->request['shashinAlbumId'] && is_numeric($this->request['shashinAlbumId'])) {
            $arrayShortcode['id'] = $this->request['shashinAlbumId'];
            $arrayShortcode['type'] = 'albumphotos';
        }

        else {
            $arrayShortcode['type'] = 'photo';
        }

        if ($this->request['shashinOrder'] && is_string($this->request['shashinOrder'])) {
            $arrayShortcode['order'] = $this->request['shashinOrder'];
        }

        if ($this->request['shashinReverse'] && is_string($this->request['shashinReverse'])) {
            $arrayShortcode['reverse'] = $this->request['shashinReverse'];
        }

        return $arrayShortcode;
    }

    public function getTotalPages(array $arrayShortcode) {
        $shortcode = $this->container->getShortcode($arrayShortcode);
        $photoCollection = $this->container->getClonablePhotoCollection();
        $photoCount = $photoCollection->getCountForShortcode($shortcode);
        return ceil($photoCount / 32);
    }

    public function getCurrentPage() {
        if (isset($this->request['shashinPage']) && is_numeric($this->request['shashinPage'])) {
           return $this->request['shashinPage'];
        }

        return 1;
    }

    public function getCollectionForCurrentPage(array $arrayShortcode, $currentPage) {
        $arrayShortcode['limit'] = 32;
        $arrayShortcode['offset'] = ($currentPage - 1) * 32;
        $shortcode = $this->container->getShortcode($arrayShortcode);
        $photoCollection = $this->container->getClonablePhotoCollection();
        return $photoCollection->getCollectionForShortcode($shortcode);
    }

    public function getPhotosArrayFromCollection($collection) {
        $photos = array();

        foreach ($collection as $photo) {
            $photos[] = array(
                'id' => $photo->id,
                'description' => $photo->description,
                'contentUrl' => $photo->contentUrl,
            );
        }

        return $photos;
    }
}

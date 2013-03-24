<?php

class Admin_ShashinMediaMenuWp {
    private $version;
    private $request;
    private $container;

    public function __construct($version) {
        $this->version = $version;
    }

    public function setContainer(Public_ShashinContainer $container = null) {
        $this->container = $container;
        return $this->container;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function initPhotoMenu() {
        add_action('admin_print_styles-media-upload-popup', array($this, 'displayMediaMenuCss'));
        return wp_iframe(array($this, 'mediaDisplayPhotoMenu'));
    }

    public function initAlbumMenu() {
        add_action('admin_print_styles-media-upload-popup', array($this, 'displayMediaMenuCss'));
        return wp_iframe(array($this, 'mediaDisplayAlbumMenu'));
    }

    public function displayMediaMenuCss() {
        $cssUrl = plugins_url('/display/', __FILE__) .'media.css';
        wp_enqueue_style('shashinMediaMenuStyle', $cssUrl, false, $this->version);
    }

    // WP requires the function name starts with "media" to load the necessary media menu CSS file!
    public function mediaDisplayPhotoMenu() {
        $this->displayMediaMenu('display/mediaPhotos.php');
    }

    // WP requires the function name starts with "media" to load the necessary media menu CSS file!
    public function mediaDisplayAlbumMenu() {
        $this->displayMediaMenu('display/mediaAlbums.php');
    }

    private function displayMediaMenu($templatePath) {
        media_upload_header();
        $rawShortcode = array('type' => 'album', 'order' => 'date', 'reverse' => 'y');
        $shortcode = $this->container->getShortcode($rawShortcode);
        $albumCollection = $this->container->getClonableAlbumCollection();
        $albumCollection->setNoLimit(true);
        $albums = $albumCollection->getCollectionForShortcode($shortcode);
        $loaderUrl = plugins_url('/display/images/', __FILE__) .'loader.gif';
        require_once $templatePath;
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

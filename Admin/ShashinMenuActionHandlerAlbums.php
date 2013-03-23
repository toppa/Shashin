<?php

class Admin_ShashinMenuActionHandlerAlbums {
    private $functionsFacade;
    private $upgrader;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct() {
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setUpgrader(Admin_ShashinUpgradeWp $upgrader) {
        $this->upgrader = $upgrader;
        return $this->upgrader;
    }

    public function setMenuDisplayer(Admin_ShashinMenuDisplayer $menuDisplayer) {
        $this->menuDisplayer = $menuDisplayer;
        return $this->menuDisplayer;
    }

    public function setAdminContainer(Admin_ShashinContainer $adminContainer) {
        $this->adminContainer = $adminContainer;
        return $this->adminContainer;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function run() {
        if (!array_key_exists('shashinAction', $this->request)) {
            return $this->menuDisplayer->run();
        }

        switch ($this->request['shashinAction']) {
            case 'addAlbums':
                $nonceToCheck = 'shashinNonceAdd' . ucfirst($this->request['shashinAlbumType']);
                $this->functionsFacade->checkAdminNonceFields($nonceToCheck, $nonceToCheck);
                $message = $this->runSynchronizerBasedOnUrl();
                break;
            case 'syncAlbum':
                $this->functionsFacade->checkAdminNonceFields('shashinNonceSync_' . $this->request['id']);
                $message = $this->runSynchronizerForExistingAlbum();
                break;
            case 'syncAllAlbums':
                $this->functionsFacade->checkAdminNonceFields('shashinNonceSyncAll');
                $message = $this->runSynchronizerForAllExistingAlbums();
                break;
            case 'deleteAlbum':
                $this->functionsFacade->checkAdminNonceFields('shashinNonceDelete_' . $this->request['id']);
                $message = $this->runDeleteAlbum();
                break;
            case 'updateIncludeInRandom':
                $this->functionsFacade->checkAdminNonceFields('shashinNonceUpdate', 'shashinNonceUpdate');
                $message = $this->runUpdateIncludeInRandom();
                break;
            case 'cleanupUpgrade':
                $this->functionsFacade->checkAdminNonceFields('shashinNonceCleanupUpgrade');
                $this->upgrader->cleanup();
                $message = __('Upgrade cleanup completed', 'shashin');
        }

        return $this->menuDisplayer->run($message);
    }

    public function runSynchronizerBasedOnUrl() {
        $synchronizer = $this->adminContainer->getSynchronizer($this->request);
        return $synchronizer->syncUserRequest();
    }

    public function runSynchronizerForExistingAlbum(Lib_ShashinAlbum $albumToSync = null) {
        if (!$albumToSync) {
            $albumToSync = $this->adminContainer->getClonableAlbum();
            $albumToSync->get($this->request['id']);
        }

        $synchronizer = $this->adminContainer->getSynchronizer(array('shashinAlbumType' => $albumToSync->albumType));
        $syncedAlbum = $synchronizer->syncExistingAlbum($albumToSync);
        return __('Synchronized', 'shashin') . ' "' . $syncedAlbum->title . '"';
    }

    public function runSynchronizerForAllExistingAlbums() {
        $albumCollection = $this->adminContainer->getClonableAlbumCollection();
        $albumsToSync = $albumCollection->getCollection();

        $albumCount = 0;
        foreach ($albumsToSync as $album) {
            $this->runSynchronizerForExistingAlbum($album);
            ++$albumCount;
        }

        return __('Synchronized all', 'shashin') . " $albumCount " . __('albums', 'shashin');
    }

    public function runDeleteAlbum() {
        $album = $this->adminContainer->getClonableAlbum();
        $album->get($this->request['id']);
        $albumData = $album->delete();
        return __('Deleted album', 'shashin') . ' "' . $albumData['title'] . '"';
    }

    public function runUpdateIncludeInRandom() {
        $albumsShortcodeMimic = array('type' => 'album', 'order' => 'title');
        $albums = Admin_ShashinContainer::getDataObjectCollection($albumsShortcodeMimic);

        foreach ($albums as $album) {
            if ($album->includeInRandom == $this->request['includeInRandom'][$album->id]) {
                continue;
            }

            $includeInRandom = array('includeInRandom'=> $this->request['includeInRandom'][$album->id]);
            $album->set($includeInRandom);
            $album->flush();

            $albumPhotosShortcodeMimic = array('id' => $album->id, 'type' => 'albumphotos');
            $albumPhotos = Admin_ShashinContainer::getDataObjectCollection($albumPhotosShortcodeMimic);

            foreach ($albumPhotos as $photo) {
                $photo->set($includeInRandom);
                $photo->flush();
            }
        }

        return __('Updated "Include In Random" settings', 'shashin');
    }
 }

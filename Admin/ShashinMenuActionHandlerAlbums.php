<?php

class Admin_ShashinMenuActionHandlerAlbums {
    private $functionsFacade;
    private $upgrader;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct() {
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
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
                $this->functionsFacade->checkAdminNonceFields('shashinNonceAdd', 'shashinNonceAdd');
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

    // user albums: https://plus.google.com/100291303544453276374/photos
    // an album's photos: https://plus.google.com/photos/100291303544453276374/albums/5725071897625277617
    public function runSynchronizerBasedOnUrl() {
        // if a non-RSS url, convert to the appropriate RSS url

        // Google Plus - an individual album
        if (strpos($this->request['userUrl'], 'plus.google.com/photos') !== false) {

        }

        // all of a Picasa user's albums
        if (strpos($this->request['userUrl'], 'kind=album') !== false) {
            $synchronizer = $this->adminContainer->getSynchronizerPicasa($this->request);
            $albumCount = $synchronizer->addMultipleAlbumsFromRssUrl();
            return __('Added', 'shashin') . " $albumCount " . __('Picasa albums', 'shashin');
        }

        // a single Picasa album
        elseif (strpos($this->request['userUrl'], 'kind=photo') !== false) {
            $synchronizer = $this->adminContainer->getSynchronizerPicasa($this->request);
            $syncedAlbum = $synchronizer->addSingleAlbumFromRssUrl();
            return __('Added Picasa album', 'shashin') . ' "' . $syncedAlbum->title . '"';
        }

        // a YouTube feed
        elseif (strpos($this->request['userUrl'], 'gdata.youtube.com') !== false) {
            $synchronizer = $this->adminContainer->getSynchronizerYoutube($this->request);
            $syncedAlbum = $synchronizer->addSingleAlbumFromRssUrl();
            return __('Added YouTube videos', 'shashin') . ' "' . $syncedAlbum->title . '"';
        }

        // a Twitpic feed
        elseif (strpos($this->request['userUrl'], 'twitpic.com/photos') !== false) {
            $synchronizer = $this->adminContainer->getSynchronizerTwitpic($this->request);
            $syncedAlbum = $synchronizer->addSingleAlbumFromRssUrl();
            return __('Added Twitpic photos', 'shashin') . ' "' . $syncedAlbum->title . '"';
        }

        else {
            throw new Exception(__('Unrecognized RSS feed', 'shashin'));
        }
    }

    public function runSynchronizerForExistingAlbum(Lib_ShashinAlbum $albumToSync = null) {
        if (!$albumToSync) {
            $albumToSync = $this->adminContainer->getClonableAlbum();
            $albumToSync->get($this->request['id']);
        }

        $synchronizerToGet = 'getSynchronizer' . ucfirst($albumToSync->albumType);
        $synchronizer = $this->adminContainer->$synchronizerToGet();
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
        $albums = $this->menuDisplayer->getDataObjects($albumsShortcodeMimic);

        foreach ($albums as $album) {
            if ($album->includeInRandom == $this->request['includeInRandom'][$album->id]) {
                continue;
            }

            $includeInRandom = array('includeInRandom'=> $this->request['includeInRandom'][$album->id]);
            $album->set($includeInRandom);
            $album->flush();

            /* I want to set this for every photo in the album as well, but this isn't
             working - commenting out for now
            $albumPhotosShortcodeMimic = array('id' => $album->id, 'type' => 'albumphotos');
            $albumPhotos = $this->menuDisplayer->getDataObjects($albumPhotosShortcodeMimic);

            foreach ($albumPhotos as $photo) {
                $photo->set($includeInRandom);
                $photo->flush();
            }
            */
        }

        return __('Updated "Include In Random" settings', 'shashin');
    }
 }

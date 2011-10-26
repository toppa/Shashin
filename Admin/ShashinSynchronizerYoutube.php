<?php

class Admin_ShashinSynchronizerYoutube extends Admin_ShashinSynchronizer {
    public function __construct() {
        parent::__construct();
    }

    public function deriveJsonUrl() {
        $this->setJsonUrl($this->rssUrl . '?alt=json');
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData['feed'], $albumRefData, 'youtube');
        $albumData['dataUrl'] = $albumData['dataUrl'] . '?alt=json';
        $albumData['pubDate'] = strtotime($albumData['pubDate']);
        $albumData['lastSync'] = $this->syncTime;
        $albumData['albumType'] = 'youtube';
        $albumData['width'] = 123;
        $albumData['height'] = 63;

        if ($this->includeInRandom) {
            $albumData['includeInRandom'] = $this->includeInRandom;
        }

        $this->album->set($albumData);
        $this->album->flush();
        $this->syncAlbumPhotos($decodedAlbumData);
        $this->deleteOldPhotos();
        return $this->album;
    }

    // degenerate
    public function syncMultipleAlbumsForThisAlbumType(array $decodedMultipleAlbumsData) {
        return null;
    }

    public function syncAlbumPhotos(array $decodedAlbumData, $sourceOrder = 0) {
        $photoRefData = $this->clonablePhoto->getRefData();

        // don't try to process empty albums
        if (is_array($decodedAlbumData['feed']['entry'])) {
            foreach ($decodedAlbumData['feed']['entry'] as $entry) {
                $photoData = $this->extractFieldsFromDecodedData($entry, $photoRefData, 'youtube');
                $photoData['albumId'] = $this->album->id;
                $photoData['albumType'] = $this->album->albumType;
                $photoData['takenTimestamp'] = strtotime($photoData['takenTimestamp']);
                $photoData['uploadedTimestamp'] = strtotime($photoData['uploadedTimestamp']);
                $photoData['sourceOrder'] = ++$sourceOrder;
                $photoData['filename'] = 'youtubeWontTellTheFilename.mpg';
                $photoData['lastSync'] = $this->syncTime;
                $photo = clone $this->clonablePhoto;
                $photo->set($photoData);
                $photo->flush();
            }
        }

        return $sourceOrder;
    }

    // degenerate
    public function getHighestResolutionVideoIfNeeded(array $entry, array $photoRefData) {
        return null;
    }
}


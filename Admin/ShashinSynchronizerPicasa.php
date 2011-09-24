<?php

class Admin_ShashinSynchronizerPicasa extends Admin_ShashinSynchronizer {
    public function __construct() {
        parent::__construct();
    }

    public function deriveJsonUrl() {
        $jsonUrl = str_ireplace('alt=rss', 'alt=json', $this->rssUrl);
        $jsonUrl = str_ireplace('feed/base/user', 'feed/api/user', $jsonUrl);
        $this->setJsonUrl($jsonUrl);
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData['feed'], $albumRefData, 'picasa');
        $albumData['pubDate'] = ToppaFunctions::makeTimestampPhpSafe($albumData['pubDate']);
        $albumData['lastSync'] = $this->syncTime;
        $albumData['albumType'] = 'picasa';

        if ($this->includeInRandom) {
            $albumData['includeInRandom'] = $this->includeInRandom;
        }

        $this->album->set($albumData);
        $this->album->flush();
        $this->syncAlbumPhotos($decodedAlbumData);
        $this->deleteOldPhotos();
        return $this->album;
    }

    public function syncMultipleAlbumsForThisAlbumType(array $decodedMultipleAlbumsData) {
        $albumCount = 0;

        foreach ($decodedMultipleAlbumsData['feed']['entry'] as $entry) {
            $this->jsonUrl = $entry['link'][0]['href'];
            $this->syncAlbum();
            ++$albumCount;
        }

        return $albumCount;
    }

    public function syncAlbumPhotos(array $decodedAlbumData) {
        $photoRefData = $this->clonablePhoto->getRefData();

        // the order photos appear in the feed reflects the user's preferred order
        $sourceOrder = 0;

        // don't try to process empty albums
        if (is_array($decodedAlbumData['feed']['entry'])) {
            foreach ($decodedAlbumData['feed']['entry'] as $entry) {
                $photoData = $this->extractFieldsFromDecodedData($entry, $photoRefData, 'picasa');
                $photoData['albumId'] = $this->album->id;
                $photoData['albumType'] = $this->album->albumType;
                $photoData['takenTimestamp'] = ToppaFunctions::makeTimestampPhpSafe($photoData['takenTimestamp']);
                $photoData['uploadedTimestamp'] = strtotime($photoData['uploadedTimestamp']);
                $photoData['sourceOrder'] = ++$sourceOrder;
                $photoData['lastSync'] = $this->syncTime;
                $photo = clone $this->clonablePhoto;
                $photo->set($photoData);
                $photo->flush();
            }
        }

        return $sourceOrder;
    }
}
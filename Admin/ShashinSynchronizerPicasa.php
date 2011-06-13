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
        $albumData['lastSync'] = time();
        $albumData['albumType'] = 'picasa';

        if ($this->includeInRandom) {
            $albumData['includeInRandom'] = $this->includeInRandom;
        }

        $this->album->set($albumData);
        $this->album->flush();
        $this->syncAlbumPhotos($decodedAlbumData);
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
        $userPhotoOrder = 0;

        foreach ($decodedAlbumData['feed']['entry'] as $entry) {
            $photoData = $this->extractFieldsFromDecodedData($entry, $photoRefData, 'picasa');
            $photoData['albumKey'] = $this->album->albumKey;
            $photoData['takenTimestamp'] = ToppaFunctions::makeTimestampPhpSafe($photoData['takenTimestamp']);
            $photoData['uploadedTimestamp'] = strtotime($photoData['uploadedTimestamp']);
            $photoData['userOrder'] = ++$userPhotoOrder;
            $photoData['lastSync'] = time();
            $photo = clone $this->clonablePhoto;
            $photo->set($photoData);
            $photo->flush();
        }

        return $userPhotoOrder;
    }

}
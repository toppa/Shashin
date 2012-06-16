<?php

class Admin_ShashinSynchronizerPicasa extends Admin_ShashinSynchronizer {
    public function __construct() {
        parent::__construct();
    }

    public function syncUserRequest() {
        $jsonUrl = $this->setJsonUrlFromUserUrl();
        $this->setJsonUrl($jsonUrl);

        if (strpos($this->jsonUrl, 'kind=photo')) {
            $album = $this->syncAlbum();
            $message =  __('Added Picasa album', 'shashin') . ' "' . $album->title . '"';
        }

        else {
            $albumCount = $this->syncMultipleAlbums();
            $message = __('Added', 'shashin') . " $albumCount " . __('Google+ albums', 'shashin');

        }

        return $message;
    }

    public function setJsonUrlFromUserUrl() {
        // Google Plus - an individual album
        // https://plus.google.com/photos/100291303544453276374/albums/5725071897625277617
        if (preg_match('#^https://plus\.google\.com/photos/(\d+)/albums/(\d+)#', $this->request['userUrl'], $matches) == 1) {
            $jsonUrl = 'https://picasaweb.google.com/data/feed/api/user/'
                . $matches[1]
                . '/albumid/'
                . $matches[2]
                . '?alt=json&kind=photo';
        }

        // Google Plus - all of a user's albums
        // https://plus.google.com/100291303544453276374/photos
        else if (preg_match('#^https://plus.google.com/photos/(\d+)#', $this->request['userUrl'], $matches) == 1) {
            $jsonUrl = 'https://picasaweb.google.com/data/feed/api/user/'
                . $matches[1]
                . '?alt=json&kind=album';
        }

        // Picasa - an individual album
        elseif (preg_match('#^https://picasaweb\..+/\w+/\w+#', $this->request['userUrl'], $matches) == 1) {
            $rssUrl = $this->retrievePicasaRssUrl();
            $jsonUrl = str_replace('/base/', '/api/', $rssUrl);
            $jsonUrl = str_replace('alt=rss', 'alt=json', $jsonUrl);
        }

        // Picasa - all of a user's albums
        else if (preg_match('#^(https://picasaweb\..+)/(\w+)#', $this->request['userUrl'], $matches) == 1) {
            $jsonUrl = $matches[1]
                . '/data/feed/api/user/'
                . $matches[2]
                . '?alt=json&kind=album';
        }

        else {
            throw New Exception (__('Unrecognized URL: ', 'shashin') . htmlentities($this->request['userUrl']));
        }

        return $this->setJsonUrl($jsonUrl);
    }

    public function retrievePicasaRssUrl() {
        $rssUrl = null;
        $response = $this->httpRequester->request($this->request['userUrl'], array('timeout' => 30, 'sslverify' => false));
        $doc = new DOMDocument();
        @$doc->loadHTML($response['body']);
        $links = $doc->getElementsByTagName('link');

        for ($i = 0; $i < $links->length; $i++) {
            $link = $links->item($i);
            if ($link->getAttribute('rel') == 'alternate' && $link->getAttribute('type') == 'application/rss+xml') {
                $rssUrl = $link->getAttribute('href');
            }
        }

        if (!$rssUrl) {
            throw New Exception(__('Unable to determine RSS feed for URL', 'shashin') . ': ' . htmlentities($this->request['userUrl']));
        }

        return $rssUrl;
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData['feed'], $albumRefData, 'picasa');
        $albumData['pubDate'] = ToppaFunctions::makeTimestampPhpSafe($albumData['pubDate']);
        $albumData['lastSync'] = $this->syncTime;
        $albumData['albumType'] = $this->album->albumType;

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

    public function syncAlbumPhotos(array $decodedAlbumData, $sourceOrder = 0) {
        $photoRefData = $this->clonablePhoto->getRefData();

        // don't try to process empty albums
        if (is_array($decodedAlbumData['feed']['entry'])) {
            foreach ($decodedAlbumData['feed']['entry'] as $entry) {
                $photoRefData = $this->getHighestResolutionVideoIfNeeded($entry, $photoRefData);
                $photoData = $this->extractFieldsFromDecodedData($entry, $photoRefData, 'picasa');
                $photoData['albumId'] = $this->album->id;
                $photoData['albumType'] = $this->album->albumType;
                if (isset($photoData['takenTimestamp'])) {
                    $photoData['takenTimestamp'] = ToppaFunctions::makeTimestampPhpSafe($photoData['takenTimestamp']);
                }
                $photoData['uploadedTimestamp'] = strtotime($photoData['uploadedTimestamp']);
                $photoData['sourceOrder'] = ++$sourceOrder;
                $photoData['lastSync'] = $this->syncTime;
                // at least one camera type can put a comma in the exposure, so save as a string
                if (isset($photoData['exposure'])) {
                    $photoData['exposure'] = strval(round($photoData['exposure'], 3));
                }
                $photo = clone $this->clonablePhoto;
                $photo->set($photoData);
                $photo->flush();
            }
        }

        return $sourceOrder;
    }

    public function getHighestResolutionVideoIfNeeded(array $entry, array $photoRefData) {
        if (!is_array($entry['media$group']['media$content'])) {
            return $photoRefData;
        }

        $highestWidth = 0;

        for ($i = 0; $i < count($entry['media$group']['media$content']); $i++) {
            if ($entry['media$group']['media$content'][$i]['medium'] == 'video'
              && $entry['media$group']['media$content'][$i]['width'] > $highestWidth) {
                $highestWidth = $entry['media$group']['media$content'][$i]['width'];
                $photoRefData['videoUrl']['picasa'][2] = $i;
                $photoRefData['videoType']['picasa'][2] = $i;
                $photoRefData['videoWidth']['picasa'][2] = $i;
                $photoRefData['videoHeight']['picasa'][2] = $i;
            }
        }

        return $photoRefData;
    }
}


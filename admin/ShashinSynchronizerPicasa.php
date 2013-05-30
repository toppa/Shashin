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
        // https://plus.google.com/u/0/photos/111831862730326767526/albums/5758674999667643409
        if (preg_match('#^https://plus\.google\.com/.*photos/(\d+)/albums/(\d+)(.*)#', $this->request['userUrl'], $matches) == 1) {
            $jsonUrl = 'https://picasaweb.google.com/data/feed/api/user/'
                . $matches[1]
                . '/albumid/'
                . $matches[2]
                . '?alt=json&kind=photo';
            $jsonUrl .= $this->addGooglePlusAuthKeyIfNeeded($matches[3]);
        }

        // Google Plus - all of a user's albums
        // https://plus.google.com/100291303544453276374/photos
        // https://plus.google.com/u/0/photos/111831862730326767526/albums
        else if (preg_match('#^https://plus.google.com/.*photos/(\d+)(.*)#', $this->request['userUrl'], $matches) == 1) {
            $jsonUrl = 'https://picasaweb.google.com/data/feed/api/user/'
                . $matches[1]
                . '?alt=json&kind=album';
        }

        // Picasa - an individual album
        elseif (preg_match('#^https://picasaweb\..+/.+/(.+)#', $this->request['userUrl'], $matches) == 1) {
            $rssUrl = $this->retrievePicasaRssUrl();
            $jsonUrl = str_replace('/base/', '/api/', $rssUrl);
            $jsonUrl = str_replace('alt=rss', 'alt=json', $jsonUrl);
            $jsonUrl .= $this->addPicasaAuthKeyIfNeeded($matches[1]);
        }

        // Picasa - all of a user's albums
        else if (preg_match('#^(https://picasaweb\..+)/(.+)#', $this->request['userUrl'], $matches) == 1) {
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
        $doc = $this->getBodyOfPageForUserUrl();
        $links = $doc->getElementsByTagName('link');

        for ($i = 0; $i < $links->length; $i++) {
            $link = $links->item($i);
            if ($link->getAttribute('rel') == 'alternate' && $link->getAttribute('type') == 'application/rss+xml') {
                $rssUrl = $link->getAttribute('href');
                break;
            }
        }

        if (!$rssUrl) {
            throw New Exception(__('Unable to determine RSS feed for URL', 'shashin') . ': ' . htmlentities($this->request['userUrl']));
        }

        return $rssUrl;
    }

    private function getBodyOfPageForUserUrl() {
        if (!class_exists('DOMDocument')) {
            throw New Exception(__('Your installation of PHP has been configured without DOM support. DOM support is required to sync albums using Picasa URLs. Try synchronizing with the Google+ URL.', 'shashin'));
        }

        $response = $this->httpRequester->request(
            $this->request['userUrl'],
            array('timeout' => 30, 'sslverify' => false)
        );
        $doc = new DOMDocument();
        @$doc->loadHTML($response['body']);
        return $doc;
    }

    public function addGooglePlusAuthKeyIfNeeded($urlMatch = null) {
        if (!isset($urlMatch)) {
            return null;
        }

        if (preg_match('/(authkey=)([\w-]+)/', $urlMatch, $authKeyMatches) != 1) {
            return null;
        }

        // Yes, seriously.
        return '&' . $authKeyMatches[1] . 'Gv1sRg' . $authKeyMatches[2];
    }

    public function addPicasaAuthKeyIfNeeded($urlMatch = null) {
        if (!isset($urlMatch)) {
            return null;
        }

        if (preg_match('/(authkey=[\w-]+)/', $urlMatch, $authKeyMatches) != 1) {
            return null;
        }

        return '&' . $authKeyMatches[1];
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData['feed'], $albumRefData, 'picasa');
        $albumData['pubDate'] = Lib_ShashinFunctions::makeTimestampPhpSafe($albumData['pubDate']);
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
                    $photoData['takenTimestamp'] = Lib_ShashinFunctions::makeTimestampPhpSafe($photoData['takenTimestamp']);
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

    /*
     * @todo: add a setting that allows for setting the maximum desired resolution
     */
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


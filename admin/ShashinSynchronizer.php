<?php

abstract class Admin_ShashinSynchronizer {
    protected $request;
    protected $httpRequester;
    protected $clonableAlbum;
    protected $clonablePhoto;
    protected $dbFacade;
    protected $album;
    protected $jsonUrl;
    protected $includeInRandom;
    protected $syncTime;

    public function __construct() {
    }

    public function setRequest(array $request) {
        $this->request = $request;
    }

    public function setHttpRequester($httpRequester) {
        $this->httpRequester = $httpRequester;
    }

    public function setClonableAlbum(Lib_ShashinAlbum $clonableAlbum) {
        $this->clonableAlbum = $clonableAlbum;
    }

    public function setClonablePhoto(Lib_ShashinPhoto $clonablePhoto) {
        $this->clonablePhoto = $clonablePhoto;
    }

    public function setDatabaseFacade(Lib_ShashinDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
    }

    abstract public function syncUserRequest();
    abstract public function setJsonUrlFromUserUrl();

    public function setJsonUrl($url) {
        $this->jsonUrl = $url;
        return $this->jsonUrl;
    }

    public function getJsonUrl() {
        return $this->jsonUrl;
    }


    public function setIncludeInRandom($includeInRandom) {
        $this->includeInRandom = htmlentities($includeInRandom);
    }

    public function syncExistingAlbum(Lib_ShashinAlbum $album) {
        $this->setJsonUrl($album->dataUrl);
        $syncedAlbum = $this->syncAlbum();
        return $syncedAlbum;
    }

    public function syncAlbum() {
        $decodedAlbumData = $this->getDataForSync();
        $this->syncTime = time();
        return $this->syncAlbumForThisAlbumType($decodedAlbumData);
    }

    public function syncMultipleAlbums() {
        $decodedMultipleAlbumsData = $this->getDataForSync();
        $this->syncTime = time();
        $albumCount = $this->syncMultipleAlbumsForThisAlbumType($decodedMultipleAlbumsData);
        return $albumCount;
    }

    private function getDataForSync() {
        $response = $this->httpRequester->request($this->jsonUrl, array('timeout' => 30, 'sslverify' => false));
        return $this->checkResponseAndDecodeAlbumData($response);
    }

    public function checkResponseAndDecodeAlbumData($response) {
        // unfortunately there's no hiding from being WP specific here
        if (is_a($response, 'WP_Error')) {
            throw new Exception(
                __('Failed to retrieve album feed at ', 'shashin')
                . $this->jsonUrl
                . '<br />'
                . __('WP_Http Error: ', 'shashin')
                . $response->get_error_message()
            );
        }

        elseif (is_array($response) && $response['response']['code'] != 200) {
            throw new Exception(
                __("Failed to retrieve album feed at ")
                . $this->jsonUrl
                . '<br />'
                . __('Error Response: ', 'shashin')
                . $response['response']['code'] . " "
                . $response['response']['message']
            );
        }

        $decodedAlbumData = json_decode($response['body'], true);

        if (!is_array($decodedAlbumData)) {
            throw new Exception(__("Failed to parse album feed at ", "shashin") . $this->jsonUrl);
        }

        return $decodedAlbumData;
    }

    abstract public function syncAlbumForThisAlbumType(array $extractedFields);
    abstract public function syncMultipleAlbumsForThisAlbumType(array $decodedMultipleAlbumsData);

    public function extractFieldsFromDecodedData(array $decodedData, array $refData, $albumType) {
        $extractedFields = array();

        foreach ($refData as $k=>$v) {
            if (!isset($v[$albumType])) {
                continue;
            }

            switch(count($v[$albumType])) {
                case 0:
                    break;
                case 1:
                    if (isset($decodedData
                        [$v[$albumType][0]])) {

                        $extractedFields[$k] = $decodedData
                            [$v[$albumType][0]];
                    }
                    break;
                case 2:
                    if (isset($decodedData
                        [$v[$albumType][0]]
                        [$v[$albumType][1]])) {

                        $extractedFields[$k] = $decodedData
                            [$v[$albumType][0]]
                            [$v[$albumType][1]];
                    }
                    break;
                case 3:
                    if (isset($decodedData
                        [$v[$albumType][0]]
                        [$v[$albumType][1]]
                        [$v[$albumType][2]])) {

                        $extractedFields[$k] = $decodedData
                            [$v[$albumType][0]]
                            [$v[$albumType][1]]
                            [$v[$albumType][2]];
                    }
                    break;
                case 4:
                    if (isset($decodedData
                        [$v[$albumType][0]]
                        [$v[$albumType][1]]
                        [$v[$albumType][2]]
                        [$v[$albumType][3]])) {

                        $extractedFields[$k] = $decodedData
                            [$v[$albumType][0]]
                            [$v[$albumType][1]]
                            [$v[$albumType][2]]
                            [$v[$albumType][3]];
                    }
                    break;
                default:
                    throw new Exception(__("Unexpected number of fields in feed", "shashin"));
            }
        }
        return $extractedFields;
    }

    abstract public function syncAlbumPhotos(array $decodedAlbumData, $sourceOrder = 0);

    public function deleteOldPhotos() {
        // before deleting anything, make sure the number of photos we synchronized matches
        // the number of photos that are supposed to be in the album, to confirm syncing of
        // the album photos was successful
        $confirmSql = 'select count(1) from ' . $this->clonablePhoto->getTableName()
            . ' where albumId = ' . $this->album->id
            . ' and lastSync = ' . $this->syncTime;

        if ($this->dbFacade->executeQuery($confirmSql, 'get_var') == $this->album->photoCount) {
            $sql = 'delete from ' . $this->clonablePhoto->getTableName()
                . ' where albumId = ' . $this->album->id
                . ' and lastSync < ' . $this->syncTime;
            return $this->dbFacade->executeQuery($sql);
        }

        return 0;
    }

    abstract public function getHighestResolutionVideoIfNeeded(array $entry, array $photoRefData);
}

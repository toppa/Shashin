<?php

class Public_ShashinContainer extends Lib_ShashinContainer {
    private $headTags;
    private $sessionManager;

    public function __construct() {
    }

    public function getShortcode(array $arrayShortcode) {
        $this->getSettings();
        $shortcode = new Public_ShashinShortcode($arrayShortcode);
        $shortcode->setSettings($this->settings);
        $shortcode->cleanAndValidate();
        return $shortcode;
    }

    public function getSessionManager() {
        if (!$this->sessionManager) {
            $this->sessionManager = new Public_ShashinSessionManager();
        }

        return $this->sessionManager;
    }

    public function getLayoutManager(
      Public_ShashinShortcode $shortcode,
      Lib_ShashinDataObjectCollection $dataObjectCollection,
      array $request) {

        $this->getSettings();
        $this->getFunctionsFacade();
        $this->getSessionManager();
        $layoutManager = new Public_ShashinLayoutManager();
        $layoutManager->setSettings($this->settings);
        $layoutManager->setFunctionsFacade($this->functionsFacade);
        $layoutManager->setContainer($this);
        $layoutManager->setShortcode($shortcode);
        $layoutManager->setDataObjectCollection($dataObjectCollection);
        $layoutManager->setRequest($request);
        $layoutManager->setSessionManager($this->sessionManager);
        return $layoutManager;
    }

    public function getHeadTags($version) {
        if (!$this->headTags) {
            $this->getFunctionsFacade();
            $this->getSettings();
            $this->headTags = new Public_ShashinHeadTags($version);
            $this->headTags->setFunctionsFacade($this->functionsFacade);
            $this->headTags->setSettings($this->settings);
        }
        return $this->headTags;
    }

    public function getDataObjectDisplayer(
      Public_ShashinShortcode $shortcode,
      Lib_ShashinDataObject $dataObject,
      Lib_ShashinDataObject $alternativeThumbnail = null,
      $forceViewer = null,
      $albumIdForAjaxPhotoDisplay = null) {

        $this->getFunctionsFacade();
        $this->getSettings();
        $this->getSessionManager();
        $dataObjectClassName = get_class($dataObject);
        $dataObjectClassName = str_replace('Lib_', 'Public_', $dataObjectClassName);
        $albumType = ucfirst($dataObject->albumType);
        $classToCall = $dataObjectClassName . 'Displayer' . $albumType;

        if (strpos($dataObjectClassName, 'ShashinPhoto') !== false) {
            if (is_string($forceViewer)) {
                $viewerName = ucfirst($forceViewer);
            }

            else {
                $viewerName = ucfirst($this->settings->imageDisplay);
            }

            $classToCall .= $viewerName;
        }

        $dataObjectDisplayer = new $classToCall();
        $dataObjectDisplayer->setSettings($this->settings);
        $dataObjectDisplayer->setShortcode($shortcode);
        $dataObjectDisplayer->setFunctionsFacade($this->functionsFacade);
        $dataObjectDisplayer->setDataObject($dataObject);
        $dataObjectDisplayer->setThumbnail($alternativeThumbnail);
        $dataObjectDisplayer->setSessionManager($this->sessionManager);
        $dataObjectDisplayer->setAlbumIdForAjaxPhotoDisplay($albumIdForAjaxPhotoDisplay);
        return $dataObjectDisplayer;
    }

    public function getOldShortcode($content, array $request) {
        $oldShortcode = new Public_ShashinOldShortcode();
        $oldShortcode->setContent($content);
        $oldShortcode->setContainer($this);
        $oldShortcode->setRequest($request);
        return $oldShortcode;
    }

}

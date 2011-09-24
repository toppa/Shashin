<?php

class Public_ShashinContainer extends Lib_ShashinContainer {
    private $headTagsBuilder;
    private $sessionManager;

    public function __construct($autoLoader) {
        parent::__construct($autoLoader);
    }

    public function getShortcode(array $rawShortcode) {
        $this->getSettings();
        $shortcode = new Public_ShashinShortcode($rawShortcode);
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

    public function getDocHeadUrlsFetcher() {
        if (!$this->headTagsBuilder) {
            $this->getFunctionsFacade();
            $this->headTagsBuilder = new Public_ShashinDocHeadUrlsFetcher($this->functionsFacade);
        }

        return $this->headTagsBuilder;
    }

    public function getDataObjectDisplayer(
      Public_ShashinShortcode $shortcode,
      Lib_ShashinDataObject $dataObject,
      Lib_ShashinDataObject $alternativeThumbnail = null,
      $forceViewer = null) {

        $this->getFunctionsFacade();
        $this->getSettings();
        $this->getSessionManager();
        $dataObjectClassName = get_class($dataObject);
        $dataObjectClassName = str_replace('Lib_', 'Public_', $dataObjectClassName);
        $albumType = ucfirst($dataObject->albumType);

        if (is_string($forceViewer)) {
            $viewerName = ucfirst($forceViewer);
        }

        else {
            $viewerName = ucfirst($this->settings->imageDisplay);
        }

        $classToCall = $dataObjectClassName . 'Displayer' . $albumType . $viewerName;
        $dataObjectDisplayer = new $classToCall();
        $dataObjectDisplayer->setSettings($this->settings);
        $dataObjectDisplayer->setShortcode($shortcode);
        $dataObjectDisplayer->setFunctionsFacade($this->functionsFacade);
        $dataObjectDisplayer->setDataObject($dataObject);
        $dataObjectDisplayer->setThumbnail($alternativeThumbnail);
        $dataObjectDisplayer->setSessionManager($this->sessionManager);
        return $dataObjectDisplayer;
    }
}

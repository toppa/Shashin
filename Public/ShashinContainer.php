<?php

class Public_ShashinContainer extends Lib_ShashinContainer {
    private $headTagsBuilder;

    public function __construct($autoLoader) {
        parent::__construct($autoLoader);
    }

    public function getLayoutManager(
      Public_ShashinShortcode $shortcode,
      Lib_ShashinDataObjectCollection $dataObjectCollection) {

        $this->getSettings();
        $this->getFunctionsFacade();
        $layoutManager = new Public_ShashinLayoutManager();
        $layoutManager->setSettings($this->settings);
        $layoutManager->setFunctionsFacade($this->functionsFacade);
        $layoutManager->setContainer($this);
        $layoutManager->setShortcode($shortcode);
        $layoutManager->setDataObjectCollection($dataObjectCollection);
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
      Lib_ShashinDataObject $alternativeThumbnail = null) {

        $this->getFunctionsFacade();
        $this->getSettings();
        $settingsValues = $this->settings->get();
        $dataObjectClassName = get_class($dataObject);
        $dataObjectClassName = str_replace('Lib_', 'Public_', $dataObjectClassName);
        $albumType = ucfirst($dataObject->albumType);
        $viewerName = ucfirst($settingsValues['imageDisplay']);
        $classToCall = $dataObjectClassName . 'Displayer' . $albumType . $viewerName;
        $dataObjectDisplayer = new $classToCall();
        $dataObjectDisplayer->setSettingsValues($settingsValues);
        $dataObjectDisplayer->setShortcode($shortcode);
        $dataObjectDisplayer->setFunctionsFacade($this->functionsFacade);
        $dataObjectDisplayer->setDataObject($dataObject);
        $dataObjectDisplayer->setThumbnail($alternativeThumbnail);
        return $dataObjectDisplayer;
    }
}

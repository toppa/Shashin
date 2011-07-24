<?php

class Public_ShashinShortcodeTransformer {
    private $shortcode;
    private $container;
    private $dataObjectCollection;
    private $layoutManager;

    public function __construct(array $shortcode, Lib_ShashinContainer $container) {
        $this->shortcode = $shortcode;
        $this->container = $container;
    }

    public function setDataObjectCollection(Lib_ShashinDataObjectCollection $dataObjectCollection) {
        $this->dataObjectCollection = $dataObjectCollection;
    }

    public function setLayoutManager(Public_ShashinLayoutManager $layoutManager) {
        $this->layoutManager = $layoutManager;
    }

    public function getShortcode() {
        return $this->shortcode;
    }

    public function cleanShortcode() {
        array_walk($this->shortcode, array('ToppaFunctions', 'trimCallback'));
        array_walk($this->shortcode, array('ToppaFunctions', 'strtolowerCallback'));
        return $this->shortcode;
    }

    public function run() {
        try {
            $collection = $this->dataObjectCollection->getCollectionForShortcode($this->shortcode);
            $thumbnailCollection = null;

            if ($this->shortcode['thumbnail']) {
                $this->dataObjectCollection->setUseThumbnailId(true);
                $thumbnailCollection = $this->dataObjectCollection->getCollection($this->shortcode);
            }

            return $this->layoutManager->run($this->container, $this->shortcode, $collection, $thumbnailCollection);

        }

        catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
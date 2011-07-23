<?php

class Public_ShashinShortcodeTransformer {
    private $shortcode;
    private $container;
    private $dataObjectCollection;

    public function __construct(array $shortcode, Lib_ShashinContainer $container) {
        $this->shortcode = $shortcode;
        $this->container = $container;
    }

    public function setDataObjectCollection(Lib_ShashinDataObjectCollection $dataObjectCollection) {
        $this->dataObjectCollection = $dataObjectCollection;
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
            $tags = '';
            $collection = $this->dataObjectCollection->getCollectionForShortcode($this->shortcode);

            if ($this->shortcode['thumbnail']) {
                $this->dataObjectCollection->setUseThumbnailId(true);
                $thumbnailCollection = $this->dataObjectCollection->getCollection($this->shortcode);
            }

            foreach ($collection as $photo) {
                $photoDisplayer = $this->container->getPhotoDisplayer($photo);
                $tags .= $photoDisplayer->run('small');
            }

            return $tags;
        }

        catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
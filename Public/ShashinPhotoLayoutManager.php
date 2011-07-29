<?php

class Public_ShashinPhotoLayoutManager extends Public_ShashinLayoutManager {
    public function __construct(
      Lib_ShashinSettings $settings,
      ToppaFunctionsFacade $functionsFacade) {

        parent::__construct($settings, $functionsFacade);
    }

    public function setPhotoTableCaptionTag() {
        if ($this->shortcode['type'] == 'albumphotos') {
            $this->tableCaptionTag =  '<caption>work in progress</caption>' . PHP_EOL;
        }

        return $this->tableCaptionTag;
    }

    public function generateCaption(Lib_ShashinDataObject $photo, $linkTag = null) {
        if ($this->shortcode['caption'] == 'y' && $photo->description) {
            return '<span class="shashin3alpha_thumb_caption">'
                . $photo->description
                . '</span>';
        }

        return null;
    }
}
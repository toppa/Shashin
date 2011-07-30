<?php

class Public_ShashinAlbumLayoutManager extends Public_ShashinLayoutManager {
    public function __construct(
      Lib_ShashinSettings $settings,
      ToppaFunctionsFacade $functionsFacade) {

        parent::__construct($settings, $functionsFacade);

    }

    public function setPhotoTableCaptionTag() {
        return null;
    }

    public function generateCaption(Lib_ShashinDataObject $album, $linkTag = null) {
        if ($this->shortcode['caption'] != 'n') {
            $caption = $this->generateCaptionTitle($album, $linkTag);
            $caption .= $this->generateCaptionDate($album);
            $caption .= $this->generateCaptionLocationAndPhotoCount($album);
            return $caption;
        }

        return null;
    }

    private function generateCaptionTitle($album, $linkTag) {
        $caption = '<span class="shashin3alpha_album_caption_title">';
        $caption .= $linkTag ? $linkTag : '';
        $caption .= $album->title;
        $caption .= $linkTag ? '</a>' : '';
        $caption .= '</span>' . PHP_EOL;
        return $caption;
    }

    private function generateCaptionDate($album) {
        return '<span class="shashin3alpha_album_caption_date">'
            . $this->functionsFacade->dateI18n("M j, Y", $album->pubDate) . '</span>' . PHP_EOL;
    }

    private function generateCaptionLocationAndPhotoCount($album) {
        if ($album->location) {
            $caption = '<span class="shashin3alpha_album_caption_location">';
                if ($album->geoPos) {
                    $caption .= '<a href="http://maps.google.com/maps?q='
                        . urlencode($album->geoPos)
                        . '"><img src="'
                        . $this->functionsFacade->getPluginsUrl('/Display/mapped_sm.gif', __FILE__)
                        . '" alt="Google Maps Location" width="15" height="12" /></a>';
                }

            $caption .= 'Photos: ' . $album->photoCount . '</span>' . PHP_EOL;
            return $caption;
        }

        return null;
    }
}
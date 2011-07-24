<?php


abstract class Lib_ShashinPhotoDisplayer {
    protected $photo;
    protected $thumbnail;
    protected $actualSize;
    protected $displayCropped = false;
    protected $imgHeight;
    protected $imgWidth;
    protected $imgSrc;
    protected $imgAltAndTitle;
    protected $imgClass;
    protected $imgTag;
    protected $aHref;
    protected $aTag;
    protected $aId;
    protected $combinedTags;
    protected $validSizes = array();
    protected $validCropSizes = array();
    protected $sizesMap = array();

    /*
        'flickr' => array(
            'xsmall' => 75,
            'small' => 100,
            'medium' => 240,
            'large' => 500,
            'xlarge' => 1024,
        ),
        'twitpic' => array(
            'xsmall' => 75,
            'small' => 150,
            'medium' => 150,
            'large' => 600,
            'xlarge' => 600,
        )

 */

    public function __construct(Lib_ShashinPhoto $photo, Lib_ShashinPhoto $alternativeThumbnail = null) {
        $this->photo = $photo;
        $this->thumbnail = $alternativeThumbnail ? $alternativeThumbnail : $this->photo;

        if (!$_SESSION['shashin_id_counter']) {
            $_SESSION['shashin_id_counter'] = 1;
        }
    }

    public function run($requestedSize = 'small', $requestedCropped = 'false') {
        try {
            $numericSize = $this->setNumericSizeFromRequestedSize($requestedSize);
            $this->setActualSizeFromValidSizes($numericSize);
            $this->setDisplayCroppedIfRequested($requestedCropped);
            $this->setImgWidthAndHeight();
            $this->setImgSrc();
            $this->setImgAltAndTitle();
            $this->setImgTag();
            $this->setAHref();
            $this->setAId();
            $this->setATag();
            $this->setCombinedTags();
            $this->incrementSessionIdCounter();
        }

        catch (Exception $e) {
            return "<strong>" . $e->getMessage() . "</strong>";
        }

        return $this->combinedTags;
    }

    public function setNumericSizeFromRequestedSize($requestedSize = 'small') {
        if (array_key_exists($requestedSize, $this->sizesMap)) {
            $numericSize = $this->sizesMap[$requestedSize];
        }

        else {
            $numericSize = $requestedSize;
        }

        if (!is_numeric($numericSize)) {
            throw New Exception("invalid size requested");
        }

        return $numericSize;
    }

    public function setActualSizeFromValidSizes($numericSize) {
        foreach ($this->validSizes as $size) {
            if ($numericSize <= $size) {
                $this->actualSize = $size;
                break;
            }
        }

        return $this->actualSize;
    }

    public function setDisplayCroppedIfRequested($requestedCropped = 'false') {
        if ($requestedCropped) {
            if (in_array($this->actualSize, $this->validCropSizes)) {
                $this->displayCropped = true;
            }
        }

        return $this->displayCropped;
    }

    public function setImgWidthAndHeight() {
        if ($this->displayCropped) {
            $this->imgWidth = $this->actualSize;
            $this->imgHeight = $this->actualSize;
        }

        // see if actualSize should be applied to the height or the width
        elseif ($this->thumbnail->width > $this->thumbnail->height) {
            $this->imgWidth = $this->actualSize;
            $percentage = $this->actualSize / $this->thumbnail->width;
            $this->imgHeight = $percentage * $this->thumbnail->height;
            settype($this->imgHeight, "int"); // drop any decimals
        }

        else {
            $this->imgHeight = $this->actualSize;
            $percentage = $this->actualSize / $this->thumbnail->height;
            $this->imgWidth = $percentage * $this->thumbnail->width;
            settype($this->imgWidth, "int"); // drop any decimals
        }

        return true;
    }

    abstract public function setImgSrc();

    public function setImgAltAndTitle() {
        // there may already be entities in the description, so we want to be
        // conservative with what we replace
        $this->imgAltAndTitle = str_replace('"', '&quot;', $this->photo->description);
    }

    public function setImgClass($class) {
        $this->imgClass = $class;
    }

    public function setImgTag() {
        $this->imgTag =
            '<img src="' . $this->imgSrc
            . '" alt="' . $this->imgAltAndTitle
            . '" title="' . $this->imgAltAndTitle
            . '" width="' . $this->imgWidth
            . '" height="' . $this->imgHeight
            . '" id="shashin_thumb_image_' . $_SESSION['shashin_id_counter'] . '"';

        if ($this->imgClass) {
            $this->imgTag .= ' class="' . $this->imgClass . '"';
        }

        $this->imgTag .= ' />';
    }

    abstract public function setAHref();

    public function setAId() {
        $this->aId = 'shashin_thumb_link_' . $_SESSION['shashin_id_counter'];
        return $this->aId;
    }

    public function incrementSessionIdCounter() {
        $_SESSION['shashin_id_counter']++;
    }

    public function setATag() {
        $this->aTag =
            '<a href="' . $this->aHref
            . '" id="' . $this->aId
            . '">';
        return $this->aTag;
    }

    public function setCombinedTags() {
        $this->combinedTags = $this->aTag . $this->imgTag . '</a>';
        return $this->combinedTags;
    }
}

<?php

class Lib_ShashinPhotoDisplayerPicasa {
    private $photo;
    private $album;
    private $requestedSize;
    private $numericSize;
    private $actualSize;
    private $requestedCropped = false;
    private $displayCropped = false;
    private $imgHeight;
    private $imgWidth;
    private $imgSrc;
    private $imgAltAndTitle;
    private $imgClass;
    private $imgTag;
    private $validSizes = array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800);
    private $validCropSizes = array(32, 48, 64, 160);
    private $sizesMap = array(
        'xsmall' => 72,
        'small' => 160,
        'medium' => 320,
        'large' => 640,
        'xlarge' => 800,
    );

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
    public function __construct(Lib_ShashinAlbum &$album) {
        $this->album = $album;

        if (!$_SESSION['shashin_id_counter']) {
            $_SESSION['shashin_id_counter'] = 1;
        }
    }

    public function setPhoto(Lib_ShashinPhoto &$photo) {
        $this->photo = $photo;
    }

    public function setRequestedSize($requestedSize) {
        $this->requestedSize = $requestedSize;
    }

    public function setRequestedCropped($requestedCropped) {
        $this->requestedCropped = $requestedCropped;
    }

    public function run() {
        try {
            $this->setNumericSizeFromRequestedSize();
            $this->checkNumericSizeIsNumeric();
            $this->setActualSizeFromValidSizes();
            $this->setDisplayCroppedIfRequested();
            $this->setImgWidthAndHeight();
            $this->setImgSrc();
            $this->setImgAltAndTitle();
            $this->setImgTag();
            $_SESSION['shashin_id_counter']++;
        }

        catch (Exception $e) {
            return "<strong>" . $e->getMessage() . "</strong>";
        }

        return $this->imgTag;
//<img src="' . $photo['enclosure_url'] . '?imgmax=72&amp;crop=1" />

    }

    public function setNumericSizeFromRequestedSize() {
        if (array_key_exists($this->requestedSize, $this->sizesMap)) {
            $this->numericSize = $this->sizesMap[$this->requestedSize];
        }

        else {
            $this->numericSize = $this->requestedSize;
        }

        return true;
    }

    public function checkNumericSizeIsNumeric() {
        if (!is_numeric($this->numericSize)) {
            throw New Exception("invalid size requested");
        }

        return true;
    }

    public function setActualSizeFromValidSizes() {
        foreach ($this->validSizes as $size) {
            if ($this->numericSize <= $size) {
                $this->actualSize = $size;
                break;
            }
        }

        return true;
    }

    public function setDisplayCroppedIfRequested() {
        if ($this->requestedCropped) {
            if (in_array($this->actualSize, $this->validCropSizes)) {
                $this->displayCropped = true;
            }
        }

        return true;
    }

    public function setImgWidthAndHeight() {
        if ($this->displayCropped) {
            $this->imgWidth = $this->actualSize;
            $this->imgHeight = $this->actualSize;
        }

        // see if actualSize should be applied to the height or the width
        elseif ($this->photo->width > $this->photo->height) {
            $this->imgWidth = $this->actualSize;
            $percentage = $this->actualSize / $this->photo->width;
            $this->imgHeight = $percentage * $this->photo->height;
            settype($this->imgHeight, "int"); // drop any decimals
        }

        else {
            $this->imgHeight = $this->actualSize;
            $percentage = $this->actualSize / $this->photo->height;
            $this->imgWidth = $percentage * $this->photo->width;
            settype($this->imgWidth, "int"); // drop any decimals
        }

        return true;
    }

    public function setImgSrc() {
        $this->imgSrc = $this->photo->contentUrl;
        $this->imgSrc .= '?imgmax=' . $this->actualSize;

        if ($this->displayCropped) {
            $this->imgSrc .= '&amp;crop=1';
        }

        return true;
    }

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
}

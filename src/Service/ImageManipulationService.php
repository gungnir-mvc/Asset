<?php
namespace Gungnir\Asset\Service;

use \Gungnir\Asset\Service\Exception\ImageManipulationServiceException;

class ImageManipulationService implements ImageManipulationServiceInterface
{
    CONST FILTER_DEFAULT    = \Imagick::FILTER_UNDEFINED;
    CONST BLUR_DEFAULT      = 1;
    CONST BEST_FIT_DEFAULT  = false;

    /** @var Int */
    private $filter = null;

    /** @var Int */
    private $blur = null;

    /** @var boolean */
    private $bestFit = null;

    /**
     * ImageManipulationService constructor.
     */
    public function __construct()
    {
        $this->initializeDefaultSettings();
    }

    /**
     * @return Int
     */
    public function getFilter(): Int
    {
        return $this->filter;
    }

    /**
     * @param Int $filter
     * @return ImageManipulationService
     */
    public function setFilter(Int $filter): ImageManipulationService
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @return Int
     */
    public function getBlur(): Int
    {
        return $this->blur;
    }

    /**
     * @param Int $blur
     * @return ImageManipulationService
     */
    public function setBlur(Int $blur): ImageManipulationService
    {
        $this->blur = $blur;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBestFit(): bool
    {
        return $this->bestFit;
    }

    /**
     * @param bool $bestFit
     *
     * @return ImageManipulationService
     */
    public function setBestFit(bool $bestFit): ImageManipulationService
    {
        $this->bestFit = $bestFit;
        return $this;
    }

    /**
     * Resize passed image
     *
     * @param \Imagick $image
     * @param Int $height
     * @param Int $width
     *
     * @throws ImageManipulationServiceException
     * @return \Imagick
     */
    public function resize(\Imagick $image, Int $height, Int $width): \Imagick
    {
        $image->resizeImage($width, $height, $this->getFilter(), $this->getBlur(), $this->isBestFit());
        $this->initializeDefaultSettings();
        return $image;
    }

    /**
     * Scales an image up or down based on passed parameters
     *
     * @param \Imagick $image
     * @param Int $amount
     * @param bool $up
     *
     * @throws ImageManipulationServiceException
     * @return \Imagick
     */
    public function scale(\Imagick $image, Int $amount, Bool $up = false): \Imagick
    {
        $rows = $image->getImageWidth() * $amount;
        $cols = $image->getImageHeight() * $amount;
        $image->scaleImage($cols, $rows);

        return $image;
    }

    /**
     * return ImageManipulationServiceInterface
     */
    private function initializeDefaultSettings()
    {
        return $this
            ->setFilter(self::FILTER_DEFAULT)
            ->setBlur(self::BLUR_DEFAULT)
            ->setBestFit(self::BEST_FIT_DEFAULT);
    }
}
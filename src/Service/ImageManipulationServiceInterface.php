<?php
namespace Gungnir\Asset\Service;

use \Gungnir\Asset\Service\Exception\ImageManipulationServiceException;

interface ImageManipulationServiceInterface
{
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
    public function resize(\Imagick $image, Int $height, Int $width) : \Imagick;

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
    public function scale(\Imagick $image, Int $amount, Bool $up = false) : \Imagick;

}
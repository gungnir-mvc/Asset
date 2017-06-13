<?php
namespace Gungnir\Asset\Repository;

use \Gungnir\Asset\Repository\Exception\ImageRepositoryException;

interface ImageRepositoryInterface
{
    /**
     * Locate and return an image based on passed image name
     *
     * @param String $imageName
     * @param array  $options
     *
     * @throws ImageRepositoryException
     * @return mixed
     */
    public function getImage(String $imageName, array $options = []) : \Imagick;
}
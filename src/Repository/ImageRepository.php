<?php
namespace Gungnir\Asset\Repository;

use Gungnir\Core\Container;
use Gungnir\Asset\Repository\Exception\ImageRepositoryException;
use Gungnir\Asset\Service\ImageManipulationServiceInterface;

class ImageRepository implements ImageRepositoryInterface
{
    /** @var Container|null */
    private $container = null;

    /** @var ImageManipulationServiceInterface */
    private $imageManipulationService = null;

    /**
     * ImageRepository constructor.
     *
     * @param Container $container
     * @param ImageManipulationServiceInterface $imageManipulationService
     */
    public function __construct(Container $container, ImageManipulationServiceInterface $imageManipulationService)
    {
        $this->container = $container;
        $this->imageManipulationService = $imageManipulationService;
    }

    /**
     * @return Container|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container|null $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return ImageManipulationServiceInterface
     */
    public function getImageManipulationService(): ImageManipulationServiceInterface
    {
        return $this->imageManipulationService;
    }

    /**
     * @param ImageManipulationServiceInterface $imageManipulationService
     */
    public function setImageManipulationService(ImageManipulationServiceInterface $imageManipulationService)
    {
        $this->imageManipulationService = $imageManipulationService;
    }

    /**
     * Locate and return an image based on passed image name
     *
     * @param String $imageName
     * @param array  $options
     *
     * @throws ImageRepositoryException
     *
     * @return \Imagick
     */
    public function getImage(String $imageName, array $options = []) : \Imagick
    {
        // Outputs something like imagename_height_width.extension
        $realImageName = $this->getRealImageName($imageName, $options);
        $options = empty($options) ? $this->extractOptionsFromImageName($imageName) : $options;
        $root = $this->getContainer()->get('Application')->getRoot();
        $imagePath =  $root . '/images/' . $imageName;
        $realImagePath =  $root . '/images/' . $realImageName;

        $imageExists = file_exists($imagePath);
        $realImageExists = file_exists($realImagePath);

        if (strcmp($imageName, $realImageName) === 0 && $imageExists) {
            // Both names are the same and it exists so load it
            $image = new \Imagick(realpath($imagePath));
        } elseif ($realImageExists) {
            // Names are not the same but the alternative image exists
            $image = new \Imagick(realpath($realImagePath));
        } else {
            // Image names differs so we must load existing image apply options and further save it
            $baseImageName = $this->extractBaseImageName($imageName);
            $baseImagePath = $root . '/images/' . $baseImageName;

            if (file_exists($baseImagePath) !== true) {
                throw new ImageRepositoryException('Image ' . $baseImageName . ' does not exist');
            }

            $image = new \Imagick(($baseImagePath));
            $height = $options['height'] ?? null;
            $width  = $options['width'] ?? null;
            $scale  = $options['scale'] ?? null;

            if ($height && $width) {
                $this->getImageManipulationService()->resize($image, $height, $width);
            }

            if ($scale) {
                $this->getImageManipulationService()->scale($image, $scale);
            }

            $image->writeImage((count($options) > 0) ? $realImagePath : $baseImagePath);

        }

        return $image;
    }

    /**
     * @param String $imageName
     * @param array $options
     *
     * @return String
     */
    private function getRealImageName(String $imageName, array $options) : String
    {
        $fileInfo = pathinfo($imageName);
        $realImageName = $fileInfo['filename'];

        if (isset($options['height']) && isset($options['width'])) {
            $realImageName .= '_' . $options['height'] . '_' . $options['width'];
        } elseif (isset($options['scale'])) {

        }

        $realImageName .= '.' . $fileInfo['extension'];
        return $realImageName;
    }

    /**
     * @param $imageName
     *
     * @return array
     */
    private function extractOptionsFromImageName($imageName) : array
    {
        $options = [];
        $fileParts = pathinfo($imageName);
        $filenameParts = explode('_', $fileParts['filename']);

        if (count($filenameParts) === 3) {
            $options['height'] = $filenameParts[1];
            $options['width'] = $filenameParts[2];
        }

        return $options;
    }

    /**
     * @param $imageName
     * @return String
     */
    private function extractBaseImageName($imageName) : String
    {
        $fileParts = pathinfo($imageName);
        $filenameParts = explode('_', $fileParts['filename']);
        return $filenameParts[0] . '.' . $fileParts['extension'];
    }
}
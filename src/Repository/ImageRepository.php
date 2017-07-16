<?php
namespace Gungnir\Asset\Repository;

use Gungnir\Asset\ImageFile;
use Gungnir\Core\Container;
use Gungnir\Asset\Repository\Exception\ImageRepositoryException;
use Gungnir\Asset\Service\ImageManipulationServiceInterface;

class ImageRepository implements ImageRepositoryInterface
{
    const HEIGHT_REGEX = "/h[0-9]\d+/";
    const WIDTH_REGEX = "/w[0-9]\d+/";
    const SCALE_REGEX = "/s[0-9]\d+/";

    /** @var string */
    private $imageBasePath = null;

    /** @var ImageManipulationServiceInterface */
    private $imageManipulationService = null;

    /**
     * ImageRepository constructor.
     *
     * @param string $imageBasePath
     * @param ImageManipulationServiceInterface $imageManipulationService
     */
    public function __construct(String $imageBasePath, ImageManipulationServiceInterface $imageManipulationService)
    {
        $this->imageBasePath = $imageBasePath;
        $this->imageManipulationService = $imageManipulationService;
    }

    /**
     * @return string
     */
    public function getImageBasePath(): string
    {
        return $this->imageBasePath;
    }

    /**
     * @param string $imageBasePath
     * @return ImageRepository
     */
    public function setImageBasePath(string $imageBasePath): ImageRepository
    {
        $this->imageBasePath = $imageBasePath;
        return $this;
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
        // Outputs something like imageName_height_width.extension
        $realImageName = $this->getRealImageName($imageName, $options);
        $options = empty($options) ? $this->extractOptionsFromImageName($imageName) : $options;

        $imagePath =  $this->getImageBasePath() . $imageName;
        $realImagePath =  $this->getImageBasePath() . $realImageName;

        $imageExists = file_exists($imagePath);
        $realImageExists = file_exists($realImagePath);

        if (strcmp($imageName, $realImageName) === 0 && $imageExists) {
            // Both names are the same and it exists so load it
            $image = new \Imagick($imagePath);
        } elseif ($realImageExists) {
            // Names are not the same but the alternative image exists
            $image = new \Imagick($realImagePath);
        } else {
            // Image names differs so we must load existing image apply options and further save it
            $baseImageName = $this->extractBaseImageName($imageName);
            $baseImagePath = $this->getImageBasePath() . $baseImageName;

            if (file_exists($baseImagePath) !== true) {
                throw new ImageRepositoryException('Image ' . $baseImageName . ' does not exist');
            }

            $image = new \Imagick(($baseImagePath));
            $height = $options['height'] ?? null;
            $width  = $options['width'] ?? null;
            $scale  = $options['scale'] ?? null;

            if ($height || $width) {
                $this->getImageManipulationService()->resize($image, (int) $height, (int) $width);
            }

            if ($scale) {
                $this->getImageManipulationService()->scale($image, $scale);
            }

            if (count($options) > 0) {
                $fh = fopen($realImagePath, 'c+');
            } else {
                $fh = fopen($baseImagePath, 'c+');
            }

            $image->writeImageFile($fh);
            fclose($fh);

        }
        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function storeImage(ImageFile $image, string $fileName): bool
    {
        try {
            $image->open();
            $stored = $image->move($this->getImageBasePath() . $fileName);
            $image->close();
        } catch (\Exception $e) {
            $stored = false;
        }
        return $stored;
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

        if (isset($options['scale'])) {
            $realImageName .= '_s' . $options['scale'];
        } else {
            if (isset($options['height'])) {
                $realImageName .= '_h' . $options['height'];
            }

            if (isset($options['width'])) {
                $realImageName .= '_w' . $options['width'];
            }
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
        return [
            'height' => $this->extractOptionFromImageName($pattern = self::HEIGHT_REGEX, $haystack = $imageName),
            'width'  => $this->extractOptionFromImageName($pattern = self::WIDTH_REGEX, $haystack = $imageName),
            'scale'  => $this->extractOptionFromImageName($pattern = self::SCALE_REGEX, $haystack = $imageName),
        ];
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

    /**
     * @param string $pattern
     * @param string $haystack
     *
     * @return string|null
     */
    private function extractOptionFromImageName(string $pattern, string $haystack): ?string
    {
        $matches = [];
        preg_match($pattern, $haystack, $matches);
        if (empty($matches)) return null;
        return array_shift($matches);
    }
}
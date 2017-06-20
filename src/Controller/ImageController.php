<?php
namespace Gungnir\Asset\Controller;

use Gungnir\Asset\PathString;
use Gungnir\Event\GenericEventObject;
use Gungnir\Framework\AbstractController;
use Gungnir\HTTP\Request;
use Gungnir\HTTP\Response;
use Gungnir\Asset\Repository\Exception\ImageRepositoryException;
use Gungnir\Asset\Repository\ImageRepository;
use Gungnir\Asset\Service\ImageManipulationService;

class ImageController extends AbstractController
{
    /** @var ImageRepository */
    private $imageRepository = null;

    /**
     * @return ImageRepository
     */
    public function getImageRepository(): ImageRepository
    {
        if (empty($this->imageRepository)) {
            $imageRootPath = new PathString($this->getApplication()->getRootPath() . 'images/');
            $this->getApplication()->getEventDispatcher()->emit(
                'gungnir.asset.imagey.basepath',
                new GenericEventObject($imageRootPath)
            );
            $this->imageRepository = new ImageRepository(
                $imageRootPath,
                    new ImageManipulationService()
            );
        }
        return $this->imageRepository;
    }

    /**
     * @param ImageRepository $imageRepository
     * @return ImageController
     */
    public function setImageRepository(ImageRepository $imageRepository): ImageController
    {
        $this->imageRepository = $imageRepository;
        return $this;
    }


    /**
     * Default entry point action for application
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $imageName = $request->parameters()->get('param');
        if (empty($imageName)) {
            $response = new Response('', 400);
            return $response;
        }

        try {
            $options = $request->query()->parameters();
            $image =  $this->getImageRepository()
                ->getImage($imageName, $options);

        } catch (ImageRepositoryException $e) {
            return new Response('', 404);
        }

        $format = $image->getImageFormat();
        switch( strtolower($format) ) {
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpeg"; break;
            default:
        }

        $response = new Response($image->getImageBlob());
        $response->setHeader('Content-type', $ctype);
        return $response;
    }
}
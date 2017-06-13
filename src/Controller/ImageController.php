<?php
namespace Gungnir\Asset\Controller;

use Gungnir\Framework\Controller;
use Gungnir\HTTP\Request;
use Gungnir\HTTP\Response;
use Gungnir\Asset\Repository\Exception\ImageRepositoryException;
use Gungnir\Asset\Repository\ImageRepository;
use Gungnir\Asset\Service\ImageManipulationService;

class ImageController extends Controller
{
    /**
     * Default entrypoint action for application
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

        $imageRepository = new ImageRepository($this->getContainer(), new ImageManipulationService());

        try {
            $options = $request->query()->parameters();
            $image =  $imageRepository->getImage($imageName, $options);
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
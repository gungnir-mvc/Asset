<?php
namespace Gungnir\Asset\Controller;

use Gungnir\Asset\PathString;
use Gungnir\Asset\Repository\Exception\StyleRepositoryException;
use Gungnir\Asset\Repository\StyleRepository;
use Gungnir\Event\GenericEventObject;
use Gungnir\Framework\AbstractController;
use Gungnir\HTTP\Request;
use Gungnir\HTTP\Response;

class StyleController extends AbstractController
{

    /** @var StyleRepository */
    private $styleRepository = null;

    /**
     * @return StyleRepository
     */
    public function getStyleRepository(): StyleRepository
    {
        if (empty($this->styleRepository)) {
            $styleRootPath = new PathString($this->getApplication()->getRootPath() . 'css/');
            $this->getApplication()->getEventDispatcher()->emit(
                'gungnir.asset.styley.basepath',
                new GenericEventObject($styleRootPath)
                );
            $this->styleRepository = new StyleRepository(
                $styleRootPath
            );
        }
        return $this->styleRepository;
    }

    /**
     * @param StyleRepository $styleRepository
     *
     * @return StyleController
     */
    public function setStyleRepository(StyleRepository $styleRepository)
    {
        $this->styleRepository = $styleRepository;
        return $this;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getIndex(Request $request): Response
    {
        $stylesheetName = $request->parameters()->get('param');
        $response = new Response();

        if (empty($stylesheetName)) {
            $response->statusCode(404);
            return $response;
        }

        $stylesheetRepository = $this->getStyleRepository();

        try {
            $stylesheet = $stylesheetRepository->getStylesheet($stylesheetName);
        } catch (StyleRepositoryException $e) {
            $response->statusCode(404);
            return $response;
        }

        $response->setBody($stylesheet->read());
        $response->setHeader('Content-type', 'text/css');

        return $response;
    }
}
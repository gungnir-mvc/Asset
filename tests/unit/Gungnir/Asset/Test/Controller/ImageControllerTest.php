<?php
namespace Gungnir\Asset\Test\Controller;

use Gungnir\Asset\Controller\ImageController;
use Gungnir\Asset\Repository\ImageRepository;
use Gungnir\Core\Application;
use Gungnir\HTTP\Request;
use Gungnir\HTTP\Response;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class ImageControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itCanBeInstantiated()
    {
        $controller = new ImageController(new Application());
        $this->assertInstanceOf(ImageController::class, $controller);
    }

    /**
     * @test
     */
    public function itReturns400BadRequestResponseWhenImageParameterIsNotPresentInRequest()
    {
        $controller = new ImageController(new Application());
        $request = new Request();
        $response  = $controller->getIndex($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->statusCode());
    }

    /**
     * @test
     */
    public function itReturns404NotFoundResponseWhenImageDoesNotExist()
    {
        $app = new Application();

        $controller = new ImageController($app);
        $request = new Request([],[],['param' => 'test.png']);
        $response  = $controller->getIndex($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->statusCode());
    }

    /**
     * @test
     */
    public function itReturnsResponseWithImageAsContentWhenImageExist()
    {
        $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');

        $image = $this->getMockBuilder(\Imagick::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getImageFormat',
                'getImageBlob'
            ])
            ->getMock();

        $image->expects($this->atLeastOnce())
            ->method('getImageFormat')
            ->will($this->returnValue('png'));
        $image->expects($this->atLeastOnce())
            ->method('getImageBlob')
            ->will($this->returnValue(
                $imageContent
            ));

        $imageRepositoryMock = $this->getMockBuilder(ImageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getImage'])
            ->getMock();

        $imageRepositoryMock->expects($this->once())
            ->method('getImage')
            ->with('test.png', [])
            ->will($this->returnValue($image));

        $app = new Application();


//

        $controller = new ImageController($app);
        $controller->setImageRepository($imageRepositoryMock);
        $request = new Request();
        $request->parameters()->set('param', 'test.png');
        $response  = $controller->getIndex($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->statusCode());
    }
}
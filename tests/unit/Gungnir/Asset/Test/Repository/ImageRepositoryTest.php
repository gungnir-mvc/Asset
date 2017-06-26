<?php
namespace Gungnir\Asset\Test\Repository;

use Gungnir\Asset\ImageFile;
use Gungnir\Asset\Repository\ImageRepository;
use Gungnir\Asset\Service\ImageManipulationService;
use org\bovigo\vfs\vfsStream;

class ImageRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itCanBeInstantiated()
    {
        $repository = new ImageRepository('', new ImageManipulationService());
        $this->assertInstanceOf(ImageRepository::class, $repository);
    }

    /**
     * @test
     */
    public function itCanReturnAnExistingImage()
    {
        $root = vfsStream::setup('root');
        $imageFolder = vfsStream::newDirectory('images');

        $root->addChild($imageFolder);
        $rootImagePath = $root->url() . '/images/';

        $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');

        file_put_contents($rootImagePath . 'test.png', $imageContent);

        $repository = new ImageRepository($rootImagePath, new ImageManipulationService());
        $image = $repository->getImage('test.png');
        $this->assertEquals(1, $image->getImageHeight());
        $this->assertEquals(1, $image->getImageWidth());

    }

    /**
     * @test
     */
    public function itCanReturnAnResizedImageIfOriginalExists()
    {
        $root = vfsStream::setup('root');
        $imageFolder = vfsStream::newDirectory('images');
        $root->addChild($imageFolder);
        $rootImagePath = $root->url() . '/images/';

        $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
        file_put_contents($rootImagePath . 'test.png', $imageContent);

        $repository = new ImageRepository($rootImagePath, new ImageManipulationService());
        $image = $repository->getImage('test.png', ['width'=> '10', 'height'=> '10']);
        $this->assertEquals(10, $image->getImageHeight());
        $this->assertEquals(10, $image->getImageWidth());
    }

    /**
     * @test
     */
    public function itCanStoreImage()
    {
        $root = vfsStream::setup('root');
        $imageFolder = vfsStream::newDirectory('images');
        $tmpFolder = vfsStream::newDirectory('tmp');
        $root->addChild($imageFolder);
        $root->addChild($tmpFolder);

        $rootImagePath = $root->url() . '/images/';

        $tmpName = '/tmp/a0ee';
        $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
        file_put_contents($root->url() . $tmpName , $imageContent);

        $image = new ImageFile($root->url() . $tmpName);

        $repository = new ImageRepository($rootImagePath, new ImageManipulationService());
        $repository->storeImage($image, 'expected.png');
        $outputImage = $repository->getImage('expected.png');

        $this->assertNotEmpty($outputImage);
    }
}
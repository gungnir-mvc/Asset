<?php
namespace Gungnir\Asset\Test\Service;

use Gungnir\Asset\Service\ImageManipulationService;

class ImageManipulationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itCanBeInstantiated()
    {
        $service = new ImageManipulationService();
        $this->assertInstanceOf(ImageManipulationService::class, $service);
    }

    /**
     * @test
     */
    public function itCanResizeAnImage()
    {
        $image = new \Imagick();
        // Data of 1px h+w png image
        $image->readImageBlob(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII='));
        $image->setImageBackgroundColor('#fff');
        $service = new ImageManipulationService();
        $image = $service->resize($image, 5, 5);

        $this->assertEquals(5, $image->getImageHeight());
        $this->assertEquals(5, $image->getImageWidth());
    }

    /**
     * @test
     */
    public function itCanResizeAnImageWithOnlyOneNonZeroValue()
    {
        $image = new \Imagick();
        // Data of 1px h+w png image
        $image->readImageBlob(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII='));
        $image->setImageBackgroundColor('#fff');
        $service = new ImageManipulationService();
        $image = $service->resize($image, 0, 5);

        $this->assertEquals(5, $image->getImageHeight());
        $this->assertEquals(5, $image->getImageWidth());
    }

    /**
     * @test
     */
    public function itCanScaleAnImage()
    {
        $image = new \Imagick();
        // Data of 1px h+w png image
        $image->readImageBlob(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII='));
        $image->setImageBackgroundColor('#fff');
        $service = new ImageManipulationService();
        $image = $service->scale($image, 2, true);

        $this->assertEquals(2, $image->getImageHeight());
        $this->assertEquals(2, $image->getImageWidth());
    }
}
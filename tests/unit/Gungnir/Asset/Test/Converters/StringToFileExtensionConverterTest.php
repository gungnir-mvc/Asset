<?php
namespace Gungnir\Asset\Test\Converters;

use Gungnir\Asset\Converters\StringToFileExtensionConverter;
use \PHPUnit\Framework\TestCase;

class StringToFileExtensionConverterTest extends TestCase 
{

    /**
     * @test
     */
    public function testThatItCanConvertCorrectly()
    {
        $cases = [
            "image/png" => "png",
            "image/gif" => "gif",
            "image/jpg" => "jpeg",
            "image/jpeg" => "jpeg",
            "image/svg+xml" => "svg"
        ];
        
        foreach ($cases as $mimeType => $extension) {
            $this->assertEquals($extension, StringToFileExtensionConverter::fromMimeType($mimeType));
        }
    }

}
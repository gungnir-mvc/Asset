<?php
namespace Gungnir\Asset\Test\Converters;

use Gungnir\Asset\Converters\StringToMimeTypeConverter;
use \PHPUnit\Framework\TestCase;

class StringToMimeTypeConverterTest extends TestCase 
{

    /**
     * @test
     */
    public function testThatItCanConvertCorrectly()
    {
        $cases = [
            "png"  => "image/png",
            "gif"  => "image/gif",
            "jpeg" => "image/jpg",
            "jpeg" => "image/jpeg",
            "svg"  => "image/svg+xml"
        ];
        
        foreach ($cases as $extension => $mimeType) {
            $this->assertEquals($mimeType, StringToMimeTypeConverter::fromExtension($extension));
        }
    }

}
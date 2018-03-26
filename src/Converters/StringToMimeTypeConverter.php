<?php
namespace Gungnir\Asset\Converters;

class StringToMimeTypeConverter 
{

    /**
     * @param string $extension
     * 
     * @return string|null
     */
    public static function fromExtension(string $extension): ?string
    {
        switch ($extension) {
            case "png":
                return "image/png";
            case "gif":
                return "image/gif";
            case "jpg":
            case "jpeg":
                return "image/jpeg";
            case "svg":
                return "image/svg+xml";
            default:
                return null;
        }
    }
}
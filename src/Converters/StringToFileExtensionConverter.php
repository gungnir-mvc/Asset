<?php
namespace Gungnir\Asset\Converters;

class StringToFileExtensionConverter 
{

    /**
     * @param string $mimeType
     * 
     * @return string|null
     */
    public static function fromMimeType(string $mimeType): ?string
    {
        switch ($mimeType) {
            case "image/png":
                return "png";
            case "image/gif":
                return "gif";
            case "image/jpg":
            case "image/jpeg":
                return "jpeg";
            case "image/svg+xml":
                return "svg";
            default:
                return null;
        }
    }
}
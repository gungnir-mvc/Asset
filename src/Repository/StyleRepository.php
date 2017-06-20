<?php
namespace Gungnir\Asset\Repository;


use Gungnir\Asset\Repository\Exception\StyleRepositoryException;
use Gungnir\Asset\StylesheetFile;
use Gungnir\Core\File;
use Gungnir\Core\FileInterface;

class StyleRepository implements StyleRepositoryInterface
{
    const DEFAULT_COMBINED_NAME = 'combined.css';

    /** @var string */
    private $baseStylesheetPath = null;

    public function __construct(string $baseStylesheetPath)
    {
        $this->baseStylesheetPath = $baseStylesheetPath;
    }

    /**
     * @return string
     */
    public function getBaseStylesheetPath(): string
    {
        return $this->baseStylesheetPath;
    }

    /**
     * @param string $baseStylesheetPath
     * @return StyleRepository
     */
    public function setBaseStylesheetPath(string $baseStylesheetPath): StyleRepository
    {
        $this->baseStylesheetPath = $baseStylesheetPath;
        return $this;
    }

    /**
     * Returns a stylesheet file
     *
     * @param string $style
     *
     * @throws StyleRepositoryException
     * @return FileInterface
     */
    public function getStylesheet(string $style, array $options = []): FileInterface
    {
        $filePath = $this->getBaseStylesheetPath() . $style;
        if (file_exists($filePath) !== true) {
            throw new StyleRepositoryException(
                sprintf('Stylesheet %s does not exist', $style)
            );
        }

        $stylesheet = new StylesheetFile($filePath);
        $stylesheet->open();

        return $stylesheet;
    }

    /**
     *
     *
     * @param array $stylesheetFiles
     * @param array $options
     *
     * @return FileInterface
     * @throws StyleRepositoryException
     */
    public function getCombinedStylesheet(array $stylesheetFiles, array $options = []): FileInterface
    {
        $strict = $options['strict'] ?? false;
        $name = $options['name'] ?? self::DEFAULT_COMBINED_NAME;
        $stylesheets = [];
        foreach ($stylesheetFiles AS $stylesheetFile) {
            $stylesheets[] = $this->getStylesheet($stylesheetFile, $options);
        }

        if ($strict && count($stylesheets) !== count($stylesheetFiles)) {
            throw new StyleRepositoryException(
                sprintf(
                    'All stylesheets required could not be loaded. Needed %s but found %s',
                    count($stylesheetFiles),
                    count($stylesheets)
                    )
            );
        }

        $combined = new StylesheetFile($this->getBaseStylesheetPath() . $name);
        $combined->open();

        foreach ($stylesheets AS $key => $stylesheet) {
            $content = $stylesheet->read();
            $content .= ($key < (count($stylesheets)-1)) ? ' ' : '';
            $combined->write($content);
        }

        // This needs to be done since gungnir file have no implementation to rewind to start of file.
        $combined->close();
        $combined->open();

        return $combined;
    }

}
<?php
namespace Gungnir\Asset\Repository;

use Gungnir\Asset\Repository\Exception\StyleRepositoryException;
use Gungnir\Core\FileInterface;

interface StyleRepositoryInterface
{
    /**
     * Returns a stylesheet file
     *
     * @param string $style
     *
     * @throws StyleRepositoryException
     * @return FileInterface
     */
    public function getStylesheet(string $style, array $options = []): FileInterface;

    /**
     * Returns a combination of all passed stylesheets
     *
     * @param array $stylesheetFiles
     * @param array $options
     *
     * @return FileInterface
     */
    public function getCombinedStylesheet(array $stylesheetFiles, array $options = []): FileInterface;
}
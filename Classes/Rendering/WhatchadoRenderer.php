<?php

namespace TRAW\Whatchado\Rendering;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;

/**
 *
 */
class WhatchadoRenderer implements FileRendererInterface
{

    /**
     * @return int
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function canRender(FileInterface $file)
    {
        return ($file->getMimeType() === 'video/whatchado' || $file->getExtension() === 'whatchado') && $this->getOnlineMediaHelper($file) !== false;
    }

    /**
     * @param FileInterface $file
     * @param int|string $width
     * @param int|string $height
     * @param array $options
     * @param false $usedPathsRelativeToCurrentScript
     * @return string|void
     */
    public function render(FileInterface $file, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false)
    {
        // TODO: Implement render() method.
    }
}
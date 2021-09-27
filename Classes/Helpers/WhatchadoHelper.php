<?php

namespace TRAW\Whatchado\Helpers;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;

class WhatchadoHelper extends AbstractOnlineMediaHelper
{
    public function transformUrlToFile($url, Folder $targetFolder)
    {

    }

    public function getPublicUrl(File $file, $relativeToCurrentScript = false)
    {
        $videoId = $this->getOnlineMediaId($file);
        return sprintf('https://www.whatchado.com/watch?v=%s', rawurlencode($videoId));
    }

    public function getPreviewImage(File $file)
    {
        // TODO: Implement getPreviewImage() method.
    }

    public function getMetaData(File $file)
    {
        // TODO: Implement getMetaData() method.
    }
}
<?php

namespace TRAW\Whatchado\Helpers;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;

class WhatchadoHelper extends AbstractOnlineMediaHelper
{


    public function transformUrlToFile($url, Folder $targetFolder)
    {
        if (preg_match('/^https:\/\/(www\.)?whatchado\.com\/(de|en)\/(:?embeds\/)?(videos|stories)\/(.+)$/', $url, $match)) {
            $language = $match[2];
            $videoId = end($match);
        }

        if (empty($language) || empty($videoId)) {
            return null;
        }

        $file = $this->findExistingFileByOnlineMediaId($videoId, $targetFolder, $this->extension);

        if ($file === null) {
            $fileName = "${videoId}_${language}" . '.' . $this->extension;

            $file = $this->createNewFile($targetFolder, $fileName, "${videoId}|${language}");
        }
        return $file;
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
        //@todo: figure out how to get metadata of whatchado videos
        return [
            'width' => '1920',
            'height' => '1080',
            'title' => '',
        ];
    }
}
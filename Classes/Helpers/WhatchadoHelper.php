<?php

namespace TRAW\Whatchado\Helpers;

use TRAW\Whatchado\Utility\ApiUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class WhatchadoHelper
 */
class WhatchadoHelper extends AbstractOnlineMediaHelper
{
    /**
     * @param string $url
     *
     * @return File|null
     */
    public function transformUrlToFile($url, Folder $targetFolder)
    {
        if (preg_match('/^https:\/\/(www\.)?whatchado\.com\/(de|en)\/(:?embeds\/)?(videos|stories)\/(.+)$/', $url, $match)) {
            $language = $match[2];
            $videoId = end($match);
        }

        if (empty($videoId)) {
            return null;
        }

        $file = $this->findExistingFileByOnlineMediaId($videoId, $targetFolder, $this->extension);

        if ($file === null) {
            $fileName = $videoId . '_' . $language . '.' . $this->extension;

            $file = $this->createNewFile($targetFolder, $fileName, sprintf('%s|%s', $videoId, $language));
        }

        return $file;
    }

    public function getPublicUrl(File $file): string
    {
        $videoId = $this->getOnlineMediaId($file);
        return sprintf('https://www.whatchado.com/de/videos/%s', rawurlencode($videoId));
    }

    public function getPreviewImage(File $file): string
    {
        $meta = $this->getMetaData($file);

        $videoId = $this->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . 'whatchado_' . md5($videoId) . '.jpg';

        if (!file_exists($temporaryFileName)) {
            $previewImage = empty($meta['previewImage']) ? false : GeneralUtility::getUrl($meta['previewImage']);
            if ($previewImage !== false) {
                file_put_contents($temporaryFileName, $previewImage);
                GeneralUtility::fixPermissions($temporaryFileName);
            }
        }

        return $temporaryFileName;
    }

    /**
     * @return array
     */
    public function getMetaData(File $file)
    {
        $apiUtility = GeneralUtility::makeInstance(ApiUtility::class);
        $meta = $apiUtility->fetchMetaData($file);

        $meta['width'] = '1900';
        $meta['height'] = '1080';

        return $meta;
    }
}

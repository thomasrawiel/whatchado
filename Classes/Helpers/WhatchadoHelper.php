<?php

namespace TRAW\Whatchado\Helpers;

use TRAW\Whatchado\Utility\ApiUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class WhatchadoHelper
 * @package TRAW\Whatchado\Helpers
 */
class WhatchadoHelper extends AbstractOnlineMediaHelper
{
    /**
     * @param string $url
     * @param Folder $targetFolder
     * @return File|null
     */
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

    /**
     * @param File $file
     * @param false $relativeToCurrentScript
     * @return string|null
     */
    public function getPublicUrl(File $file, $relativeToCurrentScript = false)
    {
        $videoId = $this->getOnlineMediaId($file);
        return sprintf('https://www.whatchado.com/de/videos/%s', rawurlencode($videoId));
    }

    /**
     * @param File $file
     * @return string
     */
    public function getPreviewImage(File $file)
    {
        $meta = $this->getMetaData($file);

        $videoId = $this->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . 'whatchado_' . md5($videoId) . '.jpg';

        if (!file_exists($temporaryFileName)) {
            $tryNames = ['maxresdefault.jpg', 'mqdefault.jpg', '0.jpg'];
            foreach ($tryNames as $tryName) {
                $previewImage = GeneralUtility::getUrl($meta['previewImage']);
                if ($previewImage !== false) {
                    file_put_contents($temporaryFileName, $previewImage);
                    GeneralUtility::fixPermissions($temporaryFileName);
                    break;
                }
            }
        }

        return $temporaryFileName;
    }

    /**
     * @param File $file
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
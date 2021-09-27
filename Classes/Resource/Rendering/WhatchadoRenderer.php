<?php

namespace TRAW\Whatchado\Resource\Rendering;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;

/**
 *
 */
class WhatchadoRenderer implements FileRendererInterface
{
    /**
     * @var OnlineMediaHelperInterface
     */
    protected $onlineMediaHelper;

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
        $options = $this->collectOptions($options, $file);
        $src = $this->createWhatchadoUrl($options, $file);
        $attributes = $this->collectIframeAttributes($width, $height, $options);

        return sprintf(
            '<iframe src="%s"%s></iframe>',
            htmlspecialchars($src, ENT_QUOTES | ENT_HTML5),
            empty($attributes) ? '' : ' ' . $this->implodeAttributes($attributes)
        );
    }


    /**
     * @param array $options
     * @param FileInterface $file
     * @return string
     */
    protected function createWhatchadoUrl(array $options, FileInterface $file)
    {
        $fileContent = explode('|', $this->getVideoIdFromFile($file));
        $videoId = $fileContent[0];
        //default language is probably de
        $language = $fileContent[1] ?? 'de';

        $urlParams = [];

        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=true';
        }

        return sprintf(
            'https://www.whatchado.com/%s/embeds/videos/%s?%s',
            $language,
            rawurlencode($videoId),
            implode('&', $urlParams)
        );
    }

    /**
     * @param FileInterface $file
     * @return string
     */
    protected function getVideoIdFromFile(FileInterface $file)
    {
        if ($file instanceof FileReference) {
            $orgFile = $file->getOriginalFile();
        } else {
            $orgFile = $file;
        }

        return $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);
    }

    /**
     * Get online media helper
     *
     * @param FileInterface $file
     * @return bool|OnlineMediaHelperInterface
     */
    protected function getOnlineMediaHelper(FileInterface $file)
    {
        if ($this->onlineMediaHelper === null) {
            $orgFile = $file;
            if ($orgFile instanceof FileReference) {
                $orgFile = $orgFile->getOriginalFile();
            }
            if ($orgFile instanceof File) {
                $this->onlineMediaHelper = OnlineMediaHelperRegistry::getInstance()->getOnlineMediaHelper($orgFile);
            } else {
                $this->onlineMediaHelper = false;
            }
        }
        return $this->onlineMediaHelper;
    }

    /**
     * @param int|string $width
     * @param int|string $height
     * @param array $options
     * @return array pairs of key/value; not yet html-escaped
     */
    protected function collectIframeAttributes($width, $height, array $options)
    {
        $attributes = [];
        $attributes['allowfullscreen'] = true;

        if (isset($options['additionalAttributes']) && is_array($options['additionalAttributes'])) {
            $attributes = array_merge($attributes, $options['additionalAttributes']);
        }
        if (isset($options['data']) && is_array($options['data'])) {
            array_walk($options['data'], function (&$value, $key) use (&$attributes) {
                $attributes['data-' . $key] = $value;
            });
        }
        if ((int)$width > 0) {
            $attributes['width'] = (int)$width;
        }
        if ((int)$height > 0) {
            $attributes['height'] = (int)$height;
        }
        if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE']) && (isset($GLOBALS['TSFE']->config['config']['doctype']) && $GLOBALS['TSFE']->config['config']['doctype'] !== 'html5')) {
            $attributes['frameborder'] = 0;
        }
        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'poster', 'preload', 'allow'] as $key) {
            if (!empty($options[$key])) {
                $attributes[$key] = $options[$key];
            }
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @return string
     * @internal
     */
    protected function implodeAttributes(array $attributes): string
    {
        $attributeList = [];
        foreach ($attributes as $name => $value) {
            $name = preg_replace('/[^\p{L}0-9_.-]/u', '', $name);
            if ($value === true) {
                $attributeList[] = $name;
            } else {
                $attributeList[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . '"';
            }
        }
        return implode(' ', $attributeList);
    }

    /**
     * @param array $options
     * @param FileInterface $file
     * @return array
     */
    protected function collectOptions(array $options, FileInterface $file)
    {
        if (!isset($options['autoplay']) && $file instanceof FileReference) {
            $autoplay = $file->getProperty('autoplay');
            if ($autoplay !== null) {
                $options['autoplay'] = $autoplay;
            }
        }

        //todo: Are there more options that can be added?

        return $options;
    }
}
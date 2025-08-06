<?php

namespace TRAW\Whatchado\Resource\Rendering;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class WhatchadoRenderer
 */
class WhatchadoRenderer implements FileRendererInterface
{
    /**
     * @var OnlineMediaHelperInterface
     */
    protected $onlineMediaHelper;

    public function getPriority(): int
    {
        return 1;
    }

    public function canRender(FileInterface $file): bool
    {
        return ($file->getMimeType() === 'video/whatchado' || $file->getExtension() === 'whatchado') && $this->getOnlineMediaHelper($file) !== false;
    }

    /**
     * @param int|string $width
     * @param int|string $height
     * @param false $usedPathsRelativeToCurrentScript
     * @return string|void
     */
    public function render(FileInterface $file, $width, $height, array $options = []): string
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

    protected function createWhatchadoUrl(array $options, FileInterface $file): string
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
            'https://www.whatchado.com/%s/embeds/videos/%s%s',
            $language,
            rawurlencode($videoId),
            '?' . implode('&', $urlParams)
        );
    }

    /**
     * @return string
     */
    protected function getVideoIdFromFile(FileInterface $file)
    {
        $orgFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;

        return $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);
    }

    /**
     * Get online media helper
     *
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
                $this->onlineMediaHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)->getOnlineMediaHelper($orgFile);
            } else {
                $this->onlineMediaHelper = false;
            }
        }

        return $this->onlineMediaHelper;
    }

    /**
     * @param int|string $width
     * @param int|string $height
     * @return array pairs of key/value; not yet html-escaped
     */
    protected function collectIframeAttributes($width, $height, array $options): array
    {
        $attributes = [];
        $attributes['allowfullscreen'] = true;

        if (isset($options['additionalAttributes']) && is_array($options['additionalAttributes'])) {
            $attributes = array_merge($attributes, $options['additionalAttributes']);
        }

        if (isset($options['data']) && is_array($options['data'])) {
            array_walk($options['data'], function (&$value, string $key) use (&$attributes): void {
                $attributes['data-' . $key] = $value;
            });
        }

        if ((int)$width > 0) {
            $attributes['width'] = (int)$width;
        }

        if ((int)$height > 0) {
            $attributes['height'] = (int)$height;
        }

        if ($this->shouldIncludeFrameBorderAttribute()) {
            $attributes['frameborder'] = 0;
        }

        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'poster', 'preload', 'allow'] as $key) {
            if (!empty($options[$key])) {
                $attributes[$key] = $options[$key];
            }
        }

        return $attributes;
    }

    protected function shouldIncludeFrameBorderAttribute(): bool
    {
        return GeneralUtility::makeInstance(PageRenderer::class)->getDocType()->shouldIncludeFrameBorderAttribute();
    }

    /**
     * @internal
     */
    protected function implodeAttributes(array $attributes): string
    {
        $attributeList = [];
        foreach ($attributes as $name => $value) {
            $name = preg_replace('/[^\p{L}0-9_.-]/u', '', $name);
            $attributeList[] = $value === true ? $name : $name . '="' . htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5) . '"';
        }

        return implode(' ', $attributeList);
    }

    protected function collectOptions(array $options, FileInterface $file): array
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

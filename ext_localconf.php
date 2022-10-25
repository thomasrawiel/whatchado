<?php
defined('TYPO3') or die('Access denied.');
call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['whatchado']
        = \TRAW\Whatchado\Helpers\WhatchadoHelper::class;
    $rendererRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::class);
    $rendererRegistry->registerRendererClass(
        \TRAW\Whatchado\Resource\Rendering\WhatchadoRenderer::class
    );

    // Register custom mime-type for whatchado-videos
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['FileInfo']['fileExtensionToMimeType']['whatchado'] = 'video/whatchado';

    // Register custom file extension as allowed media file
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',whatchado';
});
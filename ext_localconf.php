<?php
defined('TYPO3_MODE') or die('Access denied.');
call_user_func(function ($_EXTKEY) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['whatchado']
        = \TRAW\Whatchado\Helpers\WhatchadoHelper::class;

    $rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
    $rendererRegistry->registerRendererClass(
        \TRAW\Whatchado\Resource\Rendering\WhatchadoRenderer::class
    );

    // Register custom mime-type for whatchado-videos
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['FileInfo']['fileExtensionToMimeType']['whatchado'] = 'video/whatchado';

    // Register custom file extension as allowed media file
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',whatchado';
}, $_EXTKEY);
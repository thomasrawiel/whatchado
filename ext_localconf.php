<?php
defined('TYPO3_MODE') or die('Access denied.');
call_user_func(function ($_EXTKEY){
// Register your own online video service (the used key is also the bind file extension name)
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['whatchado'] = \TRAW\Whatchado\Helpers\WhatchadoHelper::class;

    $rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
    $rendererRegistry->registerRendererClass(
        \TRAW\Whatchado\Rendering\WhatchadoRenderer::class
    );

// Register an custom mime-type for your videos
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['FileInfo']['fileExtensionToMimeType']['whatchado'] = 'video/whatchado';

// Register your custom file extension as allowed media file
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',whatchado';
}, $_EXTKEY);
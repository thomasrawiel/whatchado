<?php

namespace TRAW\Whatchado\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SettingsUtility
 * @package TRAW\Whatchado\Utility
 */
class SettingsUtility
{

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getExtensionSettings()
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('whatchado');
    }
}
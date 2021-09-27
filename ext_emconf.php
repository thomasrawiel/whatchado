<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$EM_CONF[$_EXTKEY] = [
    'title' => 'Whatchado',
    'description' => 'Embed whatchado videos',
    'category' => 'misc',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [],
        'conflicts' => [],
        'suggests' => []
    ],
];

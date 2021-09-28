<?php

namespace TRAW\Whatchado\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ApiUtility
 * @package TRAW\Whatchado\Utility
 */
class ApiUtility implements SingletonInterface
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var string
     */
    protected $requestURl = '';

    /**
     * @param RequestFactory $requestFactory
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;

        $settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('whatchado');

        if ($settings['whatchadoApiKey'] && $settings['whatchadoApiUrl']) {
            $this->requestURl = $settings['whatchadoApiUrl']
                . '?'
                . 'api_key='
                . $settings['whatchadoApiKey'];
        }
    }

    /**
     * @param File $file
     * @return array|void
     */
    public function fetchMetaData(File $file)
    {
        if ($this->requestURl) {
            $videoId = explode('|', $file->getContents());
            $response = $this->makeGetRequest(
                $this->requestURl . '&path='.$videoId[0].'&language='.$videoId[1]
            );

            if($response) {
                $metaData = json_decode($response, true);

                return [
                    'title' => strip_tags($metaData['data'][0]['title']),
                    'description' => strip_tags($metaData['data'][0]['videoDescription']),
                    'previewImage' => $metaData['data'][0]['posterImageUrl'],
                    'language' => $metaData['data'][0]['videoLanguage']
                ];
            }
        }
    }

    /**
     * @param string $targetUrl
     * @return string|void
     */
    protected function makeGetRequest(string $targetUrl)
    {
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
        ];

        $response = $this->requestFactory->request(
            $targetUrl,
            'GET',
            $additionalOptions
        );
        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        }
    }
}
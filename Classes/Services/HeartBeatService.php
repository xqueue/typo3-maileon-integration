<?php

namespace XQueue\Typo3MaileonIntegration\Services;

use de\xqueue\maileon\api\client\account\AccountService;
use Exception;
use Psr\Log\LoggerAwareTrait;
use stdClass;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use XQueue\Typo3MaileonIntegration\Settings\Settings;

class HeartBeatService
{
    use LoggerAwareTrait;

    protected array $settings;

    public function __construct(array $settings, LogManager $logManager)
    {
        $this->settings = $settings;
        $this->setLogger($logManager->getLogger(__CLASS__));
    }

    public function executeHB(): void
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        try {
            $accountParameters = $this->getAccountParameters();

            if (empty($accountParameters)) {
                return;
            }

            $parameters = [
                'pluginID' => Settings::XSIC_ID,
                'checkSum' => Settings::XSIC_CHECKSUM,
                'accountID' => $accountParameters['accountID'],
                'clientHash' => $accountParameters['clientHash'],
                'event' => 'heartbeat',
            ];

            $uri = Settings::XSIC_URL . '?' . http_build_query($parameters);

            $response = $requestFactory->request($uri, 'GET');

            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 200) {
                return;
            } else {
                $this->logger->warning('HeartBeat process failed with error: ' . $response->getBody()->getContents());
                return;
            }
        } catch (Exception $e) {
            $this->logger->error('HeartBeat process failed with error: ' . $e->getMessage());
            return;
        }
    }

    /**
     * @throws Exception
     */
    protected function getAccountParameters(): array
    {
        $accountInfo = $this->getAccountInfo();

        return [
            'accountID' => $accountInfo->id,
            'clientHash' => $this->createClientHash($accountInfo->name),
        ];
    }

    /**
     * @throws Exception
     */
    protected function getAccountInfo(): stdClass
    {
        $maileonConfig = [
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' => $this->getMaileonApiKey(),
            'TIMEOUT' => 30,
        ];

        $accountService = new AccountService($maileonConfig);
        $response = $accountService->getAccountInfo();

        if (! $response->isSuccess()) {
            throw new Exception('Account info not found! (API key is missing or invalid)');
        }

        return $response->getResult();
    }

    /**
     * Get Maileon Api key from plugin config
     * Default Api key
     *
     * @throws Exception
     */
    protected function getMaileonApiKey(): string
    {
        $apiKey = $this->settings['apiKey'];

        if (empty($apiKey)) {
            throw new Exception('API key is missing or invalid');
        }

        return $apiKey;
    }

    protected function createClientHash(string $accountName): string
    {
        if (empty($accountName)) {
            return '';
        }

        $firstChar = substr($accountName, 0, 1);
        $lastChar = substr($accountName, -1, 1);

        return $firstChar . $lastChar;
    }
}

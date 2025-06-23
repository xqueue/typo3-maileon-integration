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

    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new Exception('API key is missing or invalid');
        }

        $this->apiKey = $apiKey;
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $this->setLogger($logManager->getLogger(__CLASS__));
    }

    public function sendHeartbeat(): void
    {
        try {
            $accountParams = $this->getAccountParameters();
            if (empty($accountParams)) {
                $this->logger->warning('Heartbeat skipped due to missing account parameters.');
                return;
            }

            $uri = Settings::XSIC_URL . '?' . http_build_query([
                    'pluginID'   => Settings::XSIC_ID,
                    'checkSum'   => Settings::XSIC_CHECKSUM,
                    'accountID'  => $accountParams['accountID'],
                    'clientHash' => $accountParams['clientHash'],
                    'event'      => 'heartbeat',
                ]);

            $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
            $response = $requestFactory->request($uri, 'GET');

            if ($response->getStatusCode() !== 200) {
                $this->logger->warning('Heartbeat request failed.', [
                    'status' => $response->getStatusCode(),
                    'response' => $response->getBody()->getContents(),
                ]);
            }
        } catch (Exception $e) {
            $this->logger->error('Heartbeat process failed.', ['exception' => $e]);
        }
    }

    /**
     * @throws Exception
     */
    protected function getAccountParameters(): array
    {
        $account = $this->getAccountInfo();

        return [
            'accountID'  => $account->id,
            'clientHash' => $this->createClientHash($account->name),
        ];
    }

    /**
     * @throws Exception
     */
    protected function getAccountInfo(): stdClass
    {
        $accountService = new AccountService([
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY'  => $this->apiKey,
            'TIMEOUT'  => 30,
        ]);

        $response = $accountService->getAccountInfo();

        if (! $response->isSuccess()) {
            throw new Exception('Failed to retrieve Maileon account info.');
        }

        return $response->getResult();
    }

    /**
     * @throws Exception
     */
    protected function createClientHash(string $accountName): string
    {
        $accountName = trim($accountName);

        if ($accountName === '') {
            throw new Exception('Cannot create client hash: account name is empty.');
        }

        return substr($accountName, 0, 1) . substr($accountName, -1, 1);
    }
}
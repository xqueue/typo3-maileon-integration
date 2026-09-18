<?php

namespace XQueue\Typo3MaileonIntegration\Domain\Finishers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use XQueue\Typo3MaileonIntegration\Services\FormProcessingService;

class MaileonSubscribeFinisher extends AbstractFinisher
{
    /**
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formDefinition = $formRuntime->getFormDefinition();

        $formValues = $this->finisherContext->getFormValues();
        $finisherSettings = $this->parseFinisherSettings();

        try {
            $contactService = GeneralUtility::makeInstance(FormProcessingService::class);
            $contactService->processSubscribeForm($formValues, $formDefinition, $finisherSettings);
        } catch (\Throwable $e) {
            $this->logger->error('Maileon subscription failed.', [
                'email' => $formValues['email'] ?? null,
                'exception' => $e,
            ]);
            throw new FinisherException('Maileon subscription failed: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function parseFinisherSettings(): array
    {
        $options = $this->options;

        return [
            'permission' => $options['permission'] ?? 'none',
            'enableDoiProcess' => filter_var($options['enableDoiProcess'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'finalPermission' => $options['finalPermission'] ?? 'doi+',
            'doiKey' => $options['doiKey'] ?? '',
        ];
    }
}

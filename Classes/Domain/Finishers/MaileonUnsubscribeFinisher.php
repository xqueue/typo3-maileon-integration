<?php

namespace XQueue\Typo3MaileonIntegration\Domain\Finishers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use XQueue\Typo3MaileonIntegration\Services\FormProcessingService;

class MaileonUnsubscribeFinisher extends AbstractFinisher
{
    /**
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formDefinition = $formRuntime->getFormDefinition();
        $formValues = $this->finisherContext->getFormValues();

        try {
            $contactService = GeneralUtility::makeInstance(FormProcessingService::class);
            $contactService->processUnsubscribeForm($formValues, $formDefinition);
        } catch (\Throwable $e) {
            $this->logger->error('Maileon unsubscription failed.', [
                'email' => $formValues['email'] ?? null,
                'exception' => $e,
            ]);
            throw new FinisherException('Maileon unsubscription failed: ' . $e->getMessage(), 0, $e);
        }
    }
}

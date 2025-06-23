<?php

namespace XQueue\Typo3MaileonIntegration\Domain\Finishers;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use XQueue\Typo3MaileonIntegration\Services\FormProcessingService;

class MaileonUnsubscribeFinisher extends AbstractFinisher
{
    /**
     * @throws Exception
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formDefinition = $formRuntime->getFormDefinition();
        $formValues = $this->finisherContext->getFormValues();

        try {
            $contactService = GeneralUtility::makeInstance(FormProcessingService::class);
            $contactService->processUnsubscribeForm($formValues, $formDefinition);
        } catch (Exception $e) {
            throw new Exception('Maileon unsubscription failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
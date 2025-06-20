<?php
namespace XQueue\Typo3MaileonIntegration\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use XQueue\Typo3MaileonIntegration\Domain\Model\Subscription;
use XQueue\Typo3MaileonIntegration\Domain\Repository\XQHbSendRepository;
use XQueue\Typo3MaileonIntegration\Services\FormProcessingService;
use XQueue\Typo3MaileonIntegration\Services\HeartBeatService;

/**
 * SubscribeController
 */
class SubscribeController extends ActionController
{
    protected FormProcessingService $formProcessingService;

    protected XQHbSendRepository $xqHbSendRepository;

    protected HeartBeatService $heartBeatService;

    public function initializeAction(): void
    {
        $logManager = GeneralUtility::makeInstance(LogManager::class);

        $this->formProcessingService = new FormProcessingService($this->settings);
        $this->xqHbSendRepository = GeneralUtility::makeInstance(XQHbSendRepository::class);
        $this->heartBeatService = new HeartBeatService($this->settings, $logManager);
    }
    public function showAction(): ResponseInterface
    {
        $this->view->setTemplatePathAndFilename(
            'EXT:typo3_maileon_integration/Resources/Private/Templates/Subscribe/Subscribe.html'
        );

        $this->assignFormSettingsToView();

        // Render the view and return the response
        $content = $this->view->render();
        return new HtmlResponse($content);
    }
    /**
     * Subscribe action
     *
     * @return ResponseInterface
     */
    public function subscribeAction(): ResponseInterface
    {
        $submittedData = $this->request->getArguments();
        $errors = $this->formProcessingService->validateData($submittedData);
        $this->assignFormSettingsToView();

        if (!empty($errors)) {
            // Return errors and form values back to the view
            $this->view->assign('errors', $errors);
            $this->view->assign('formValues', $submittedData);

            $content = $this->view->render();
            return new HtmlResponse($content);
        }

        try {
            $this->handleHB();
            $response = $this->formProcessingService->trySubscribeContact($submittedData);

            if ($response->isSuccess()) {
                // redirect to result page
                if ($this->settings["targetSuccess"]) {
                    return $this->redirectByPid($this->settings["targetSuccess"]);
                }

                $this->addFlashMessage(
                    LocalizationUtility::translate('subscribe.success', 'typo3_maileon_integration')
                );
                $this->redirect('show');
            } else {
                if ($this->settings["debug"] == 1) {
                    // show debug information
                    DebuggerUtility::var_dump($response->getBodyData());
                } else {
                    // redirect to error page
                    if ($this->settings["targetError"]) {
                        return $this->redirectByPid($this->settings["targetError"]);
                    }

                    $this->addFlashMessage(
                        LocalizationUtility::translate('submit.failed', 'typo3_maileon_integration')
                    );
                }
            }
        } catch (Exception $e) {
            $this->view->assign('errors', $e->getMessage());
        }

        $content = $this->view->render();
        return new HtmlResponse($content);
    }

    /**
     * Unsubscribe action
     */
    public function unsubscribeAction(?Subscription $data = null): ResponseInterface
    {
        if ($data != null) {
            // validate data
            if (null != $data->getEmail()) {
                $response = $this->formProcessingService->tryUnsubscribeContact($data->getEmail());

                // redirect to result page
                if ($response->isSuccess()) {
                    if ($this->settings["targetSuccess"]) {
                        return $this->redirectByPid($this->settings["targetSuccess"]);
                    }

                    $this->addFlashMessage(
                        LocalizationUtility::translate('unsubscribe.success', 'typo3_maileon_integration')
                    );
                } else {
                    if ($this->settings["targetError"]) {
                        return $this->redirectByPid($this->settings["targetError"]);
                    }

                    $this->addFlashMessage(
                        LocalizationUtility::translate('submit.failed', 'typo3_maileon_integration')
                    );
                }
            }
        }

        return $this->htmlResponse();
    }

    /**
     * Redirect by page id
     */
    protected function redirectByPid(int $pid): ResponseInterface
    {
        $uriBuilder = $this->uriBuilder;
        $uriBuilder->reset();
        $uri = $uriBuilder
            ->setTargetPageUid($pid)
            ->build();

        return $this->redirectToUri($uri);
    }

    protected function assignFormSettingsToView(): void
    {
        $standardFields = $this->settings['subscribeForm']['standardFields'] ?? [];
        $customFields = $this->settings['subscribeForm']['customFields'] ?? [];
        $privacyPolicyUrl = $this->settings['privacyPolicyUrl'] ?? '#';

        $activeStandardFields = array_filter($standardFields, function ($field) {
            return $field['active'] ?? false;
        });

        $activeCustomFields = array_filter($customFields, function ($field) {
            return $field['active'] ?? false;
        });

        $this->view->assign('privacyPolicyUrl', $privacyPolicyUrl);
        $this->view->assign('standardFields', $activeStandardFields);
        $this->view->assign('customFields', $activeCustomFields);
    }

    /**
     * @throws InvalidQueryException
     */
    protected function handleHB(): void
    {
        $task = $this->xqHbSendRepository->findByTask('maileon_hb');

        if (empty($task)) {
            $this->heartBeatService->executeHB();
            $this->xqHbSendRepository->updateLastExecution('maileon_hb');
        }

        if (! $this->xqHbSendRepository->hasTaskRunToday('maileon_hb')) {
            $this->heartBeatService->executeHB();
            $this->xqHbSendRepository->updateLastExecution('maileon_hb');
        }
    }
}

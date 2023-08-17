<?php
namespace XQueue\Typo3MaileonIntegration\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use XQueue\Typo3MaileonIntegration\Domain\Model\Subscription;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Contact;

/**
 * SubscribeController
 */
class SubscribeController extends ActionController
{
    /**
     * Subscribe action
     *
     * @param Subscription|null $data
     * @return ResponseInterface
     */
    public function subscribeAction(?Subscription $data = null): ResponseInterface
    {
        if ($data != null) {
            // validate data
            $isValid = true;

            if (false == $data->isApproval()) {
                $isValid = false;
                $this->view->assign("error_approval", true);
                return $this->htmlResponse();
            }

            if (false == $data->isPrivacy()) {
                $isValid = false;
                $this->view->assign("error_privacy", true);
                return $this->htmlResponse();
            }

            if (true == $isValid) {
                // send data via maileon api
                $contactsService = $this->getContactsService();

                // Check if contact exists in Maileon
                $getContactByEmail = $contactsService->getContactByEmail($data->getEmail());

                $newContact = $this->createNewContact($data);

                if ($getContactByEmail->isSuccess()) {
                    $existingContact = $getContactByEmail->getResult();

                    // Check permission of existing customer
                    if (Permission::$NONE == $existingContact->permission) {
                        // Contact does exist without permission - create with doi mailing
                        $response = $contactsService->createContact(
                            $newContact,
                            SynchronizationMode::$UPDATE,
                            'Typo3',
                            'subscriptionForm',
                            true,
                            ($this->settings["targetPermission"] == 5),
                            $this->settings["doiMailingKey"]
                        );
                    } else {
                        // Contact does exist with permission - just update customer data
                        $response = $contactsService->createContact(
                            $newContact,
                            SynchronizationMode::$UPDATE,
                            'Typo3',
                            'subscriptionForm'
                        );
                    }
                } else {
                    // Contact does not exist - create with doi mailing
                    $response = $contactsService->createContact(
                        $newContact,
                        SynchronizationMode::$UPDATE,
                        'Typo3',
                        'subscriptionForm',
                        true,
                        ($this->settings["targetPermission"] == 5),
                        $this->settings["doiMailingKey"]
                    );
                }

                if ($response->isSuccess()) {
                    // redirect to result page
                    if ($this->settings["targetSuccess"]) {
                        return $this->redirectByPid($this->settings["targetSuccess"]);
                    }
                } else {
                    if ($this->settings["debug"] == 1) {
                        // show debug information
                        DebuggerUtility::var_dump($response->getBodyData());
                    } else {
                        // redirect to error page
                        if ($this->settings["targetError"]) {
                            return $this->redirectByPid($this->settings["targetError"]);
                        }
                    }
                }
            } else {
                // return to form and show error
                $this->view->assign("data", $data);
                return $this->htmlResponse();
            }
        }

        return $this->htmlResponse();
    }

    /**
     * Unsubscribe action
     *
     * @param Subscription|null $data
     * @return void
     */
    public function unsubscribeAction(?Subscription $data = null): ResponseInterface
    {
        if ($data != null) {
            // validate data
            if (null != $data->getEmail()) {
                // send data via maileon api
                $contactsService = $this->getContactsService();
                $response = $contactsService->unsubscribeContactByEmail($data->getEmail());

                // redirect to result page
                if ($response->isSuccess()) {
                    if ($this->settings["targetSuccess"]) {
                        return $this->redirectByPid($this->settings["targetSuccess"]);
                    }
                } else {
                    if ($this->settings["targetError"]) {
                        return $this->redirectByPid($this->settings["targetError"]);
                    }
                }
            }
        }

        return $this->htmlResponse();
    }

    /**
     * Redirect by page id
     *
     * @param integer $pid
     * @return ResponseInterface
     */
    private function redirectByPid(int $pid): ResponseInterface
    {
        $uriBuilder = $this->uriBuilder;
        $uriBuilder->reset();
        $uri = $uriBuilder
        ->setTargetPageUid($pid)
        ->build();

        return $this->redirectToUri($uri);
    }

    /**
     * Returns contacts service from maileon api
     *
     * @return ContactsService
     */
    private function getContactsService()
    {
        // Set the global configuration for accessing the REST-API
        $config = array(
          "BASE_URI" => "https://api.maileon.com/1.0",
          "API_KEY" => $this->settings["apiKey"],
          "TIMEOUT" => 30,
          "DEBUG" => "false" // NEVER enable on production
        );

        $contactsService = new ContactsService($config);
        $contactsService->setDebug(false);

        return $contactsService;
    }

    /**
     * Create Contact obj
     *
     * @param Subscription $data
     * @return Contact
     */
    private function createNewContact(Subscription $data)
    {
        $newContact = new Contact();
        $newContact->anonymous = false;

        $newContact->email = $data->getEmail();

        // Standard and custom fields
        $standard_fields = $this->extractMaileonStandardFields($data);
        $custom_fields = $this->extractMaileonCustomFields($data);

        $newContact->standard_fields = $standard_fields;
        $newContact->custom_fields = $custom_fields;

        if (!empty($newContact->custom_fields)) {
            $this->checkAndCreateCustomFields($newContact->custom_fields);
        }

        return $newContact;
    }

    /**
     * Get array for standard fields
     *
     * @param Subscription $data
     * @return array
     */
    private function extractMaileonStandardFields(Subscription $data)
    {
        return [
            "SALUTATION" => $data->getSalutation(),
            "FIRSTNAME" => $data->getFirstname(),
            "LASTNAME" => $data->getLastname(),
            "ORGANIZATION" => $data->getOrganization()
        ];
    }

    /**
     * Get array for custom fields
     *
     * @param Subscription $data
     * @return array
     */
    private function extractMaileonCustomFields(Subscription $data)
    {
        return [
            "Position" => $data->getPosition(),
            "SubscriptionNumber" => $data->getSubscriptionnumber(),
            "Typo3_created" => true
        ];
    }

    /**
     * Check the custom fields exist at Maileon. If not create it.
     *
     * @param array $customFields
     * @return boolean
     */
    private function checkAndCreateCustomFields(array $customFields): bool
    {
        $contactsService = $this->getContactsService();

        $customFieldsResponse = $contactsService->getCustomFields();
        $customFieldsFromMaileon = $customFieldsResponse->getResult();

        if (!array_key_exists('Typo3_created', $customFieldsFromMaileon->custom_fields)) {
            $contactsService->createCustomField('Typo3_created', 'boolean');
        }

        foreach ($customFields as $fieldName => $fieldValue) {
            if (!array_key_exists($fieldName, $customFieldsFromMaileon->custom_fields)) {
                $contactsService->createCustomField($fieldName, 'string');
            }
        }

        return true;
    }
}

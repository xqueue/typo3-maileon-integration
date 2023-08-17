<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\AbstractMaileonService;
use de\xqueue\maileon\api\client\MaileonAPIResult;

/**
 *
 *
 * @author Viktor Balogh (Wiera)
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class ReportsService extends AbstractMaileonService
{
    
    

    /**
     * Returns a page of openers.
     *
     * @param integer $fromDate
     *  If provided, only the openers after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the openers before the given date will be returned.
     *  The value of to_date must be a numeric value representing a point in time
     *  milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the openers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the openers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the openers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the openers by external ids. Each value must correspond
     *  to a contacts external id.
     * @param string $formatFilter
     *  Filters the opens by format. Possible values are: html / text.
     * @param array $socialNetworkFilter
     *  Multivalued parameter to filter the opens by the social networks where they occurred.
     * @param array $deviceTypeFilter
     *  Multivalued parameter to filter the opens by device type. Possible values for
     *  device_type are: UNKNOWN / COMPUTER / TABLET / MOBILE
     * @param bool $embedEmailClientInfos
     *  If the set to true, available email client details will be appended to each open.
     * @param bool $excludeAnonymousOpens
     *  If this is set to true (default), only openers that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been
     *  backed up for mailings because of a backup instruction. For each unsubscription, the corresponding
     *  field backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of
     *  page_size must be in the range 1 to 1000.
     * @param bool $embedTransactionId
     *  If the set to true, the attribut "transaction_id" of a transaction will be returned that caused this open, if available.
     * @param bool $embedContactHash
     *  If the set to true, anonymized contacts will be annotated with a random number that is the same for a contact within each sendout.
     *  With this flag, it is possible to calculate unique opens from all opens, even if contacts are unsubscribed and therefore anonymized.
     *  If in two opens of the same sendout the contact hash is the same, then the same contact opened twice. 
     *  In different mails the same contact hash might occur on an open but will most probably not belong to the same (anonymized) contact.
     * @return MaileonAPIResult
     */
    public function getOpens(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $formatFilter = null,
        $socialNetworkFilter = null,
        $deviceTypeFilter = null,
        $embedEmailClientInfos = false,
        $excludeAnonymousOpens = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100,
        $embedTransactionId = false,
        $embedContactHash = false
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);
        if (isset($embedEmailClientInfos)) {
            $params['embed_email_client_infos'] = ($embedEmailClientInfos == true) ? "true" : "false";
        }
        if (isset($excludeAnonymousOpens)) {
            $params['exclude_anonymous_opens'] = ($excludeAnonymousOpens == true) ? "true" : "false";
        }

        if (isset($formatFilter)) {
            $params['format'] = $formatFilter;
        }
        if (isset($embedTransactionId)) {
            $params['embed_transaction_id'] = ($embedTransactionId == true) ? "true" : "false";
        }
        if (isset($embedContactHash)) {
            $params['embed_contact_hash'] = ($embedContactHash == true) ? "true" : "false";
        }
        $params = $this->appendArrayFields($params, "social_network", $socialNetworkFilter);
        $params = $this->appendArrayFields($params, "device_type", $deviceTypeFilter);

        return $this->get('reports/opens', $params);
    }

    /**
     * Returns a page of unique openers.
     *
     * @param integer $fromDate
     *  If provided, only the openers after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the openers before the given date will be returned.
     *  The value of to_date must be a numeric value representing a point in time
     *  milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the openers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the openers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the openers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the openers by external ids. Each value must
     *  correspond to a contacts external id.
     * @param bool $embedEmailClientInfos
     *  If the set to true, available email client details will be appended to each open.
     * @param bool $excludeAnonymousOpens
     *  If this is set to true (default), only openers that have not yet been
     *  anonymized (due to deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have
     *  been backed up for mailings because of a backup instruction. For each unsubscription,
     *  the corresponding field backups will be returned if available. Note that this only
     *  applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size
     *  must be in the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getUniqueOpens(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $embedEmailClientInfos = false,
        $excludeAnonymousOpens = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);
        if (isset($embedEmailClientInfos)) {
            $params['embed_email_client_infos'] = ($embedEmailClientInfos == true) ? "true" : "false";
        }
        if (isset($excludeAnonymousOpens)) {
            $params['exclude_anonymous_opens'] = ($excludeAnonymousOpens == true) ? "true" : "false";
        }

        return $this->get('reports/opens/unique', $params);
    }

    /**
     * Count openers.
     *
     * @param integer $fromDate
     *  If provided, only the openers after the given date will be returned.
     *  The value of from_date must be a numeric value representing a point in time
     *  milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the openers before the given date will be returned.
     *  The value of to_date must be a numeric value representing a point in time
     *  milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the openers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the openers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the openers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the openers by external ids. Each value must
     *  correspond to a contacts external id.
     * @param string $formatFilter
     *  Filters the opens by format. Possible values are: html / text.
     * @param array $socialNetworkFilter
     *  Multivalued parameter to filter the opens by the social networks where they occurred.
     * @param array $deviceTypeFilter
     *  Multivalued parameter to filter the opens by device type. Possible values for
     *  device_type are: UNKNOWN / COMPUTER / TABLET / MOBILE
     * @param bool $excludeAnonymousOpens
     *  If this is set to true (default), only openers that have not yet been
     *  anonymized (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getOpensCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $formatFilter = null,
        $socialNetworkFilter = null,
        $deviceTypeFilter = null,
        $excludeAnonymousOpens = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousOpens)) {
            $params['exclude_anonymous_opens'] = ($excludeAnonymousOpens == true) ? "true" : "false";
        }

        if (isset($formatFilter)) {
            $params['format'] = $formatFilter;
        }
        $params = $this->appendArrayFields($params, "social_network", $socialNetworkFilter);
        $params = $this->appendArrayFields($params, "device_type", $deviceTypeFilter);

        return $this->get('reports/opens/count', $params);
    }

    /**
     * Count unique openers.
     *
     * @param integer $fromDate
     *  If provided, only the openers after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds
     *  afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the openers before the given date will be returned. The value
     *  of to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the openers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the openers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the openers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the openers by external ids. Each value
     *  must correspond to a contacts external id.
     * @param bool $excludeAnonymousOpens
     *  If this is set to true (default), only openers that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getUniqueOpensCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $excludeAnonymousOpens = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousOpens)) {
            $params['exclude_anonymous_opens'] = ($excludeAnonymousOpens == true) ? "true" : "false";
        }

        return $this->get('reports/opens/unique/count', $params);
    }

    /**
     * Returns a page of recipients.
     *
     * @param integer $fromDate
     *  If provided, only the recipients after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the recipients before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the recipients by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the recipients by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the recipients by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the recipients by external ids. Each value must
     *  correspond to a contacts external id.
     * @param bool $excludeDeletedRecipients
     *  Supported values: true / false. If set to true, the recipients that have been removed from
     *  maileon will be excluded.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have
     *  been backed up for mailings because of a backup instruction. For each unsubscription, the
     *  corresponding field backups will be returned if available. Note that this only applies for
     *  non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of
     *  page_size must be in the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getRecipients(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $excludeDeletedRecipients = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100,
        $embedTransactionId = false
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );
        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);
        if (isset($excludeDeletedRecipients)) {
            $params['exclude_deleted_recipients'] = ($excludeDeletedRecipients == true) ? "true" : "false";
        }
        if (isset($embedTransactionId)) {
            $params['embed_transaction_id'] = ($embedTransactionId == true) ? "true" : "false";
        }

        return $this->get('reports/recipients', $params);
    }

    /**
     * Count recipients.
     *
     * @param integer $fromDate
     *  If provided, only the recipients after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the recipients before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the recipients by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the recipients by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the recipients by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the recipients by external ids. Each value must
     *  correspond to a contacts external id.
     * @param bool $excludeDeletedRecipients
     *  Supported values: true / false. If set to true, the recipients that have been
     *  removed from maileon will be excluded.
     * @return MaileonAPIResult
     */
    public function getRecipientsCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $excludeDeletedRecipients = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeDeletedRecipients)) {
            $params['exclude_deleted_recipients'] = ($excludeDeletedRecipients == true) ? "true" : "false";
        }

        return $this->get('reports/recipients/count', $params);
    }


    /**
     * Returns a page of clickers.
     *
     * @param integer $fromDate
     *  If provided, only the clickers after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the clickers before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the clickers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the clickers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the clickers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the clickers by external ids. Each value must correspond
     *  to a contacts external id.
     * @param string $formatFilter
     *  Filters the opens by format. Possible values are: html / text.
     * @param array $linkIdFilter
     *  Multivalued parameter to filter the clicks by links. Each value must correspond to a link id.
     * @param string $linkUrlFilter
     *  Filters the clicks by link url.
     * @param array $linkTagFilter
     *  Multivalued parameter to filter the clicks by tags associated to the links.
     * @param array $socialNetworkFilter
     *  Multivalued parameter to filter the opens by the social networks where they occurred.
     * @param array $deviceTypeFilter
     *  Multivalued parameter to filter the opens by device type. Possible values for device_type
     *  are: UNKNOWN / COMPUTER / TABLET / MOBILE
     * @param bool $embedEmailClientInfos
     *  If the set to true, available email client details will be appended to each open.
     * @param bool $excludeAnonymousClicks
     *  If this is set to true (default), only clicks that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been backed
     *  up for mailings because of a backup instruction. For each unsubscription, the corresponding field
     *  backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be in
     *  the range 1 to 1000.
     * @param bool $embedLinkTags
     *  If the set to true, available link tags will be appended to each click.
     * @param bool $embedTransactionId
     *  If the set to true, the attribut "transaction_id" of a transaction will be returned that caused this click, if available.
     * @param bool $embedContactHash
     *  If the set to true, anonymized contacts will be annotated with a random number that is the same for a contact within each sendout.
     *  With this flag, it is possible to calculate unique clicks from all clicks, even if contacts are unsubscribed and therefore anonymized.
     *  If in two clicks of the same sendout the contact hash is the same, then the same contact clicked twice. 
     *  In different mails the same contact hash might occur on a click but will most probably not belong to the same (anonymized) contact.
     * @return MaileonAPIResult
     */
    public function getClicks(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $formatFilter = null,
        $linkIdFilter = null,
        $linkUrlFilter = null,
        $linkTagFilter = null,
        $socialNetworkFilter = null,
        $deviceTypeFilter = null,
        $embedEmailClientInfos = false,
        $excludeAnonymousClicks = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100,
        $embedLinkTags = false,
        $embedTransactionId = false,
        $embedContactHash = false
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);
        if (isset($embedEmailClientInfos)) {
            $params['embed_email_client_infos'] = ($embedEmailClientInfos == true) ? "true" : "false";
        }
        if (isset($embedLinkTags)) {
            $params['embed_link_tags'] = ($embedLinkTags == true) ? "true" : "false";
        }
        if (isset($excludeAnonymousClicks)) {
            $params['exclude_anonymous_clicks'] = ($excludeAnonymousClicks == true) ? "true" : "false";
        }

        if (isset($formatFilter)) {
            $params['format'] = $formatFilter;
        }
        $params = $this->appendArrayFields($params, "link_id", $linkIdFilter);
        if (isset($linkUrlFilter)) {
            $params['link_url'] = $linkUrlFilter;
        }
        if (isset($embedTransactionId)) {
            $params['embed_transaction_id'] = ($embedTransactionId == true) ? "true" : "false";
        }
        if (isset($embedContactHash)) {
            $params['embed_contact_hash'] = ($embedContactHash == true) ? "true" : "false";
        }
        $params = $this->appendArrayFields($params, "link_tag", $linkTagFilter);
        $params = $this->appendArrayFields($params, "social_network", $socialNetworkFilter);
        $params = $this->appendArrayFields($params, "device_type", $deviceTypeFilter);

        return $this->get('reports/clicks', $params);
    }

    /**
     * Returns a page of unique clickers.
     *
     * @param integer $fromDate
     *  If provided, only the clickers after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the clickers before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the clickers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the clickers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the clickers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the clickers by external ids. Each value must correspond
     *  to a contacts external id.
     * @param bool $embedEmailClientInfos
     *  If the set to true, available email client details will be appended to each open.
     * @param bool $excludeAnonymousClicks
     *  If this is set to true (default), only clicks that have not yet been anonymized (due to
     *  deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been backed
     *  up for mailings because of a backup instruction. For each unsubscription, the corresponding field
     *  backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be in the
     *  range 1 to 1000.
     * @param bool $embedLinkTags
     *  If the set to true, available link tags will be appended to each click.
     * @return MaileonAPIResult
     */
    public function getUniqueClicks(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $embedEmailClientInfos = false,
        $excludeAnonymousClicks = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100,
        $embedLinkTags = false
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);
        if (isset($embedEmailClientInfos)) {
            $params['embed_email_client_infos'] = ($embedEmailClientInfos == true) ? "true" : "false";
        }
        if (isset($embedLinkTags)) {
            $params['embed_link_tags'] = ($embedLinkTags == true) ? "true" : "false";
        }
        if (isset($excludeAnonymousClicks)) {
            $params['exclude_anonymous_clicks'] = ($excludeAnonymousClicks == true) ? "true" : "false";
        }

        return $this->get('reports/clicks/unique', $params);
    }


    /**
     * Count clickers.
     *
     * @param integer $fromDate
     *  If provided, only the clickers after the given date will be returned. The value of from_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the clickers before the given date will be returned. The value of to_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the clickers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the clickers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the clickers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the clickers by external ids. Each value must correspond to a contacts
     *  external id.
     * @param string $formatFilter
     *  Filters the opens by format. Possible values are: html / text.
     * @param array $linkIdFilter
     *  Multivalued parameter to filter the clicks by links. Each value must correspond to a link id.
     * @param string $linkUrlFilter
     *  Filters the clicks by link url.
     * @param array $linkTagFilter
     *  Multivalued parameter to filter the clicks by tags associated to the links.
     * @param array $socialNetworkFilter
     *  Multivalued parameter to filter the opens by the social networks where they occurred.
     * @param array $deviceTypeFilter
     *  Multivalued parameter to filter the opens by device type. Possible values for device_type
     *  are: UNKNOWN / COMPUTER / TABLET / MOBILE
     * @param bool $excludeAnonymousClicks
     *  If this is set to true (default), only clicks that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getClicksCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $formatFilter = null,
        $linkIdFilter = null,
        $linkUrlFilter = null,
        $linkTagFilter = null,
        $socialNetworkFilter = null,
        $deviceTypeFilter = null,
        $excludeAnonymousClicks = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousClicks)) {
            $params['exclude_anonymous_clicks'] = ($excludeAnonymousClicks == true) ? "true" : "false";
        }

        if (isset($formatFilter)) {
            $params['format'] = $formatFilter;
        }
        $params = $this->appendArrayFields($params, "link_id", $linkIdFilter);
        if (isset($linkUrlFilter)) {
            $params['link_url'] = $linkUrlFilter;
        }
        $params = $this->appendArrayFields($params, "link_tag", $linkTagFilter);
        $params = $this->appendArrayFields($params, "social_network", $socialNetworkFilter);
        $params = $this->appendArrayFields($params, "device_type", $deviceTypeFilter);

        return $this->get('reports/clicks/count', $params);
    }

    /**
     * Count unique clickers.
     *
     * @param integer $fromDate
     *  If provided, only the clickers after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the clickers before the given date will be returned. The value of to_date must be a
     *  numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the clickers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the clickers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the clickers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the clickers by external ids. Each value must correspond to a
     *  contacts external id.
     * @param bool $excludeAnonymousClicks
     *  If this is set to true (default), only clicks that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getUniqueClicksCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $excludeAnonymousClicks = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousClicks)) {
            $params['exclude_anonymous_clicks'] = ($excludeAnonymousClicks == true) ? "true" : "false";
        }

        return $this->get('reports/clicks/unique/count', $params);
    }

    /**
     * Returns a page of bouncers.
     *
     * @param integer $fromDate
     *  If provided, only the bouncers after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the bouncers before the given date will be returned. The value of to_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the bouncers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the bouncers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the bouncers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the bouncers by external ids. Each value must correspond
     *  to a contacts external id.
     * @param array $statusCodeFilter
     *  Filters the bounces by status codes. Status codes follow the pattern digit.digit.digit (example: 5.0.0).
     * @param string $typeFilter
     *  Filters the bounces by type: permanent / transient.
     * @param string $sourceFilter
     *  Filters the bounces by their source: mta-listener / reply.
     * @param bool $excludeAnonymousBounces
     *  If this is set to true (default), only bounces that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been
     *  backed up for mailings because of a backup instruction. For each unsubscription, the corresponding
     *  field backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size
     *  must be in the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getBounces(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $statusCodeFilter = null,
        $typeFilter = null,
        $sourceFilter = null,
        $excludeAnonymousBounces = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );
        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);

        if (isset($excludeAnonymousBounces)) {
            $params['exclude_anonymous_bounces'] = ($excludeAnonymousBounces == true) ? "true" : "false";
        }

        if (isset($typeFilter)) {
            $params['type'] = $typeFilter;
        }
        if (isset($sourceFilter)) {
            $params['source_filter'] = $sourceFilter;
        }

        return $this->get('reports/bounces', $params);
    }

    /**
     * Returns a page of unique bouncers.
     *
     * @param integer $fromDate
     *  If provided, only the bouncers after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the bouncers before the given date will be returned. The value of to_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the bouncers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the bouncers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the bouncers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the bouncers by external ids. Each value must correspond to a
     *  contacts external id.
     * @param bool $excludeAnonymousBounces
     *  If this is set to true (default), only bounces that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been backed
     *  up for mailings because of a backup instruction. For each unsubscription, the corresponding field
     *  backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be in
     *  the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getUniqueBounces(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $excludeAnonymousBounces = false,
        $standardFields = null,
        $customFields = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);

        if (isset($excludeAnonymousBounces)) {
            $params['exclude_anonymous_bounces'] = ($excludeAnonymousBounces == true) ? "true" : "false";
        }

        return $this->get('reports/bounces/unique', $params);
    }

    /**
     * Count bouncers.
     *
     * @param integer $fromDate
     *  If provided, only the bouncers after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the bouncers before the given date will be returned. The value of to_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the bouncers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the bouncers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the bouncers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the bouncers by external ids. Each value must correspond
     *  to a contacts external id.
     * @param array $statusCodeFilter
     *  Filters the bounces by status codes. Status codes follow the pattern digit.digit.digit (example: 5.0.0).
     * @param string $typeFilter
     *  Filters the bounces by type: permanent / transient.
     * @param string $sourceFilter
     *  Filters the bounces by their source: mta-listener / reply.
     * @param bool $excludeAnonymousBounces
     *  If this is set to true (default), only bounces that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getBouncesCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $statusCodeFilter = null,
        $typeFilter = null,
        $sourceFilter = null,
        $excludeAnonymousBounces = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousBounces)) {
            $params['exclude_anonymous_bounces'] = ($excludeAnonymousBounces == true) ? "true" : "false";
        }

        if (isset($typeFilter)) {
            $params['type'] = $typeFilter;
        }
        if (isset($sourceFilter)) {
            $params['source_filter'] = $sourceFilter;
        }

        return $this->get('reports/bounces/count', $params);
    }

    /**
     * Count unique bouncers.
     *
     * @param integer $fromDate
     *  If provided, only the bouncers after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the bouncers before the given date will be returned. The value of to_date must be
     *  a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the bouncers by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the bouncers by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the bouncers by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the bouncers by external ids. Each value must correspond to a
     *  contacts external id.
     * @param bool $excludeAnonymousBounces
     *  If this is set to true (default), only bounces that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getUniqueBouncesCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $excludeAnonymousBounces = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousBounces)) {
            $params['exclude_anonymous_bounces'] = ($excludeAnonymousBounces == true) ? "true" : "false";
        }

        return $this->get('reports/bounces/unique/count', $params);
    }

    /**
     * Returns a page of blocks.
     *
     * @param integer $fromDate
     *  If provided, only the blocks after the given date will be returned. The value of from_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the blocks before the given date will be returned. The value of to_date must be
     *  a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $contactIds
     *  Multivalued parameter to filter the blocks by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the blocks by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the blocks by external ids. Each value must correspond to a
     *  contacts external id.
     * @param array $reasons
     *  Filter by reason, valid: blacklist, bounce_policy.
     * @param string $oldStatus
     *  Filter by old status, valid: allowed, blocked.
     * @param string $newStatus
     *  Filter by new status, valid: allowed, blocked.
     * @param bool $excludeAnonymousBlocks
     *  If this is set to true (default), only bounces that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be
     *  in the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getBlocks(
        $fromDate = null,
        $toDate = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $reasons = null,
        $oldStatus = null,
        $newStatus = null,
        $excludeAnonymousBlocks = false,
        $standardFields = null,
        $customFields = null,
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            null,
            null,
            null
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);

        if (isset($excludeAnonymousBlocks)) {
            $params['exclude_anonymous_blocks'] = ($excludeAnonymousBlocks == true) ? "true" : "false";
        }

        $params = $this->appendArrayFields($params, "reasons", $reasons);
        if (isset($oldStatus)) {
            $params['old_status'] = $oldStatus;
        }
        if (isset($newStatus)) {
            $params['new_status'] = $newStatus;
        }

        return $this->get('reports/blocks', $params);
    }

    /**
     * Count blocks.
     *
     * @param integer $fromDate
     *  If provided, only the blocks after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the blocks before the given date will be returned. The value of to_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $contactIds
     *  Multivalued parameter to filter the blocks by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the blocks by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the blocks by external ids. Each value must correspond to a
     *  contacts external id.
     * @param array $reasons
     *  Filter by reason, valid: blacklist, bounce_policy.
     * @param string $oldStatus
     *  Filter by old status, valid: allowed, blocked.
     * @param string $newStatus
     *  Filter by new status, valid: allowed, blocked.
     *  Filters the bounces by their source: mta-listener / reply.
     * @param bool $excludeAnonymousBlocks
     *  If this is set to true (default), only bounces that have not yet been anonymized
     *  (due to deletion/unsubscription) are returned.
     * @return MaileonAPIResult
     */
    public function getBlocksCount(
        $fromDate = null,
        $toDate = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $reasons = null,
        $oldStatus = null,
        $newStatus = null,
        $excludeAnonymousBlocks = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            null,
            null
        );

        if (isset($excludeAnonymousBlocks)) {
            $params['exclude_anonymous_blocks'] = ($excludeAnonymousBlocks == true) ? "true" : "false";
        }

        $params = $this->appendArrayFields($params, "reasons", $reasons);
        if (isset($oldStatus)) {
            $params['old_status'] = $oldStatus;
        }
        if (isset($newStatus)) {
            $params['new_status'] = $newStatus;
        }

        return $this->get('reports/blocks/count', $params);
    }

    /**
     * Returns a page of unsubscriberss.
     *
     * @param integer $fromDate
     *   If provided, only the unsubscriptions after the given date will be returned.
     *   The value of from_date must be a numeric value representing a point in time milliseconds
     *   afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unsubscriptions before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the unsubscriptions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the unsubscriptions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unsubscriptions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unsubscriptions by external ids.
     *  Each value must correspond to a contacts external id.
     * @param string $source
     *  Filters the unsubscriptions by their source. The source can be an
     *  unsubscription link (link), a reply mail (reply) or other.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been
     *  backed up for mailings because of a backup instruction. For each unsubscription, the corresponding
     *  field backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size
     *  must be in the range 1 to 1000.
     * @param array $standardFields
     *  The list of standard contact fields to return. Please note, that those values are only available if 
     *  Maileon is set up to move those values to unsubscriber table on unsubscription.
     * @param array $customFields
     *  The list of custom contact fields to return. Please note, that those values are only available if 
     *  Maileon is set up to move those values to unsubscriber table on unsubscription.
     * @param bool $exclude_anonymous
     *  If this is set to true, only unsubscribers that have not yet been anonymized (due to setting) are returned.
     *
     * @return MaileonAPIResult
     */
    public function getUnsubscribers(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $source = null,
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100,
        $standardFields = null,
        $customFields = null,
        $excludeAnonymous = false
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            $source,
            $embedFieldBackups
        );

        if (isset($excludeAnonymous)) {
            $params['exclude_anonymous'] = ($excludeAnonymous == true) ? "true" : "false";
        }
        
        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);

        return $this->get('reports/unsubscriptions', $params);
    }

    /**
     * Count unsubscribers.
     *
     * @param integer $fromDate
     *  If provided, only the unsubscriptions after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unsubscriptions before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the unsubscriptions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the unsubscriptions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unsubscriptions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unsubscriptions by external ids. Each value must correspond
     *  to a contacts external id.
     * @param string $source
     *  Filters the unsubscriptions by their source. The source can be an unsubscription link
     *  (link), a reply mail (reply) or other.
     * @param bool $exclude_anonymous
     *  If this is set to true, only unsubscribers that have not yet been anonymized (due to setting) are returned.
     *
     * @return MaileonAPIResult
     */
    public function getUnsubscribersCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = null,
        $contactIds = null,
        $contactEmails = null,
        $contactExternalIds = null,
        $source = null,
        $excludeAnonymous = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            $source
        );

        if (isset($excludeAnonymous)) {
            $params['exclude_anonymous'] = ($excludeAnonymous == true) ? "true" : "false";
        }

        return $this->get('reports/unsubscriptions/count', $params);
    }    /**
    * Returns a page of unsubscriberss.
    *
    * @param integer $fromDate
    *   If provided, only the unsubscriptions after the given date will be returned.
    *   The value of from_date must be a numeric value representing a point in time milliseconds
    *   afterJanuary 1, 1970 00:00:00
    * @param integer $toDate
    *  If provided, only the unsubscriptions before the given date will be returned. The value of
    *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
    * @param string $order
    *  String that describes the order. Possible values are: "count" or "name". Default is "count".
    * @param array $asc
    *  Describes if results will be ordered ascending or descending. Can be true or false, default is true.
    * @param integer $pageIndex
    *  The index of the result page. The index must be greater or equal to 1.
    * @param integer $pageSize
    *  The maximum count of items in the result page. If provided, the value of page_size
    *  must be in the range 1 to 1000.
    *
    * @return MaileonAPIResult
    */
    public function getUnsubscriberReasons(
        $fromDate = null,
        $toDate = null,
        $order = "count",
        $asc = true,
        $pageIndex = 1,
        $pageSize = 100
        ) {
            $params = $this->createQueryParameters(
                $pageIndex,
                $pageSize,
                $fromDate,
                $toDate,
                null,
                null,
                null,
                null,
                null,
                null
                );
            
            if (isset($asc)) {
                $params['asc'] = ($asc == true) ? "true" : "false";
            }
            if (isset($order)) {
                $params['order'] = $order;
            }
            
            return $this->get('reports/unsubscriptions/reasons', $params);
    }

    /**
     * Returns a page of subscribers.
     *
     * @param integer $fromDate
     *  If provided, only the unsubscriptions after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unsubscriptions before the given date will be returned. The value of to_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the unsubscriptions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the unsubscriptions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unsubscriptions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unsubscriptions by external ids. Each value must correspond to
     *  a contacts external id.
     * @param bool $excludeAnonymousContacts
     *  If this is set to true (default), only subscribers that have not yet been anonymized
     *  (due to deletion) are returned.
     * @param array $standardFields
     *  The list of standard contact fields to return.
     * @param array $customFields
     *  The list of custom contact fields to return.
     * @param bool $embedFieldBackups
     *  Supported values: true / false. Field Backups are the values of contact fields that have been backed
     *  up for mailings because of a backup instruction. For each unsubscription, the corresponding field
     *  backups will be returned if available. Note that this only applies for non anonymizable field backups.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be in
     *  the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getSubscribers(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $excludeAnonymousContacts = false,
        $standardFields = array(),
        $customFields = array(),
        $embedFieldBackups = false,
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            $embedFieldBackups
        );

        $params = $this->appendArrayFields($params, "standard_field", $standardFields);
        $params = $this->appendArrayFields($params, "custom_field", $customFields);
        if (isset($excludeAnonymousContacts)) {
            $params ['exclude_anonymous_contacts'] = ($excludeAnonymousContacts == true) ? "true" : "false";
        }

        return $this->get('reports/subscribers', $params);
    }

    /**
     * Count subscribers.
     *
     * @param integer $fromDate
     *  If provided, only the unsubscriptions after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unsubscriptions before the given date will be returned. The value of to_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the unsubscriptions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the unsubscriptions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unsubscriptions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unsubscriptions by external ids. Each value must correspond to a
     *  contacts external id.
     * @param bool $excludeAnonymousContacts
     *  If this is set to true (default), only subscribers that have not yet been anonymized
     *  (due to deletion) are returned.
     * @return MaileonAPIResult
     */
    public function getSubscribersCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $excludeAnonymousContacts = false
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        if (isset($excludeAnonymousContacts)) {
            $params ['exclude_anonymous_contacts'] = ($excludeAnonymousContacts == true) ? "true" : "false";
        }

        return $this->get('reports/subscribers/count', $params);
    }

    /**
     * Returns a page of conversions.
     *
     * @param integer $fromDate
     *  If provided, only the conversions after the given date will be returned. The value of from_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the conversions before the given date will be returned. The value of to_date must
     *  be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the conversions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the conversions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the conversions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the conversions by external ids. Each value must correspond
     *  to a contacts external id.
     * @param array $siteIds
     *  Multivalued parameter to filter the conversions by site ids. Each value must correspond to a valid site id.
     * @param array $goalIds
     *  Multivalued parameter to filter the conversions by goal ids. Each value must correspond to a valid goal id.
     * @param array $linkIds
     *  Multivalued parameter to filter the conversions by link ids. Each value must correspond to a valid link id.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must
     *  be in the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getConversions(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $siteIds = array(),
        $goalIds = array(),
        $linkIds = array(),
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            null
        );

        $params = $this->appendArrayFields($params, "site_ids", $siteIds);
        $params = $this->appendArrayFields($params, "goal_ids", $goalIds);
        $params = $this->appendArrayFields($params, "link_ids", $linkIds);

        return $this->get('reports/analytics/conversions', $params);
    }

    /**
     * Returns the count of conversions.
     *
     * @param integer $fromDate
     *  If provided, only the conversions after the given date will be returned. The value of
     *  from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the conversions before the given date will be returned. The value of
     *  to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the conversions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the conversions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the conversions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the conversions by external ids. Each value must correspond to
     *  a contacts external id.
     * @param array $siteIds
     *  Multivalued parameter to filter the conversions by site ids. Each value must correspond to a valid site id.
     * @param array $goalIds
     *  Multivalued parameter to filter the conversions by goal ids. Each value must correspond to a valid goal id.
     * @param array $linkIds
     *  Multivalued parameter to filter the conversions by link ids. Each value must correspond to a valid link id.
     * @return number
     */
    public function getConversionsCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $siteIds = array(),
        $goalIds = array(),
        $linkIds = array()
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        $params = $this->appendArrayFields($params, "site_ids", $siteIds);
        $params = $this->appendArrayFields($params, "goal_ids", $goalIds);
        $params = $this->appendArrayFields($params, "link_ids", $linkIds);

        return $this->get('reports/analytics/conversions/count', $params);
    }

    /**
     * Returns a page of unique conversions.
     *
     * @param integer $fromDate
     *  If provided, only the unique conversions after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unique conversions before the given date will be returned. The value of to_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the unique conversions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the unique conversions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unique conversions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unique conversions by external ids. Each value must
     *  correspond to a contacts external id.
     * @param array $siteIds
     *  Multivalued parameter to filter the unique conversions by site ids. Each value must correspond
     *  to a valid site id.
     * @param array $goalIds
     *  Multivalued parameter to filter the unique conversions by goal ids. Each value must correspond
     *  to a valid goal id.
     * @param array $linkIds
     *  Multivalued parameter to filter the unique conversions by link ids. Each value must correspond
     *  to a valid link id.
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be in
     *  the range 1 to 1000.
     * @return MaileonAPIResult
     */
    public function getUniqueConversions(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $siteIds = array(),
        $goalIds = array(),
        $linkIds = array(),
        $pageIndex = 1,
        $pageSize = 100
    ) {
        $params = $this->createQueryParameters(
            $pageIndex,
            $pageSize,
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null,
            null
        );

        $params = $this->appendArrayFields($params, "site_ids", $siteIds);
        $params = $this->appendArrayFields($params, "goal_ids", $goalIds);
        $params = $this->appendArrayFields($params, "link_ids", $linkIds);

        return $this->get('reports/analytics/conversions/unique', $params);
    }

    /**
     * Returns a page of unique conversions.
     *
     * @param integer $fromDate
     * If provided, only the unique conversions after the given date will be returned. The value of
     * from_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     * If provided, only the unique conversions before the given date will be returned. The value of
     * to_date must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the unique conversions by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the unique conversions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unique conversions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unique conversions by external ids. Each value must
     *  correspond to a contacts external id.
     * @param array $siteIds
     *  Multivalued parameter to filter the unique conversions by site ids. Each value must correspond
     *  to a valid site id.
     * @param array $goalIds
     *  Multivalued parameter to filter the unique conversions by goal ids. Each value must correspond
     *  to a valid goal id.
     * @param array $linkIds
     *  Multivalued parameter to filter the unique conversions by link ids. Each value must correspond
     *  to a valid link id.
     * @return number
     */
    public function getUniqueConversionsCount(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $siteIds = array(),
        $goalIds = array(),
        $linkIds = array()
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        $params = $this->appendArrayFields($params, "site_ids", $siteIds);
        $params = $this->appendArrayFields($params, "goal_ids", $goalIds);
        $params = $this->appendArrayFields($params, "link_ids", $linkIds);

        return $this->get('reports/analytics/conversions/unique/count', $params);
    }

    /**
     * Returns the revenue value.
     *
     * @param integer $fromDate
     *  If provided, only the revenues after the given date will be returned. The value of from_date
     * must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the revenues before the given date will be returned. The value of to_date
     * must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $mailingIds
     *  Multivalued parameter to filter the revenues by mailings. Each value must correspond to a mailing id.
     * @param array $contactIds
     *  Multivalued parameter to filter the revenues by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the revenues by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the revenues by external ids. Each value must correspond to a
     *  contacts external id.
     * @param array $siteIds
     *  Multivalued parameter to filter the revenues by site ids. Each value must correspond to a valid site id.
     * @param array $goalIds
     *  Multivalued parameter to filter the revenues by goal ids. Each value must correspond to a valid goal id.
     * @param array $linkIds
     *  Multivalued parameter to filter the revenues by link ids. Each value must correspond to a valid link id.
     * @return MaileonAPIResult
     */
    public function getRevenue(
        $fromDate = null,
        $toDate = null,
        $mailingIds = array(),
        $contactIds = array(),
        $contactEmails = array(),
        $contactExternalIds = array(),
        $siteIds = array(),
        $goalIds = array(),
        $linkIds = array()
    ) {
        $params = $this->createCountQueryParameters(
            $fromDate,
            $toDate,
            $contactIds,
            $contactEmails,
            $contactExternalIds,
            $mailingIds,
            null
        );

        $params = $this->appendArrayFields($params, "site_ids", $siteIds);
        $params = $this->appendArrayFields($params, "goal_ids", $goalIds);
        $params = $this->appendArrayFields($params, "link_ids", $linkIds);

        return $this->get('reports/analytics/conversions/revenue', $params);
    }

    /**
     * Creates the common query parameters
     *
     * @param integer $pageIndex
     *  The index of the result page. The index must be greater or equal to 1.
     * @param integer $pageSize
     *  The maximum count of items in the result page. If provided, the value of page_size must be in
     *  the range 1 to 1000.
     * @param integer $fromDate
     *  If provided, only the unsubscriptions after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unsubscriptions before the given date will be returned. The value of to_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $contactIds
     *  Multivalued parameter to filter the unsubscriptions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unsubscriptions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unsubscriptions by external ids. Each value must
     *  correspond to a contacts external id.
     * @param array $mailingIds
     *  Multivalued parameter to filter the unsubscriptions by mailings. Each value must correspond to a mailing id.
     * @param string $source
     *  Filters the unsubscriptions by their source. The source can be an unsubscription link
     *  (link), a reply mail (reply) or other.
     * @param bool
     *  $embedFieldBackups Supported values: true / false. Field Backups are the values of
     *  contact fields that have been backed up for mailings because of a backup instruction.
     *  For each unsubscription, the corresponding field backups will be returned if available.
     *  Note that this only applies for non anonymizable field backups.
     *
     * @return MaileonAPIResult
     */
    private function createQueryParameters(
        $pageIndex,
        $pageSize,
        $fromDate,
        $toDate,
        $contactIds,
        $contactEmails,
        $contactExternalIds,
        $mailingIds,
        $source,
        $embedFieldBackups
    ) {
        $queryParameters = array(
            'page_index' => $pageIndex,
            'page_size' => $pageSize
        );

        if (isset($fromDate)) {
            $queryParameters ['from_date'] = $fromDate;
        }
        if (isset($toDate)) {
            $queryParameters ['to_date'] = $toDate;
        }
        if (isset($source)) {
            $queryParameters ['source'] = $source;
        }

        $queryParameters = $this->appendArrayFields($queryParameters, "ids", $contactIds);
        $queryParameters = $this->appendArrayFields($queryParameters, "emails", $contactEmails);
        $queryParameters = $this->appendArrayFields($queryParameters, "eids", $contactExternalIds);

        if (isset($embedFieldBackups)) {
            $queryParameters ['embed_field_backups'] = ($embedFieldBackups == true) ? "true" : "false";
        }

        if (isset($mailingIds)) {
            $queryParameters ['mailing_id'] = array();

            foreach ($mailingIds as $mailingId) {
                $queryParameters ['mailing_id'] [] = $mailingId;
            }
        }
        return $queryParameters;
    }

    /**
     * Creates the common query parameters for count operations
     *
     * @param integer $fromDate
     *  If provided, only the unsubscriptions after the given date will be returned. The value of from_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param integer $toDate
     *  If provided, only the unsubscriptions before the given date will be returned. The value of to_date
     *  must be a numeric value representing a point in time milliseconds afterJanuary 1, 1970 00:00:00
     * @param array $contactIds
     *  Multivalued parameter to filter the unsubscriptions by contacts. Each value must correspond to a contact id.
     * @param array $contactEmails
     *  Multivalued parameter to filter the unsubscriptions by email addresses.
     * @param array $contactExternalIds
     *  Multivalued parameter to filter the unsubscriptions by external ids. Each value must
     *  correspond to a contacts external id.
     * @param array $mailingIds
     *  Multivalued parameter to filter the unsubscriptions by mailings. Each value must correspond to a mailing id.
     * @param string $source
     *  Filters the unsubscriptions by their source. The source can be an unsubscription link
     *  (link), a reply mail (reply) or other.
     *
     * @return MaileonAPIResult
     */
    private function createCountQueryParameters(
        $fromDate,
        $toDate,
        $contactIds,
        $contactEmails,
        $contactExternalIds,
        $mailingIds,
        $source
    ) {
        $queryParameters = array();

        if (isset($fromDate)) {
            $queryParameters ['from_date'] = $fromDate;
        }
        if (isset($toDate)) {
            $queryParameters ['to_date'] = $toDate;
        }
        if (isset($source)) {
            $queryParameters ['source'] = $source;
        }

        $queryParameters = $this->appendArrayFields($queryParameters, "ids", $contactIds);
        $queryParameters = $this->appendArrayFields($queryParameters, "emails", $contactEmails);
        $queryParameters = $this->appendArrayFields($queryParameters, "eids", $contactExternalIds);

        if (isset($mailingIds)) {
            $queryParameters ['mailing_id'] = array();

            foreach ($mailingIds as $mailingId) {
                $queryParameters ['mailing_id'] [] = $mailingId;
            }
        }
        return $queryParameters;
    }
}

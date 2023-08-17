<?php

namespace de\xqueue\maileon\api\client\reports;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * This class represents an unsubscription containing the timestamp, the contact,
 * the ID of the mailing the unsubscription came from, and the source.
 *
 * @author Viktor Balogh (Wiera)
 * @author Marcus St&auml;nder | Trusted Mails GmbH |
 * <a href="mailto:marcus.staender@trusted-mails.com">marcus.staender@trusted-mails.com</a>
 */
class Unsubscriber extends AbstractXMLWrapper
{
    /**
     * @var integer
     */
    public $timestamp;

    /**
     * @var ReportContact
     */
    public $contact;

    /**
     * @var integer
     */
    public $mailingId;
    
    /**
     * @var string
     */
    public $transactionId;
    
    /**
     * @var integer
     */
    public $messageId;

    /**
     * @var string
     */
    public $source;

    /**
     * @return string
     *  containing a human-readable representation of this unsubscription
     */
    public function toString()
    {
        return "Unsubscriber [timestamp=" . $this->timestamp .
        ", contact=" . $this->contact->toString() .
        ", mailingId=" . $this->mailingId .
        ", source=" . $this->source .
        ", transactionId=" . $this->transactionId .
        ", messageId=" . $this->messageId ."]";
    }

    /**
     * @return string containing a csv pepresentation of this unsubscriber
     */
    public function toCsvString()
    {
        return $this->timestamp .
        ";" . $this->contact->toCsvString() .
        ";" . $this->mailingId .
        ";" . $this->source .
        ";" . $this->transactionId .
        ";" . $this->messageId;
    }

    /**
     * Initializes this unsubscription from an XML representation.
     *
     * @param \SimpleXMLElement $xmlElement
     *  the XML representation to use
     */
    public function fromXML($xmlElement)
    {
        $this->contact = new ReportContact();
        $this->contact->fromXML($xmlElement->contact);

        if (isset($xmlElement->mailing_id)) {
            $this->mailingId = $xmlElement->mailing_id;
        }
        if (isset($xmlElement->source)) {
            $this->source = $xmlElement->source;
        }
        if (isset($xmlElement->timestamp)) {
            $this->timestamp = $xmlElement->timestamp;
        }
        if (isset($xmlElement->transaction_id)) {
            $this->transactionId = $xmlElement->transaction_id;
        }
        if (isset($xmlElement->msg_id )) {
            $this->messageId = $xmlElement->msg_id ;
        }
    }

    /**
     * For future use, not implemented yet.
     *
     * @return \SimpleXMLElement
     *  containing the XML serialization of this object
     */
    public function toXML()
    {
        $xmlString = "<?xml version=\"1.0\"?><unsubscriber></unsubscriber>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->contact)) {
            $xml->addChild("contact", $this->contact->toXML());
        }        
        if (isset($this->timestamp)) {
            $xml->addChild("timestamp", $this->timestamp);
        }
        if (isset($this->mailingId)) {
            $xml->addChild("mailing_id", $this->mailingId);
        }
        if (isset($this->transactionId)) {
            $xml->addChild("transaction_id", $this->transactionId);
        }
        if (isset($this->messageId )) {
            $xml->addChild("msg_id", $this->messageId);
        }
        if (isset($this->source)) {
            $xml->addChild("source", $this->source);
        }

        return $xml;
    }
}

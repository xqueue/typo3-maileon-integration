<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a Maileon attachment. This class wraps the XML structure.
 *
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 */
class Attachment extends AbstractXMLWrapper
{
    public $id;
    public $filename;
    public $sizekb;
    public $mime_type;
    public $diagnosis;
    public $created;
    public $updated;

    /**
     * Constructor initializing default values.
     *
     * @param integer $id
     * @param string $filename
     * @param integer $sizekb
     * @param string $mime_type
     * @param string $diagnosis
     * @param string $created
     * @param string $updated
     */
    public function __construct(
        $id = null,
        $filename = null,
        $sizekb = null,
        $mime_type = null,
        $diagnosis = null,
        $created = null,
        $updated = null
    ) {
        $this->id = $id;
        $this->filename = $filename;
        $this->sizekb = $sizekb;
        $this->mime_type = $mime_type;
        $this->diagnosis = $diagnosis;
        $this->created = $created;
        $this->updated = $updated;
    }

    /**
     * Initialization of the attachment from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the attachment from.
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->id)) {
            $this->id = (int)$xmlElement->id;
        }
        if (isset($xmlElement->filename)) {
            $this->filename = (string)$xmlElement->filename;
        }
        if (isset($xmlElement->sizekb)) {
            $this->sizekb = (int)$xmlElement->sizekb;
        }
        if (isset($xmlElement->mime_type)) {
            $this->mime_type = (string)$xmlElement->mime_type;
        }
        if (isset($xmlElement->diagnosis)) {
            $this->diagnosis = (string)$xmlElement->diagnosis;
        }
        if (isset($xmlElement->created)) {
            $this->created = (string)$xmlElement->created;
        }
        if (isset($xmlElement->updated)) {
            $this->updated = (string)$xmlElement->updated;
        }
    }

    /**
     * Creates the XML representation of an attachment
     *
     * @return \SimpleXMLElement
     */
    public function toXML()
    {
        /*$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><attachment></attachment>");

        $xml->addChild("id", $this->id);
        $xml->addChild("filename", $this->filename);
        $xml->addChild("sizekb", $this->sizekb);
        $xml->addChild("mime_type", $this->mime_type);
        $xml->addChild("diagnosis", $this->diagnosis);
        $xml->addChild("created", $this->created);
        $xml->addChild("updated", $this->updated);

        return $xml;*/
        throw new MaileonAPIException('not implemented');
    }
    
    /**
     * Human readable representation of this wrapper.
     *
     * @return string
     *  A human readable version of the mailing.
     */
    public function toString()
    {
        return "Attachment [id=" . $this->id . ", "
                . "filename=" . $this->filename . ", "
                . "sizekb=" . $this->sizekb . ", "
                . "mime_type=" . $this->mime_type . ", "
                . "diagnosis=" . $this->diagnosis . ", "
                . "created=" . $this->created . ", "
                . "updated=" . $this->updated . "]";
    }
}

<?php

namespace de\xqueue\maileon\api\client;

/**
 * An exception that is thrown when a technical error has occurred either in the communication
 * with Maileon's REST API or in the API itself.
 */
class MaileonAPIException extends \RuntimeException
{
    private $response;

    public function __construct(
        $message = "",
        $response = false,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     *
     * @return false|string
     *  the HTTP response body if there was one, false otherwise.
     *  If a CURL error occured, this returns the ID of the CURL exception, see e.g. https://curl.se/libcurl/c/libcurl-errors.html
     */
    public function getResponse()
    {
        return $this->response;
    }
}

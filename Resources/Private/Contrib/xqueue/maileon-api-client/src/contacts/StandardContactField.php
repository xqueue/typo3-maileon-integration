<?php

namespace de\xqueue\maileon\api\client\contacts;

/**
 * The class contains the valid names for a Maileon standard contact field
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class StandardContactField
{
    /** The Constant ADDRESS. */
    public static $ADDRESS = "ADDRESS";

    /** The Constant BIRTHDAY. */
    public static $BIRTHDAY = "BIRTHDAY";

    /** The Constant CITY. */
    public static $CITY = "CITY";

    /** The Constant COUNTRY. */
    public static $COUNTRY = "COUNTRY";

    /** The Constant FIRSTNAME. */
    public static $FIRSTNAME = "FIRSTNAME";

    /** The Constant FULLNAME. */
    public static $FULLNAME = "FULLNAME";

    /** The Constant GENDER. */
    public static $GENDER = "GENDER";

    /** The Constant HNR. */
    public static $HNR = "HNR";

    /** The Constant LASTNAME. */
    public static $LASTNAME = "LASTNAME";

    /** The Constant LOCALE. */
    public static $LOCALE = "LOCALE";

    /** The Constant NAMEDAY. */
    public static $NAMEDAY = "NAMEDAY";

    /** The Constant ORGANIZATION. */
    public static $ORGANIZATION = "ORGANIZATION";

    /** The Constant REGION. */
    public static $REGION = "REGION";

    /** The Constant SALUTATION. */
    public static $SALUTATION = "SALUTATION";

    /** The Constant TITLE. */
    public static $TITLE = "TITLE";

    /** The Constant ZIP. */
    public static $ZIP = "ZIP";
    
    /** The Constant STATE. */
    public static $STATE = "STATE";
    
    /** The Constant SENDOUT_STATUS. Sendout status can be "blocked" or "allowed" */
    public static $SENDOUT_STATUS = "SENDOUT_STATUS";
    
    /** The Constant PERMISSION_STATUS. Permission status can be "available" (permission != none and not unsubscribed), "none" (no permission given, yet), or "unsubscribed" */
    public static $PERMISSION_STATUS = "PERMISSION_STATUS";


    public static function init()
    {
        // Nothing to initialize
    }
}
StandardContactField::init();

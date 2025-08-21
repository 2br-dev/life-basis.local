<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail\MsTnef;

class Mailinfo
{
    public $subject;
    public $topic;
    public $topic_is_unicode = FALSE;
    public $from;
    public $from_is_unicode = FALSE;
    public $from_name;
    public $from_name_is_unicode = FALSE;
    public $date_sent;
    public $code_page = '';

    function getTopic()
    {
        return $this->topic;
    }

    function getSubject()
    {
        return $this->subject;
    }

    function getFrom()
    {
        return $this->from;
    }

    function getCodePage()
    {
        return $this->code_page;
    }

    function getFromName()
    {
        return $this->from_name;
    }

    function getDateSent()
    {
        return $this->date_sent;
    }

    function receiveTnefAttribute($attribute, $value, $length)
    {
        switch ($attribute) {
            case TNEF_AOEMCODEPAGE:
                $this->code_page = tnef_geti16($value);
                break;

            case TNEF_ASUBJECT:
                $this->subject = substr($value, 0, $length - 1);
                break;

            case TNEF_ADATERECEIVED:
                if (!$this->date_sent) {
                    $this->date_sent = new Datetime();
                    $this->date_sent->setTnefBuffer($value);
                }
                break;
            case TNEF_ADATESENT:
                $this->date_sent = new Datetime();
                $this->date_sent->setTnefBuffer($value);
        }
    }

    function receiveMapiAttribute($attr_type, $attr_name, $value, $length, $is_unicode = FALSE)
    {
        switch ($attr_name) {
            case TNEF_MAPI_CONVERSATION_TOPIC:
                $this->topic = $value;
                if ($is_unicode) $this->topic_is_unicode = TRUE;
                break;

            case TNEF_MAPI_SENT_REP_EMAIL_ADDR:
                $this->from = $value;
                if ($is_unicode) $this->from_is_unicode = TRUE;
                break;

            case TNEF_MAPI_SENT_REP_NAME:
                $this->from_name = $value;
                if ($is_unicode) $this->from_name_is_unicode = TRUE;
                break;
        }
    }
}
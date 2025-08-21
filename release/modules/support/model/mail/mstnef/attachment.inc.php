<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail\MsTnef;

require 'functions.php';
require 'constants.php';

/**
 * Класс, позволяющий открывать вложения в формате Microsoft Outlook TNEF
 *
 * https://learn.microsoft.com/en-us/exchange/mail-flow/content-conversion/tnef-conversion?view=exchserver-2019
 */
class Attachment
{

   public $mailinfo;
   public $files;
   public $files_nested;
   public $attachments;
   public $current_receiver;
    public $body;

   function __construct()
   {
       $this->files = array();
       $this->attachments = array();
       $this->mailinfo = new Mailinfo();
       $this->body = [];
   }

   /**
    * @return FileBase[]
    */
   function &getFiles()
   {
      return $this->files;
   }

   /**
    * @return FileBase[]
    */
   function &getFilesNested()
   {
      if (!$this->files_nested)
      {
         $this->files_nested = array();

         $num_attach = count($this->attachments);
         if ($num_attach > 0)
         {
            for ($cnt = 0; $cnt < $num_attach; $cnt++)
            {
               $this->addFiles($this->files_nested, $this->files);
               $this->addFiles($this->files_nested, $this->attachments[$cnt]->getFilesNested());
            }
         }
         else
            $this->addFiles($this->files_nested, $this->files);
      }

      return $this->files_nested;
   }

   function addFiles(&$add_to, &$add)
   {
      global $tnef_minimum_rtf_size_to_decode;
      $num_files = count($add);
      for ($cnt = 0; $cnt < $num_files; $cnt++)
         if ((strtolower(get_class($add[$cnt])) != "filertf") || ($add[$cnt]->getSize() > $tnef_minimum_rtf_size_to_decode))
            $add_to[] = &$add[$cnt];
   }

    function addFilesCond(&$add_to, &$add)
    {
        global $tnef_minimum_rtf_size_to_decode;
        $num_files = count($add);
        for ($cnt = 0; $cnt < $num_files; $cnt++)
            if ((strtolower(get_class($add[$cnt])) == "filertf") && ($add[$cnt]->getSize() > $tnef_minimum_rtf_size_to_decode))
                $add_to[] = &$add[$cnt];
    }

    function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return Mailinfo
     */
    function getMailinfo()
    {
        return $this->mailinfo;
    }

    function getBodyElements()
    {
        return $this->body;
    }

    function decodeTnef(&$buffer)
    {
        $tnef_signature = tnef_geti32($buffer);
        if ($tnef_signature == TNEF_SIGNATURE) {
            $tnef_key = tnef_geti16($buffer);

            while (strlen($buffer) > 0) {
                $lvl_type = tnef_geti8($buffer);

                switch ($lvl_type) {
                    case TNEF_LVL_MESSAGE:
                        $this->tnef_decode_attribute($buffer);
                        break;

                    case TNEF_LVL_ATTACHMENT:
                        $this->tnef_decode_attribute($buffer);
                        break;

                    default:
                        break;
            }
         }
      }

      $code_page = $this->mailinfo->getCodePage();
      if (!empty($code_page))
         foreach ($this->files as $i => $file)
            $this->files[$i]->setMessageCodePage($code_page);
   }

   function tnef_decode_attribute(&$buffer)
   {
      $attribute = tnef_geti32($buffer);     // attribute if
      $length = tnef_geti32($buffer);        // length
      $value = tnef_getx($length, $buffer);  // data
      tnef_geti16($buffer);                  // checksum

      switch($attribute)
      {
         case TNEF_ARENDDATA:                   // marks start of new attachment
            $this->current_receiver = new File();
            $this->files[] = $this->current_receiver;
            break;

         case TNEF_AMAPIATTRS:
            $this->extract_mapi_attrs($value);
            break;

         case TNEF_AMAPIPROPS:
            $this->extract_mapi_attrs($value);
            break;

         default:
            $this->mailinfo->receiveTnefAttribute($attribute, $value, $length);
            if ($this->current_receiver)
               $this->current_receiver->receiveTnefAttribute($attribute, $value, $length);
            break;
      }
   }

   function extract_mapi_attrs(&$buffer)
   {

      $number = tnef_geti32($buffer); // number of attributes
      $props = 0;
      $ended = 0;

      while ((strlen($buffer) > 0) && ($props < $number) && (!$ended))
      {
         $props++;
         $value = '';
         unset($named_id);
         $length = 0;
         $have_multivalue = 0;
         $num_multivalues = 1;
         $attr_type = tnef_geti16($buffer);
         $attr_name = tnef_geti16($buffer);
      
         if (($attr_type & TNEF_MAPI_MV_FLAG) != 0)
         {
            $have_multivalue = 1;
            $attr_type = $attr_type & ~TNEF_MAPI_MV_FLAG;
         }

         if (($attr_name >= 0x8000) && ($attr_name < 0xFFFE))   // Named Attribute
         {
            $guid = tnef_getx(16, $buffer);
            $named_type = tnef_geti32($buffer);
            switch ($named_type)
            {
               case TNEF_MAPI_NAMED_TYPE_ID:
                  $named_id = tnef_geti32($buffer);
                  $attr_name = $named_id;
                  break;

               case TNEF_MAPI_NAMED_TYPE_STRING:
                  $attr_name = 0x9999;    // dummy to identify strings
                  $idlen = tnef_geti32($buffer);
                  $buflen = $idlen + ((4 - ($idlen % 4)) % 4);  // pad to next 4 byte boundary
                  $named_id = substr(tnef_getx($buflen, $buffer), 0, $idlen );  // read and truncate to length
                  break;

               default:
                  break;
            }
         }

         if ($have_multivalue)
         {
            $num_multivalues = tnef_geti32($buffer);
         }

         switch($attr_type)
         {
            case TNEF_MAPI_NULL:
               break;

            case TNEF_MAPI_SHORT:
               $value = tnef_geti16($buffer);
               break;

            case TNEF_MAPI_INT:
            case TNEF_MAPI_BOOLEAN:
               for ($cnt = 0; $cnt < $num_multivalues; $cnt++)
                  $value = tnef_geti32($buffer);
               break;

            case TNEF_MAPI_FLOAT:
            case TNEF_MAPI_ERROR:
               $value = tnef_getx(4, $buffer);
               break;

            case TNEF_MAPI_DOUBLE:
            case TNEF_MAPI_APPTIME:
            case TNEF_MAPI_CURRENCY:
            case TNEF_MAPI_INT8BYTE:
            case TNEF_MAPI_SYSTIME:
               $value = tnef_getx(8, $buffer);
               break;

            case TNEF_MAPI_CLSID:
               break;

            case TNEF_MAPI_STRING:
            case TNEF_MAPI_UNICODE_STRING:
            case TNEF_MAPI_BINARY:
            case TNEF_MAPI_OBJECT:
               if ($have_multivalue)
                  $num_vals = $num_multivalues;
               else
                  $num_vals = tnef_geti32($buffer);

               if ($num_vals > 20)      // A Sanity check.
               {
                  $ended = 1;
               }
               else
               {
                  for ($cnt = 0; $cnt < $num_vals; $cnt++)
                  {
                     $length = tnef_geti32($buffer);
                     $buflen = $length + ((4 - ($length % 4)) % 4); // pad to next 4 byte boundary
                     if ($attr_type == TNEF_MAPI_STRING)
                       $length -= 1;
                     $value = substr(tnef_getx($buflen, $buffer), 0, $length); // read and truncate to length
                  }
               }
               break;

            default:
               break;
         }

         switch ($attr_name)
         {
             case TNEF_MAPI_ATTACH_DATA:
                 tnef_getx(16, $value); // skip the next 16 bytes (unknown data)
                 $att = new Attachment();
                 $att->decodeTnef($value);
                 $this->attachments[] = $att;
                 break;

             case TNEF_MAPI_RTF_COMPRESSED:
                 $this->files[] = new FileRTF($value);
                 break;
             case TNEF_MAPI_BODY:
             case TNEF_MAPI_BODY_HTML:
                 $result = [];
                 $result['type'] = 'text';
                 $result['subtype'] = $attr_name == TNEF_MAPI_BODY ? 'plain' : 'html';
                 $result['name'] = ('Untitled') . ($attr_name == TNEF_MAPI_BODY ? '.txt' : '.html');
                 $result['stream'] = $value;
                 $result['size'] = strlen($value);
                 $this->body[] = $result;
                 break;
             default:
                 $this->mailinfo->receiveMapiAttribute($attr_type, $attr_name, $value, $length, ($attr_type == TNEF_MAPI_UNICODE_STRING));
                 if ($this->current_receiver)
                     $this->current_receiver->receiveMapiAttribute($attr_type, $attr_name, $value, $length, ($attr_type == TNEF_MAPI_UNICODE_STRING));
                 break;
         }
      }
   }
}
<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail\MsTnef;

class File extends FileBase
{
   public $metafile;

   function getMetafile()
   {
      return $this->metafile;
   }

   function receiveTnefAttribute($attribute, $value, $length)
   {
      switch ($attribute)
      {
         case TNEF_AFILENAME:

            if (($pos = strrpos($value, '/')) !== FALSE)
               $this->name = substr($value, $pos + 1);
            else
               $this->name = $value;

            break;

         case TNEF_AOEMCODEPAGE:
            $this->code_page = tnef_geti16($value);
            break;

         case TNEF_ATTACHDATA:
            $this->content = $value;
            break;

         case TNEF_ATTACHMETAFILE:
            $this->metafile = $value;
            break;

         case TNEF_AATTACHCREATEDATE:
            $this->created = new Datetime();
            $this->created->setTnefBuffer($value);

         case TNEF_AATTACHMODDATE:
            $this->modified = new Datetime();
            $this->modified->setTnefBuffer($value);
            break;
      }
   }

   function receiveMapiAttribute($attr_type, $attr_name, $value, $length, $is_unicode=FALSE)
   {
      switch ($attr_name)
      {

         case TNEF_MAPI_ATTACH_LONG_FILENAME:
            if (($pos = strrpos($value, '/')) !== FALSE)
               $this->name = substr($value, $pos + 1);
            else
               $this->name = $value;

            if ($is_unicode) $this->name_is_unicode = TRUE;

            break;

         case TNEF_MAPI_ATTACH_MIME_TAG:
            $type0 = $type1 = '';
            $mime_type = explode('/', $value, 2);
            if (!empty($mime_type[0])) 
               $type0 = $mime_type[0];
            if (!empty($mime_type[1])) 
               $type1 = $mime_type[1];
            $this->type = "$type0/$type1";
            if ($is_unicode) {
                $this->type = substr(mb_convert_encoding($this->type, "UTF-8" , "UTF-16LE"), 0, -1);
            }
            break;

         case TNEF_MAPI_ATTACH_EXTENSION:
            $type = extension_to_mime($value);
            if ($type)
	       $this->type = $type;
            break;
      }
   }
}
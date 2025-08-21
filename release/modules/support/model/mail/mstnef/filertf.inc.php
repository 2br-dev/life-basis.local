<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Mail\MsTnef;

class FileRTF extends FileBase
{
   public $size;
   const MAX_DICT_SIZE = 4096;
   const INIT_DICT_SIZE = 207;

   function __construct($buffer)
   {
      parent::__construct();
      $this->type = "application/rtf";
      $this->name = "EmbeddedRTF.rtf";

      $this->decode_crtf($buffer);
   }

   function getSize()
   {
      return $this->size;
   }

   function decode_crtf(&$buffer)
   {
      $size_compressed = tnef_geti32($buffer);
      $this->size = tnef_geti32($buffer);
      $magic = tnef_geti32($buffer);
      $crc32 = tnef_geti32($buffer);

      switch ($magic) {
         case CRTF_COMPRESSED:
            $this->uncompress($buffer);
            break;

         case CRTF_UNCOMPRESSED:
            $this->content = $buffer;
            break;

         default:
            break;
      }
   }

   function uncompress(&$data)
   {
      $preload = "{\\rtf1\\ansi\\mac\\deff0\\deftab720{\\fonttbl;}{\\f0\\fnil \\froman \\fswiss \\fmodern \\fscript \\fdecor MS Sans SerifSymbolArialTimes New RomanCourier{\\colortbl\\red0\\green0\\blue0\n\r\\par \\pard\\plain\\f0\\fs20\\b\\i\\u\\tab\\tx";
      $length_preload = strlen($preload);
      $init_dict = [];
      for ($cnt = 0; $cnt < $length_preload; $cnt++) {
         $init_dict[$cnt] = $preload[$cnt];
      }
      $init_dict = array_merge($init_dict, array_fill(count($init_dict), self::MAX_DICT_SIZE - $length_preload, ' '));
      $write_offset = self::INIT_DICT_SIZE;
      $output_buffer = '';
      $end = false;
      $in = 0;
      $l = strlen($data);
      while (!$end) {
         if ($in >= $l) {
            break;
         }
          $control = strrev(str_pad(decbin(ord($data[$in++])), 8, 0, STR_PAD_LEFT));
         for ($i = 0; $i < 8; $i++) {
            if ($control[$i] == '1') {
                $token = unpack("n", $data[$in++] . $data[$in++])[1];
                $offset = ($token >> 4) & 0b111111111111;
                $length = $token & 0b1111;
                if ($write_offset == $offset) {
                    $end = true;
                    break;
                }
                $actual_length = $length + 2;
                for ($step = 0; $step < $actual_length; $step++) {
                    $read_offset = ($offset + $step) % self::MAX_DICT_SIZE;
                    $char = $init_dict[$read_offset];
                    $output_buffer .= $char;
                  $init_dict[$write_offset] = $char;
                  $write_offset = ($write_offset + 1) % self::MAX_DICT_SIZE;
               }
            } else {
                if ($in >= $l) {
                    break;
                }
                $val = $data[$in++];
                $output_buffer .= $val;
                $init_dict[$write_offset] = $val;
                $write_offset = ($write_offset + 1) % self::MAX_DICT_SIZE;
            }
         }
      }
      $this->content = $output_buffer;
   }
}




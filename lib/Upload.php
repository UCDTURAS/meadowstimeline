<?php
namespace FAC;
use FAC;
/**
 * Upload classfile.
 *
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

class Upload{

    static public function getUnique($test, $dir='uploads'){

        $file = (object) pathinfo($test);
        $test = MEADOWS_DIR."/{$dir}/{$file->basename}";
        $count = 2;

        while(file_exists($test)){
            $test = MEADOWS_DIR."/{$dir}/{$file->filename}-{$count}.{$file->extension}";
            $count++;
        }

        return $test;
    }

    /**
     * Validate an upload.
     * @param  array  $file The array from $_FILES global.
     * @return \FAC\Error       Returns Error instance on fail, true on pass.
     */
    static public function validateUpload(array $file)
    {

      $code = $file['error'];
      $message = null;

      switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
          $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
          break;
        case UPLOAD_ERR_FORM_SIZE:
          $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
          break;
        case UPLOAD_ERR_PARTIAL:
          $message = "The uploaded file was only partially uploaded";
          break;
        case UPLOAD_ERR_NO_FILE:
          $message = "No file was uploaded";
          break;
        case UPLOAD_ERR_NO_TMP_DIR:
          $message = "Missing a temporary folder";
          break;
        case UPLOAD_ERR_CANT_WRITE:
          $message = "Failed to write file to disk";
          break;
        case UPLOAD_ERR_EXTENSION:
          $message = "File upload stopped by extension";
          break;

        default:
            $message = null;
            break;
      }

      if($message)
        return new Error($message);

      return true;
    }
}

<?php
namespace FAC;
use FAC;
/**
 * Error classfile.
 * @todo implement logging.
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

class Error{

  const SESSION_KEY = 'FAC_ERRORS';
  public $msg;

  /**
   * constructor.
   * @param string $msg The error message
   */
  public function __construct($msg){

    if(!isset($_SESSION[self::SESSION_KEY]))
      $_SESSION[self::SESSION_KEY] = array();

    $this->msg = $msg;
    $_SESSION[self::SESSION_KEY][] = $msg;
  }

  /**
   * Factory method.
   * @param  string $msg The error message.
   * @return \FAC\Error      Returns new Error instance.
   */
  static public function factory($msg){
      return new Error($msg);
  }

  /**
   * Get an array of reported errors.
   * @param boolean $reset Default true. Whether to reset the errors array(s).
   * @return array An array of error strings, empty array if none.
   */
  static public function getErrors($reset=true){

    //get, reset and return errors.
    if(isset($_SESSION[self::SESSION_KEY])){

      $ret = $_SESSION[self::SESSION_KEY];

      if($reset){
        unset($_SESSION[self::SESSION_KEY]);
      }

      return $ret;
    }

    //default
    return array();
  }
}

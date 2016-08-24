<?php
namespace FAC;
use FAC;
/**
 * Message classfile.
 * @todo implement logging.
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

class Message{

  const SESSION_KEY = 'FAC_MESSAGES';
  public $msg;

  /**
   * constructor.
   * @param string $msg The message message
   */
  public function __construct($msg){

    if(!isset($_SESSION[self::SESSION_KEY]))
      $_SESSION[self::SESSION_KEY] = array();

    $this->msg = $msg;
    $_SESSION[self::SESSION_KEY][] = $msg;
  }

  /**
   * Factory method.
   * @param  string $msg The message message.
   * @return \FAC\Message      Returns new Message instance.
   */
  static public function factory($msg){
      return new Message($msg);
  }

  /**
   * Get an array of reported messages.
   * @param boolean $reset Default true. Whether to reset the messages array(s).
   * @return array An array of message strings, empty array if none.
   */
  static public function getMessages($reset=true){

    //get, reset and return messages.
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

  /**
   * Print out messages in ul list (boostrap alerts)
   */
  static public function printMessages(){

    $messages = self::getMessages();

    print "<ul>";
    foreach($messages as $message){
      print "<li class=\"alert alert-success\">{$message}</li>";
    }
    print "</ul>";
  }
}

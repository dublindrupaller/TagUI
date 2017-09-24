<?php
/**
 * @file
 *
 */

/**
 *  code class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class code extends step {  
  
  /**
   * Construct wrapper object   
   */
  public function __construct($intent){    
    $this->intent = $intent;
  }
  /**
   * @return string
   */
  public function getIntent($intent) {    

    if (substr($intent,0,4)=="code ") {
      return $this->intent;
    }    
    return FALSE;
  }


  /**
   * @return string 
   *   casperjs code as string
   *
   * @param string $raw_intent
   *   The full written step line for passing directly to the casperjs output or parsing for sikuli
   * @param array $params
   *   Array of params for the given step
   * @param string $twb
   *   Tagui_web_browser token for constructing test header casperjs   
   * @param boolean $sikuli
   *   if input is meant for sikuli visual automation 
   *
   */
  public function parseIntent($intent, $raw_intent, $twb, $sikuli=FALSE) {     
    $params = parse_condition($raw_intent);
    // not relevant to call end_fi for condition statement, will reset for and while loop tracker prematurely
    if ((substr($params,0,3)=="if ") or (substr($params,0,8)=="else if ") or (substr($params,0,4)=="for ") or (substr($params,0,6)=="while ")) return $params."\n"; 
    else return $params.end_fi()."\n";
  }


// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function code_intent($raw_intent) {
  $params = parse_condition($raw_intent);
  // not relevant to call end_fi for condition statement, will reset for and while loop tracker prematurely
  if ((substr($params,0,3)=="if ") or (substr($params,0,8)=="else if ") or (substr($params,0,4)=="for ") or (substr($params,0,6)=="while ")) return $params."\n"; 
  else return $params.end_fi()."\n";
}


*/

}
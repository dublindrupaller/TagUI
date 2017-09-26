<?php
/**
 * @file
 *
 */

/**
 *  js class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class js extends step {  
  
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

    if (substr($intent,0,3)=="js "){
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
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    if ($params == "") echo "ERROR - " . current_line() . " statement missing for " . $raw_intent . "\n";
    else return $params.end_fi()."\n";
  }

}

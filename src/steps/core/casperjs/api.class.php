<?php
/**
 * @file
 *
 */


/**
 *  api class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class api extends step {  
  
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

    if (substr($intent,0,4)=="api ") {
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
    if ($params == "") echo "ERROR - " . current_line() . " API URL missing for " . $raw_intent . "\n"; 
    else return "{techo('".$raw_intent."');\napi_result = call_api('".$params."');\n" ."try {api_json = JSON.parse(api_result);} catch(e) {api_json = JSON.parse('null');}}".end_fi()."\n";
  }

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function api_intent($raw_intent) {
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  if ($params == "") echo "ERROR - " . current_line() . " API URL missing for " . $raw_intent . "\n"; 
  else return "{techo('".$raw_intent."');\napi_result = call_api('".$params."');\n" ."try {api_json = JSON.parse(api_result);} catch(e) {api_json = JSON.parse('null');}}".end_fi()."\n";
}


*/
}
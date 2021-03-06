<?php
/**
 * @file
 *
 */


/**
 *  api class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public getHeaderJs()
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

  public function getHeaderJs() {
    $js = <<<TAGUI
  function api_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
if (params == '') return "this.echo('ERROR - API URL missing for " + raw_intent + "')";
else return "api_result = call_api('" + params + "'); " +
"try {api_json = JSON.parse(api_result);} catch(e) {api_json = JSON.parse('null');}";}
TAGUI;
    return $js;
  }
}

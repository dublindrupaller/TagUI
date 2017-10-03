<?php
/**
 * @file
 *
 */

/**
 *  dom class which is a child of step
 *
 *  The class contains four methods:
 *  - __construct
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js()
 */

class dom extends step {  
  
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

    if (substr($intent,0,4)=="dom "){
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
    else return "dom_result = ".$twb.".evaluate(function() {".$params."});".end_fi()."\n";
  }


  public function get_header_js() {
    $js = <<<TAGUI
function dom_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
if (params == '') return "this.echo('ERROR - statement missing for " + raw_intent + "')";
else return "dom_result = this.evaluate(function(dom_json) {" + params + "}, dom_json)";}
TAGUI;
    return $js;
  }
}

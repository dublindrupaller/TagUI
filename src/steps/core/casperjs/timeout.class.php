<?php
/**
 * @file
 *
 */

/**
 *  timeout class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class timeout extends step {  
      
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

    if (substr($intent,0,8)=="timeout ") {
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
    if ($params == "") echo "ERROR - " . current_line() . " time in seconds missing for " . $raw_intent . "\n";
    else return "casper.options.waitTimeout = " . (floatval($params)*1000) . ";" . end_fi()."\n";
  }

   public function get_header_js() {
    $js = <<<TAGUI
function timeout_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
if (params == '') return "this.echo('ERROR - time in seconds missing for " + raw_intent + "')";
else return check_chrome_context("casper.options.waitTimeout = " + (parseFloat(params)*1000).toString() + ";");}
TAGUI;
    return $js;
  }       
}

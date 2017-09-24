<?php
/**
 * @file
 *
 */

/**
 *  popup class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class popup extends step {  

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

    if (substr($intent,0,6)=="popup ") {
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
    if ($GLOBALS['inside_popup'] != 0){
      echo "ERROR - " . current_line() . " popup called consecutively " . $raw_intent . "\n"; return;
    }
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    if ($GLOBALS['inside_frame']!=0) echo "ERROR - " . current_line() . " invalid after frame - " . $raw_intent . "\n";
    else if ($params == "") echo "ERROR - " . current_line() . " keyword missing for " . $raw_intent . "\n";
    else {
      $GLOBALS['inside_popup']=1; // during execution check for popup before going into popup context
      return "{techo('".$raw_intent."');\ncasper.waitForPopup(/".preg_quote($params)."/, function then() {},\n"."function timeout() {this.echo('ERROR - cannot find popup ".$params."').exit();});\n"."casper.withPopup(/".preg_quote($params)."/, function() {\n";
    }
  }

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function popup_intent($raw_intent) {
  if ($GLOBALS['inside_popup'] != 0){
    echo "ERROR - " . current_line() . " popup called consecutively " . $raw_intent . "\n"; return;
  }
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  if ($GLOBALS['inside_frame']!=0) echo "ERROR - " . current_line() . " invalid after frame - " . $raw_intent . "\n";
  else if ($params == "") echo "ERROR - " . current_line() . " keyword missing for " . $raw_intent . "\n";
  else {
    $GLOBALS['inside_popup']=1; // during execution check for popup before going into popup context
    return "{techo('".$raw_intent."');\ncasper.waitForPopup(/".preg_quote($params)."/, function then() {},\n"."function timeout() {this.echo('ERROR - cannot find popup ".$params."').exit();});\n"."casper.withPopup(/".preg_quote($params)."/, function() {\n";
  }
}




*/

}
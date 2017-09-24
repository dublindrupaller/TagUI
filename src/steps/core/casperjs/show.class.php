<?php
/**
 * @file
 *
 */

/**
 *  show class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class show extends step {
    
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

    if ((substr($intent,0,5)=="show ") || (substr($intent,0,6)=="print ")) {
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
    // TODO: $params is passed as an array but sent to casperjs code and sikuli output as a string   
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    if (strtolower($params) == "page") return "this.echo('".$raw_intent."' + ' - \\n' + ".$twb.".getHTML());".end_fi()."\n";
    if ($params == "") echo "ERROR - " . current_line() . " target missing for " . $raw_intent . "\n"; 
    else return "{// nothing to do on this line".beg_tx($params)."this.echo('".$raw_intent."' + ' - ' + ".$twb.".fetchText(tx('" . $params . "')).trim());".end_tx($params);
  }
}
class_alias('show', 'print');
// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function show_intent($raw_intent) {
  $twb = $GLOBALS['tagui_web_browser'];
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  if (strtolower($params) == "page") return "this.echo('".$raw_intent."' + ' - \\n' + ".$twb.".getHTML());".end_fi()."\n";
  if ($params == "") echo "ERROR - " . current_line() . " target missing for " . $raw_intent . "\n"; 
  else return "{// nothing to do on this line".beg_tx($params)."this.echo('".$raw_intent."' + ' - ' + ".$twb.".fetchText(tx('" . $params . "')).trim());".end_tx($params);
}

*/
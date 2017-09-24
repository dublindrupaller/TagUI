<?php
/**
 * @file
 *
 */

/**
 *  techo class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class techo extends step {
     
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

    if (substr($intent,0,5)=="techo ") {
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
    // TODO: $params is passed as an array but sent to casperjs code as a string    
    return "{techo('".$raw_intent."');".beg_tx($params).$twb.".click(tx('" . $params . "'));".end_tx($params);           
  }    
}

/*  copying in the original php function from tagui_parse.php for john to review oop version.

function echo_intent($raw_intent) {
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  if ($params == "") echo "ERROR - " . current_line() . " text missing for " . $raw_intent . "\n"; 
  else return "this.echo(".add_concat($params).");".end_fi()."\n";
}
*/
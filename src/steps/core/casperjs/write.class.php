<?php
/**
 * @file
 *
 */

/**
 *  write class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class write extends step {  
      
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

    if (substr($intent, 0, 6) == "write " || $intent == "write") {
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
   *
   */
  public function parseIntent($intent, $raw_intent, $twb, $sikuli=FALSE) {     
    // TODO: $params is passed as an array but sent to casperjs code and sikuli output as a string   
    $raw_intent = str_replace("'","\"",$raw_intent); // avoid breaking echo below when single quote is used
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params," to "))); 
    $param2 = trim(substr($params,4+strpos($params," to ")));
    if ($params == "") echo "ERROR - " . current_line() . " variable missing for " . $raw_intent . "\n";
    else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');\nappend_text('".abs_file($param2)."',".add_concat($param1).");}".end_fi()."\n";
    else return "{techo('".$raw_intent."');\nappend_text(''," . add_concat($params) . ");}".end_fi()."\n";
  }
}

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function write_intent($raw_intent) {
  $raw_intent = str_replace("'","\"",$raw_intent); // avoid breaking echo below when single quote is used
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  $param1 = trim(substr($params,0,strpos($params," to "))); $param2 = trim(substr($params,4+strpos($params," to ")));
  if ($params == "") echo "ERROR - " . current_line() . " variable missing for " . $raw_intent . "\n";
  else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');\nappend_text('".abs_file($param2)."',".add_concat($param1).");}".end_fi()."\n";
  else return "{techo('".$raw_intent."');\nappend_text(''," . add_concat($params) . ");}".end_fi()."\n";
}



*/


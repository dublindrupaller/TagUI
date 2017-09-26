<?php
/**
 * @file
 *
 */

/**
 *  frame class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class frame extends step {  
  
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

     if (substr($intent,0,6)=="frame ") {
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
    if ($GLOBALS['inside_frame'] != 0){
      echo "ERROR - " . current_line() . " frame called consecutively " . $raw_intent . "\n"; return;
    }
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params,"|"))); 
    $param2 = trim(substr($params,1+strpos($params,"|")));

    if ($params == "") echo "ERROR - " . current_line() . " name missing for " . $raw_intent . "\n";
    else if (strpos($params,"|")!==false) {
      $GLOBALS['inside_frame']=2; return "{techo('".$raw_intent."');\ncasper.withFrame('".$param1."', function() {casper.withFrame('".$param2."', function() {\n";
    } else {
      $GLOBALS['inside_frame']=1; return "{techo('".$raw_intent."');\ncasper.withFrame('".$params."', function() {\n";
    }
  }

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function frame_intent($raw_intent) {
  if ($GLOBALS['inside_frame'] != 0){
    echo "ERROR - " . current_line() . " frame called consecutively " . $raw_intent . "\n"; return;
  }
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  $param1 = trim(substr($params,0,strpos($params,"|"))); $param2 = trim(substr($params,1+strpos($params,"|")));
  if ($params == "") echo "ERROR - " . current_line() . " name missing for " . $raw_intent . "\n";
  else if (strpos($params,"|")!==false) {
    $GLOBALS['inside_frame']=2; return "{techo('".$raw_intent."');\ncasper.withFrame('".$param1."', function() {casper.withFrame('".$param2."', function() {\n";
  } else {
    $GLOBALS['inside_frame']=1; return "{techo('".$raw_intent."');\ncasper.withFrame('".$params."', function() {\n";
  }
}



*/

}
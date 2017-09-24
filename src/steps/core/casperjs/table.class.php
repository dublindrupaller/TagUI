<?php
/**
 * @file
 *
 */

/**
 *  table class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class table extends step {  
       
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

    if (substr($intent,0,6)=="table "){
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
    $param1 = trim(substr($params,0,strpos($params," to "))); 
    $param2 = trim(substr($params,4+strpos($params," to ")));

    if ($params == "") echo "ERROR - " . current_line() . " target missing for " . $raw_intent . "\n";
    else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');".beg_tx($param1). "save_table('".abs_file($param2)."',tx('".$param1."'));".end_tx($param1); 
    else return "{techo('".$raw_intent."');".beg_tx($params)."save_table('',tx('".$params."'));".end_tx($params);
  }
}

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function table_intent($raw_intent) {
  $twb = $GLOBALS['tagui_web_browser'];
  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  $param1 = trim(substr($params,0,strpos($params," to "))); $param2 = trim(substr($params,4+strpos($params," to ")));
  if ($params == "") echo "ERROR - " . current_line() . " target missing for " . $raw_intent . "\n";
  else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');".beg_tx($param1). "save_table('".abs_file($param2)."',tx('".$param1."'));".end_tx($param1); 
  else return "{techo('".$raw_intent."');".beg_tx($params)."save_table('',tx('".$params."'));".end_tx($params);
}



*/
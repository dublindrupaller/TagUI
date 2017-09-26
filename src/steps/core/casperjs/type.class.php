<?php
/**
 * @file
 *
 */

/**
 *  type class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class type extends step {
      
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

  if ((substr($intent,0,5)=="type ") || (substr($intent,0,6)=="enter ")) {
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
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params," as "))); 
    $param2 = trim(substr($params,4+strpos($params," as ")));

    if (is_sikuli($param1) and $param2 != "") {
      $abs_param1 = abs_file($param1); $abs_intent = str_replace($param1,$abs_param1,$raw_intent);
      return call_sikuli($abs_intent,$abs_param1);
    } // use sikuli visual automation as needed
    if (($param1 == "") or ($param2 == "")) echo "ERROR - " . current_line() . " target/text missing for " . $raw_intent . "\n"; 

    else { // special handling for [clear] keyword to clear text field by doing an extra step to clear the field
      if (substr($param2,0,7)=="[clear]") {
        if (strlen($param2)>7) $param2 = substr($param2,7); 
        else $param2 = "";
        $clear_field = $twb.".sendKeys(tx('".$param1."'),'',{reset: true});\n";
      } 
      else $clear_field = "";
      if (strpos($param2,"[enter]")===false) return "{techo('".$raw_intent."');".beg_tx($param1).$clear_field.$twb.".sendKeys(tx('".$param1."'),'".$param2."');".end_tx($param1);
      else {// special handling for [enter] keyword to send enter key events
        $param2 = str_replace("[enter]","',{keepFocus: true});\n" .
        $twb.".sendKeys(tx('".$param1."'),casper.page.event.key.Enter,{keepFocus: true});\n" .
        $twb.".sendKeys(tx('".$param1."'),'",$param2); return "{techo('".$raw_intent."');".beg_tx($param1).$clear_field.
        $twb.".sendKeys(tx('".$param1."'),'".$param2."',{keepFocus: true});".end_tx($param1);
      }
    }
  }    

}

class_alias('type', 'enter');

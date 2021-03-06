<?php
/**
 * @file
 *
 */

/**
 *  check class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public getHeaderJs()
 */

class check extends step {  
  
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

    if (substr($intent,0,6)=="check ") {
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
    $params = str_replace("||"," JAVASCRIPT_OR ",$params); // to handle conflict with "|" delimiter
    $param1 = trim(substr($params,0,strpos($params,"|"))); 
    $param2 = trim(substr($params,1+strpos($params,"|")));
    $param3 = trim(substr($param2,1+strpos($param2,"|"))); 
    $param2 = trim(substr($param2,0,strpos($param2,"|")));
    $param1 = str_replace(" JAVASCRIPT_OR ","||",$param1); // to restore back "||" that were replaced
    $param2 = str_replace(" JAVASCRIPT_OR ","||",$param2); 
    $param3 = str_replace(" JAVASCRIPT_OR ","||",$param3);
    if (substr_count($params,"|")!=2) echo "ERROR - " . current_line() . " if/true/false missing for " . $raw_intent . "\n"; 
    else return "{".parse_condition("if ".$param1)."\nthis.echo(".$param2.");\nelse this.echo(".$param3.");}".end_fi()."\n";
  } 

  public function getHeaderJs() {
    $js = <<<TAGUI
  function check_intent(raw_intent) {
return "this.echo('ERROR - step not supported in live mode, there is no conditions language parser')";}
TAGUI;
    return $js;
  }
}

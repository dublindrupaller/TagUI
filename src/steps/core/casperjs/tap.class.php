<?php
/**
 * @file
 *
 */

/**
 *  tap class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class tap extends step {
      
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

    if ((substr($intent,0,4)=="tap ") || (substr($intent,0,6)=="click ")) {
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
  // TODO: $params is passed as an array but sent to casperjs code and sikuli output as a string   
    if ($sikuli) {
      $abs_params = abs_file($params); 
      $abs_intent = str_replace($params,$abs_params,$raw_intent);
      $parsed_code =  call_sikuli($abs_intent,$abs_params);
    } else {
      $parsed_code = "{techo('".$raw_intent."');".beg_tx($params).$twb.".click(tx('" . $params . "'));".end_tx($params);       
    }    
    return $parsed_code;
  }    
}
class_alias('tap', 'click');
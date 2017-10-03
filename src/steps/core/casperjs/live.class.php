<?php
/**
 * @file
 *
 */

/**
 *  live class which is a child of step
 *
 *  The class contains four methods:
 *  - __construct
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js()
 */

class live extends step {  
  
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

    if (substr($intent,0,5)=="live "){
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
    return "{var live_input = ''; var sys = require('system'); sys.stdout.write('LIVE MODE - type done to quit\\n \\b');\n"."while (true) {live_input = sys.stdin.readLine(); // evaluate input in casperjs context until done is entered\n"."if (live_input.indexOf('done') == 0) break; eval(tagui_parse(live_input));}}".end_fi()."\n";
  }


  public function get_header_js() {
    $js = <<<TAGUI
function live_intent(raw_intent) {
return "this.echo('ERROR - you are already in live mode, type done to quit live mode')";}
TAGUI;
    return $js;
  }    

}

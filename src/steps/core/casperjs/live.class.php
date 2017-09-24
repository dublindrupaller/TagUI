<?php
/**
 * @file
 *
 */

/**
 *  live class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
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

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function live_intent($raw_intent) { // live mode to interactively test tagui steps and js code (casperjs context)
  return "{var live_input = ''; var sys = require('system'); sys.stdout.write('LIVE MODE - type done to quit\\n \\b');\n"."while (true) {live_input = sys.stdin.readLine(); // evaluate input in casperjs context until done is entered\n"."if (live_input.indexOf('done') == 0) break; eval(tagui_parse(live_input));}}".end_fi()."\n";
}





*/

}
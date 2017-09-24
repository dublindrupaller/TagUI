<?php
/**
 * @file
 *
 */

/**
 *  test class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class test extends step {  
    
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

    if (substr($intent,0,5)=="test ") {
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
    echo "ERROR - " . current_line() . " use CasperJS tester module to professionally " . $raw_intent . "\n";
    echo "ERROR - " . current_line() . " info at http://docs.casperjs.org/en/latest/modules/tester.html" . "\n";
    echo "ERROR - " . current_line() . " support CSS selector or tx('selector') for XPath algo by TagUI" . "\n";
  }
}

// notes from GUS
// The above depends on the assumption that the checking of $params not being empty happens before the call to the parseIntent() method
// same goes for the sikuli check and establishing $twb.



/*  copying in the original php function from tagui_parse.php for john to review oop version.

function test_intent($raw_intent) {
  echo "ERROR - " . current_line() . " use CasperJS tester module to professionally " . $raw_intent . "\n";
  echo "ERROR - " . current_line() . " info at http://docs.casperjs.org/en/latest/modules/tester.html" . "\n";
  echo "ERROR - " . current_line() . " support CSS selector or tx('selector') for XPath algo by TagUI" . "\n";
}




*/
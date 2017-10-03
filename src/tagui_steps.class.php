

<?php
/**
 * @file 
 *   Factory class for parsing Tagui
 *   
 */
class tagui {
  public $driver;
  public $classes;
  
  function __construct($driver) {
    $this->driver = $driver;
    $this->getClasses();
    $this->loadSteps();    
  }
  
  protected function getClasses() {
    $directories['core']     = 'steps/core/' . $this->driver;
    $directories['custom']   = 'steps/custom/' . $this->driver;
    
    foreach ($directories as $directory) {
      foreach (new DirectoryIterator($directory) as $fileInfo) {
        // ignore dot files
        if($fileInfo->isDot()) continue;
        $matches = array();
        if (preg_match('/^(?<class>[^.].+)\.class.php$/', $fileInfo->getFilename(), $matches)) {
          $class= $matches['class'];
          $this->classes[$class] = $fileInfo->getPathname();        
        }
      }
    }
  }
  
  protected function loadSteps() {
    // Need to load the abastract class first
    include_once('steps/step.class.php');
    
    foreach ($this->classes as $class => $path) {
      include_once($path);
      $this->steps[$class] = new $class($class);
      $this->headerjs[] = $this->steps[$class]->getHeaderJs();
    }
  }
  
  public function getIntent($intent) {
    foreach ($this->steps as $class) {
      if ($return = $class->getIntent($intent)) {
        break;
      }
    }
    return $return;
  }
  
  public function parseIntent($intent, $script_line, $twb, $sikuli = FALSE) {
    //print "DEBUG: intent= ". $intent ." script_line=". $script_line ." twb=". $twb ." sikuli=". $sikuli. " end of line.". PHP_EOL;
    return $this->steps[$intent]->parseIntent($intent, $script_line, $twb, $sikuli = FALSE);
  }

  public function getHeaderJs($live_mode) {
    $return = FALSE; 
    if (!empty($this->headerjs) && $live_mode == TRUE) {
      $return = implode("\n", $this->headerjs);
    }
    elseif ($live_mode == TRUE) {
      die("ERROR - cannot open tagui_header.js or LIVE MODE is OFF" . "\n");
    }
    return $return;
  }
  
}

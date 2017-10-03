<?php
/**
 * @file
 *
 */
/**
 * abstract class step
 *  Base class which should not be called directly,
 *
 *  The class contains four methods:
 *  - __construct
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js()
 */
abstract class step {
  public $intent;
  /**
   * @return string
   */
  abstract public function getIntent($intent);
  /**
   * @return casperjs code as string
   *
   */
  abstract public function parseIntent($intent, $raw_intent, $twb, $sikuli = FALSE);

/**
   * @return casperjs code as string
   *
   */
  abstract public function getHeaderJs();
    
}

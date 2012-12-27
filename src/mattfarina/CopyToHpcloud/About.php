<?php

/**
 * Generate the about text for the application.
 */

namespace mattfarina\CopyToHpcloud;

/**
 * Generates the about text.
 */
class About extends \Fortissimo\Command\Base {

  public function expects() {
    return $this
      ->description('The about text.')
      ->usesParam('version', 'The version of the application to display.')
      ->andReturns('The help text block.')
      ;
  }

  public function doCommand() {

    $version = $this->param('version', '');

    return '<info>Copy To HP Cloud Version: ' . $version . ".</info>

This application is a proof of concept and utility function for transferring
files more quickly to HP Cloud Object Storage. It is MIT licensed.

For more details see http://github.com/mattfarina/copy-to-hpcloud.";
  }
}
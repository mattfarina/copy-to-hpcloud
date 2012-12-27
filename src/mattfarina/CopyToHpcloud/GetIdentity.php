<?php
/**
 * @file
 * Get an identity services object.
 */

namespace mattfarina\CopyToHpcloud;

use HPCloud\Services\IdentityServices;

/**
 * Gets an identity services object and puts it on the context.
 */
class GetIdentity extends \Fortissimo\Command\Base {

  public function expects() {
    return $this
      ->description('Adds the CLI options for copying to hpcloud.')
      ->usesParam('access', 'An object of Symfony\Component\Console\Input\InputDefinition.')
        ->whichIsRequired()
      ->usesParam('secret', 'An object of Symfony\Component\Console\Input\InputDefinition.')
        ->whichIsRequired()
      ->usesParam('tenantid', 'An object of Symfony\Component\Console\Input\InputDefinition.')
        ->whichIsRequired()
      ->usesParam('verbose', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->usesParam('endpoint', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->andReturns('An instance of \HPCloud\Services\IdentityServices.')
      ;
  }

  public function doCommand() {

    $access = $this->param('access');
    $secret = $this->param('secret');
    $tenantid = $this->param('tenantid');
    $verbose = $this->param('verbose', FALSE);
    $endpoint = $this->param('endpoint', 'https://region-a.geo-1.identity.hpcloudsvc.com:35357/v2.0/');
    
    $output = $this->context->datasource('output');

    $is = new IdentityServices($endpoint);

    try {
      $token = $is->authenticateAsAccount($access, $secret, $tenantid);

      if ($verbose) {
        $output->writeln("<info>Authentication complete.</info>");
      }
    } catch (\Exception $e) {
      $output->writeln("<error>Authentication error: " . $e->getMessage() ."</error>");
      die();
    }

    return $is;

  }
}
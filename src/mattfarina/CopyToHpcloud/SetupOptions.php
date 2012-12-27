<?php
/**
 * @file
 * A fortissimo command to setup the CLI options.
 */

namespace mattfarina\CopyToHpcloud;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Setup the cli options 
 */
class SetupOptions extends \Fortissimo\Command\Base {

  public function expects() {
    return $this
      ->description('Adds the CLI options for copying to hpcloud.')
      ->usesParam('inputDefinition', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->andReturns('Nothing.')
      ;
  }

  public function doCommand() {

    $id = $this->param('inputDefinition');

    $id->addArgument(new InputArgument('file', InputArgument::REQUIRED, 'The local file or directory to copy to HP Cloud Object Storage.'));
    $id->addArgument(new InputArgument('container', InputArgument::REQUIRED, 'The remote container to copy the files to.'));

    $id->addOption(new InputOption('access', 'a', InputOption::VALUE_REQUIRED, 'An access key id.'));
    $id->addOption(new InputOption('secret', 's', InputOption::VALUE_REQUIRED, 'The secret key associated with the access key id.'));
    $id->addOption(new InputOption('tenantid', 't', InputOption::VALUE_REQUIRED, 'The tenant id associated with this container.'));
    $id->addOption(new InputOption('region', 'r', InputOption::VALUE_OPTIONAL, 'The object storage region to copy to.', 'region-a.geo-1'));
    $id->addOption(new InputOption('create', 'c', InputOption::VALUE_NONE, 'If a container should be created when one does not exist at that name.'));
    $id->addOption(new InputOption('verbose', 'v', InputOption::VALUE_NONE, 'Display verbose info when transfering.'));
    $id->addOption(new InputOption('endpoint', 'e', InputOption::VALUE_OPTIONAL, 'The identity services endpoint.', 'https://region-a.geo-1.identity.hpcloudsvc.com:35357/v2.0/'));
  }
}
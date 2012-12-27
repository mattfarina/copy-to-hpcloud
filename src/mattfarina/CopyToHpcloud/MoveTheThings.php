<?php
/**
 * @file
 * A fortissimo command to move the files to object storage.
 */

namespace mattfarina\CopyToHpcloud;

use HPCloud\Storage\ObjectStorage;
use \HPCloud\Storage\ObjectStorage\Object;
use Dflydev\ApacheMimeTypes\FlatRepository;

/**
 * A command to move the local files to object storage.
 *
 * Some notes:
 * - This command is way to long. It should be broken out into multiple commands
 *   and turned into a library to do this stuff. Thats for another day.
 */
class MoveTheTHings extends \Fortissimo\Command\Base {

  public function expects() {
    return $this
      ->description('Adds the CLI options for copying to hpcloud.')
      ->usesParam('file', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->usesParam('container', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->usesParam('ident', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->usesParam('region', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->usesParam('create', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->usesParam('verbose', 'An object of Symfony\Component\Console\Input\InputDefinition.')
      ->andReturns('Nothing.')
      ;
  }

  public function doCommand() {

    $files = $this->param('file');
    $container = $this->param('container');
    $is = $this->param('ident');
    $region = $this->param('region');
    $create = $this->param('create');
    $this->verbose = $this->param('verbose');
    $this->output = $this->context->datasource('output');

    // Setup the mime lookup
    $this->mimeLookup = new FlatRepository();

    // Make sure we can connect to object storage in a region.
    // We don't use the newFromIdentity method because its ability to pass a
    // region in is currently borked.
    $cat = $is->serviceCatalog();
    $tok = $is->token();
    $store = ObjectStorage::newFromServiceCatalog($cat, $tok, $region);

    if ($store && $this->verbose) {
      $this->output->writeln('<info>Connected to object storage.</info>');
    }
    if (!$store) {
      $this->output->writeln("<error>Unable to connect to region.</error>");
      die();
    }

    // Make sure the container exists. If not, create it if flagged to do so.
    if (!$store->hasContainer($container)) {
      if ($create) {
        $ret = $store->createContainer($container);
        if ($ret) {
          $this->output->writeln("<info>Created container: " . $container . "</info>");
        }
        else {
          $this->output->writeln("<error>Unable to create container.</error>");
        }
      }
      else {
        $this->output->writeln("<error>The container does not exist. Use --create to create the container.</error>");
      }
    }

    // Get the container
    $this->containerStore = $store->container($container);

    // Transfer the files
    if (is_file($files)) {
      $this->uploadObject($files);
    }
    else {
      $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($files));
      foreach ($it as $path => $obj) {
        $this->uploadObject($path);
      }
    }

  }

  /**
   * Get the mime type for a file or fall back to the default.
   *
   * @param string $ext
   *   A file extension
   * @param string $default
   *   The default type to use if none is found.
   *
   * @return string
   *   A mime type for a given extension.
   */
  public function lookupType($ext, $default = 'application/octet-stream') {
    
    if ($type = $this->mimeLookup->findType($ext)) {
      return $type;
    }

    return $default;
  }

  /**
   * Clean up a file system path so it can be used in a path.
   *
   * @param string $filename
   *   The name of file
   *
   * @return
   *   A sanitized version of the name.
   */
  public function normalizeFilename($filename) {
    $ds = strpos($filename, './');
    if ($ds !== FALSE && $ds < 2) {
      $filename = substr($filename, $ds + 2);
    }
    $parts = explode('/', $filename);
    $res = array_map('rawurlencode', $parts);
    return implode('/', $res);
  }

  /**
   * Upload an object to object storage.
   *
   * @param string $name
   *   Path to the file to upload
   */
  function uploadObject($name) {
    if ($this->verbose) {
      $this->output->writeln("Uploading: " . $name);
    }
    $objname = $this->normalizeFilename($name);
    $tmp = new \SplFileInfo($name);
    $mimeType = $this->lookupType($tmp->getExtension());

    $tmp_obj = new Object($objname);
    $tmp_obj->setContentType($mimeType);

    $ret = $this->containerStore->save($tmp_obj, fopen($name, 'rb'));
  }
}
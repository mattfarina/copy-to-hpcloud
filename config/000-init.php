<?php
/**
 * @file
 * Master configuration file. This should be included first.
 */
global $registry, $argv, $argvOffset;

$registry->route('copy', 'Copy files from a local filesystem to HP Cloud Object Storage.')
  ->does('\Fortissimo\CLI\SetupOptions', 'CliOptions')
  ->does('\mattfarina\CopyToHpcloud\SetupOptions')
    ->using('inputDefinition')->from('cxt:CliOptions')
  ->does('\Fortissimo\CLI\ParseOptions', 'opts')
    ->using('help', 'Copy an object to object storage.')
    ->using('usage', '<comment>Usage:</comment> ' . $argv[0]. ' copy files container [--OPTIONS]')
    ->using('optionSpec')->from('cxt:CliOptions')
    ->using('options', $argv)
  ->does('\mattfarina\CopyToHpcloud\GetIdentity', 'ident')
    ->using('access')->from('cxt:access')
    ->using('secret')->from('cxt:secret')
    ->using('tenantid')->from('cxt:tenantid')
    ->using('verbose')->from('cxt:verbose')
    ->using('endpoint')->from('cxt:endpoint')
  ->does('\mattfarina\CopyToHpcloud\MoveTheThings')
    ->using('ident')->from('cxt:ident')
    ->using('file')->from('cxt:file')
    ->using('container')->from('cxt:container')
    ->using('region')->from('cxt:region')
    ->using('verbose')->from('cxt:verbose')
    ->using('create')->from('cxt:create')
  ;

$registry->route('self-update', 'Update the application if there is a newer version available.')
  ->does('\Fortissimo\CLI\Update\GetVersionFromTextFile', 'version1')
    ->using('file', FORT_APP_PATH .'/VERSION')
  ->does('\Fortissimo\CLI\Update\GetVersionFromTextFile', 'version2')
    ->using('file', 'http://download.mattfarina.com/copy-to-hpcloud/VERSION')
  ->does('\Fortissimo\CLI\Update\CompareVersions', 'versionDiff')
    ->using('version1')->from('cxt:version1')
    ->using('version2')->from('cxt:version2')
  ->does('\Fortissimo\CLI\Update\Update')
    ->using('file', 'http://download.mattfarina.com/copy-to-hpcloud/copy-to-hpcloud')
    ->using('doUpdate')->from('cxt:versionDiff')
  ;

$registry->route('about', "Display information about the App.")
  ->does('\Fortissimo\CLI\Update\GetVersionFromTextFile', 'version')
    ->using('file', FORT_APP_PATH .'/VERSION')
  ->does('\mattfarina\CopyToHpcloud\About', 'helpText')
    ->using('version')->from('cxt:version')
  ->does('\Fortissimo\CLI\IO\Write')
    ->using('text')->from('cxt:helpText')
  ;

$registry->route('help', "Show the help text.")
  ->does('\Fortissimo\CLI\ShowHelp');

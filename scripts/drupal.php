#!/usr/bin/env php
<?php declare(strict_types = 1);

/**
 * @file
 * Drupal CLI tool.
 *
 * Drush depends on specific version of DCG and therefore in some cases it
 * cannot be installed via Composer because of version conflict. This script
 * provides a very simple replacing for some common Drush commands.
 *
 * This script must be executed from the Drupal root directory.
 *
 * @internal
 */

use Composer\InstalledVersions;
use Drupal\Core\DrupalKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Request;

$class_loader = require_once __DIR__ . '/autoload.php';

$root_package = InstalledVersions::getRootPackage();
\chdir($root_package['install_path']);
$request = Request::createFromGlobals();
$kernel = DrupalKernel::createFromRequest($request, $class_loader, 'prod');
$kernel->boot();
$kernel->preHandle($request);
// Cancel Drupal error handler and send the errors to STDOUT immediately.
\restore_error_handler();
\error_reporting(\E_ALL);

$module_installer = $kernel->getContainer()->get('module_installer');

$module_install = static fn (InputInterface $input): int =>
$module_installer->install([$input->getArgument('module')]) ? Command::SUCCESS : Command::FAILURE;

$module_uninstall = static fn (InputInterface $input): int =>
$module_installer->uninstall([$input->getArgument('module')]) ? Command::SUCCESS : Command::FAILURE;

(new Application('Drupal CLI'))

  ->register('module:install')
  ->addArgument('module', InputArgument::REQUIRED)
  ->setCode($module_install)
  ->getApplication()

  ->register('module:uninstall')
  ->addArgument('module', InputArgument::REQUIRED)
  ->setCode($module_uninstall)
  ->getApplication()

  ->register('cache:flush')
  ->setCode('drupal_flush_all_caches')
  ->getApplication()

  ->run();

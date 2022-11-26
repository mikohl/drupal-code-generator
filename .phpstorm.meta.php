<?php /** @noinspection ALL */

namespace PHPSTORM_META {

  use DrupalCodeGenerator\Helper\Dumper\DumperInterface;

  override(
    \Symfony\Component\Console\Helper\HelperSet::get(0),
    map([
      'service_info' => \DrupalCodeGenerator\Helper\Drupal\ServiceInfo::class,
      'module_info' => \DrupalCodeGenerator\Helper\Drupal\ModuleInfo::class,
      'theme_info' => \DrupalCodeGenerator\Helper\Drupal\ThemeInfo::class,
      'hook_info' => \DrupalCodeGenerator\Helper\Drupal\HookInfo::class,
      'route_info' => \DrupalCodeGenerator\Helper\Drupal\RouteInfo::class,
      'permission_info' => \DrupalCodeGenerator\Helper\Drupal\PermissionInfo::class,
      'config_info' => \DrupalCodeGenerator\Helper\Drupal\ConfigInfo::class,
      'dry_dumper' => \DrupalCodeGenerator\Helper\Dumper\DumperInterface::class,
      'filesytem_dumper' => \DrupalCodeGenerator\Helper\Dumper\DumperInterface::class,
      'renderer' => \DrupalCodeGenerator\Helper\Renderer\RendererInterface::class,
      'question' => \DrupalCodeGenerator\Helper\QuestionHelper::class,
      'assets_table_printer' => \DrupalCodeGenerator\Helper\Printer\PrinterInterface::class,
      'assets_list_printer' => \DrupalCodeGenerator\Helper\Printer\PrinterInterface::class,
    ]),
  );

  override(
    \Symfony\Component\Console\Command\Command::getHelper(0),
    map([
      'service_info' => \DrupalCodeGenerator\Helper\Drupal\ServiceInfo::class,
      'module_info' => \DrupalCodeGenerator\Helper\Drupal\ModuleInfo::class,
      'theme_info' => \DrupalCodeGenerator\Helper\Drupal\ThemeInfo::class,
      'hook_info' => \DrupalCodeGenerator\Helper\Drupal\HookInfo::class,
      'route_info' => \DrupalCodeGenerator\Helper\Drupal\RouteInfo::class,
      'permission_info' => \DrupalCodeGenerator\Helper\Drupal\PermissionInfo::class,
      'config_info' => \DrupalCodeGenerator\Helper\Drupal\ConfigInfo::class,
      'dry_dumper' => \DrupalCodeGenerator\Helper\Dumper\DumperInterface::class,
      'filesytem_dumper' => \DrupalCodeGenerator\Helper\Dumper\DumperInterface::class,
      'renderer' => \DrupalCodeGenerator\Helper\Renderer\RendererInterface::class,
      'question' => \DrupalCodeGenerator\Helper\QuestionHelper::class,
      'assets_table_printer' => \DrupalCodeGenerator\Helper\Printer\PrinterInterface::class,
      'assets_list_printer' => \DrupalCodeGenerator\Helper\Printer\PrinterInterface::class,
    ])
  );

}

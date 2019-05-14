<?php

namespace DrupalCodeGenerator\Command\Drupal_8;

use DrupalCodeGenerator\Command\ModuleGenerator;
use DrupalCodeGenerator\Utils;

/**
 * Implements d8:plugin-manager command.
 */
class PluginManager extends ModuleGenerator {

  protected $name = 'd8:plugin-manager';
  protected $description = 'Generates plugin manager';
  protected $alias = 'plugin-manager';

  /**
   * {@inheritdoc}
   */
  protected function generate() :void {
    $vars = &$this->collectDefault();

    // Validator::validateMachineName does not allow dots. But they can appear
    // in plugin types (field.widget, views.argument, etc).
    $plugin_type_validator = function ($value) {
      if (!preg_match('/^[a-z][a-z0-9_\.]*[a-z0-9]$/', $value)) {
        throw new \UnexpectedValueException('The value is not correct machine name.');
      }
      return $value;
    };
    $vars['plugin_type'] = $this->ask('Plugin type', '{machine_name}', $plugin_type_validator);

    $discovery_types = [
      'annotation' => 'Annotation',
      'yaml' => 'YAML',
      'hook' => 'Hook',
    ];
    $vars['discovery'] = $this->choice('Discovery type', $discovery_types, 'Annotation');
    $vars['class_prefix'] = '{plugin_type|camelize}';

    $common_files = [
      'model.services.yml',
      'src/ExampleInterface.php',
      'src/ExamplePluginManager.php',
    ];

    $files = [];
    switch ($vars['discovery']) {
      case 'annotation':
        $files = [
          'src/Annotation/Example.php',
          'src/ExamplePluginBase.php',
          'src/Plugin/Example/Foo.php',
        ];
        break;

      case 'yaml':
        $files = [
          'model.examples.yml',
          'src/ExampleDefault.php',
        ];
        break;

      case 'hook':
        $files = [
          'model.module',
          'src/ExampleDefault.php',
        ];
        break;
    }

    $files = array_merge($common_files, $files);

    $templates_path = 'd8/plugin-manager/{discovery}/';

    $path_placeholders = ['model', 'Example', 'examples'];
    $path_replacements = [
      $vars['machine_name'],
      $vars['class_prefix'],
      Utils::pluralize($vars['plugin_type']),
    ];

    foreach ($files as $file) {
      $asset = $this->addFile()
        ->path(str_replace($path_placeholders, $path_replacements, $file))
        ->template($templates_path . $file);
      if ($file === 'model.services.yml') {
        $asset->action('append')->headerSize(1);
      }
      elseif ($file == 'model.module') {
        $asset
          ->action('append')
          ->headerTemplate('d8/file-docs/module')
          ->headerSize(7);
      }
    }
  }

}

<?php

namespace DrupalCodeGenerator\Command\Drupal_8;

use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\Utils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Implements d8:composer command.
 */
class Project extends BaseGenerator {

  protected $name = 'd8:project';
  protected $description = 'Generates a composer project';
  protected $alias = 'project';

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {

    $name_validator = function ($value) {
      if (!preg_match('#[^/]+/[^/]+$#i', $value)) {
        throw new \UnexpectedValueException('The value is not correct project name.');
      }
      return $value;
    };
    $questions['name'] = new Question('Project name (vendor/name)', FALSE);
    $questions['name']->setValidator($name_validator);

    $questions['description'] = new Question('Description', FALSE);

    $questions['license'] = new Question('License', 'proprietary');
    $licenses = [
      'Apache-2.0',
      'BSD-2-Clause',
      'BSD-3-Clause',
      'BSD-4-Clause',
      'GPL-2.0-only',
      'GPL-2.0-or-later',
      'GPL-3.0-only',
      'GPL-3.0-or-later',
      'LGPL-2.1-onl',
      'LGPL-2.1-or-later',
      'LGPL-3.0-only',
      'LGPL-3.0-or-later',
      'MIT',
      'proprietary',
    ];
    $questions['license']->setAutocompleterValues($licenses);

    $document_roots = [
      'docroot',
      'web',
      'www',
      'public_html',
      'public',
      'htdocs',
      'httpdocs',
      'html',
    ];
    $questions['document_root'] = new Question('Document root directory, type single dot to use Composer root', 'docroot');
    $questions['document_root']->setNormalizer(function ($value) {
      return $value == '.' ? '' : $value;
    });
    $questions['document_root']->setAutocompleterValues($document_roots);

    $questions['php'] = new Question('PHP version', '>=' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION);

    $stabilities = [
      'stable',
      'RC',
      'beta',
      'alpha',
      'dev',
    ];
    $questions['stability'] = new Question('Minimum Stability', 'dev');
    $questions['stability']->setValidator(Utils::getOptionsValidator($stabilities));
    $questions['stability']->setAutocompleterValues($stabilities);

    $this->collectVars($input, $output, $questions);

    $sections = ['require', 'require-dev'];
    $questions['drush'] = new ConfirmationQuestion('Would you like to install Drush?', TRUE);
    $vars = $this->collectVars($input, $output, $questions);
    if ($vars['drush']) {
      $questions['drush_installation'] = new Question('Drush installation (require|require-dev)', 'require');
      $questions['drush_installation']->setValidator(Utils::getOptionsValidator($sections));
      $questions['drush_installation']->setAutocompleterValues($sections);
      $this->collectVars($input, $output, $questions);
    }

    $questions['drupal_console'] = new ConfirmationQuestion('Would you like to install Drupal Console?', !$vars['drush']);
    $vars = $this->collectVars($input, $output, $questions);
    if ($vars['drupal_console']) {
      $questions['drupal_console_installation'] = new Question('Drupal Console installation (require|require-dev)', 'require-dev');
      $questions['drupal_console_installation']->setValidator(Utils::getOptionsValidator($sections));
      $questions['drupal_console_installation']->setAutocompleterValues($sections);
      $this->collectVars($input, $output, $questions);
    }

    $questions['composer_patches'] = new ConfirmationQuestion('Would you like to install Composer patches plugin?', TRUE);
    $questions['composer_merge'] = new ConfirmationQuestion('Would you like to install Composer merge plugin?', FALSE);
    $questions['behat'] = new ConfirmationQuestion('Would you like to create Behat tests?', FALSE);

    $vars = &$this->collectVars($input, $output, $questions);

    $vars['document_root_path'] = $vars['document_root'] ?
      $vars['document_root'] . '/' : $vars['document_root'];

    $this->addFile()
      ->path('composer.json')
      ->content(self::buildComposerJson($vars));

    $this->addFile()
      ->path('.gitignore')
      ->template('d8/_project/gitignore.twig');

    if ($vars['drupal_coder']) {
      $this->addFile()
        ->path('phpcs.xml')
        ->template('d8/_project/phpcs.xml.twig');
    }

    if ($vars['behat']) {
      $this->addFile()
        ->path('tests/behat/behat.yml')
        ->template('d8/_project/tests/behat/behat.yml.twig');

      $this->addFile()
        ->path('tests/behat/local.behat.yml')
        ->template('d8/_project/tests/behat/local.behat.yml.twig');

      $this->addFile()
        ->path('tests/behat/bootstrap/BaseContext.php')
        ->template('d8/_project/tests/behat/bootstrap/BaseContext.php.twig');

      $this->addFile()
        ->path('tests/behat/bootstrap/ExampleContext.php')
        ->template('d8/_project/tests/behat/bootstrap/ExampleContext.php.twig');

      $this->addFile()
        ->path('tests/behat/features/example/user_forms.feature')
        ->template('d8/_project/tests/behat/features/example/user_forms.feature.twig');
    }

    $this->addFile()
      ->path('patches/.keep')
      ->content('');

    $this->addDirectory()
      ->path('scripts');

    if ($vars['document_root']) {
      $this->addDirectory()
        ->path('config/sync');
    }

    if ($vars['drush']) {
      $this->addFile()
        ->path('drush/drush.yml')
        ->template('d8/_project/drush/drush.yml.twig');
      $this->addFile()
        ->path('drush/Commands/PolicyCommands.php')
        ->template('d8/_project/drush/Commands/PolicyCommands.php.twig');
      $this->addFile()
        ->path('drush/sites/self.site.yml')
        ->template('d8/_project/drush/sites/self.site.yml.twig');
    }

    $this->addDirectory()
      ->path($vars['document_root_path'] . 'modules/contrib');

    $this->addDirectory()
      ->path($vars['document_root_path'] . 'modules/custom');

    $this->addDirectory()
      ->path($vars['document_root_path'] . 'themes');

    $this->addDirectory()
      ->path($vars['document_root_path'] . 'libraries');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $result = parent::execute($input, $output);
    if ($result === 0) {
      $output->writeln(' <info>Review <comment>composer.json</comment> file and run <comment>composer install</comment> command.</info>');
    }
    return $result;
  }

  /**
   * Builds composer.json file.
   *
   * @param array $vars
   *   Collected variables.
   *
   * @return string
   *   Encoded JSON content.
   */
  protected static function buildComposerJson(array $vars) {

    $composer_json = [];

    $composer_json['name'] = $vars['name'];
    $composer_json['description'] = $vars['description'];
    $composer_json['type'] = 'project';
    $composer_json['license'] = $vars['license'];
    $composer_json['prefer-stable'] = TRUE;
    $composer_json['minimum-stability'] = $vars['stability'];

    $composer_json['repositories'][] = [
      'type' => 'composer',
      'url' => 'https://packages.drupal.org/8',
    ];

    $require = [];
    $require_dev = [];

    self::addPackage($require, 'drupal/core');
    self::addPackage($require, 'composer/installers');
    self::addPackage($require, 'drupal-composer/drupal-scaffold');
    self::addPackage($require_dev, 'webflo/drupal-core-require-dev');

    if ($vars['drush']) {
      $vars['drush_installation'] == 'require'
        ? self::addPackage($require, 'drush/drush')
        : self::addPackage($require_dev, 'drush/drush');
    }

    if ($vars['drupal_console']) {
      $vars['drupal_console_installation'] == 'require'
        ? self::addPackage($require, 'drupal/console')
        : self::addPackage($require_dev, 'drupal/console');
    }

    if ($vars['composer_patches']) {
      self::addPackage($require, 'cweagans/composer-patches');
    }

    if ($vars['composer_merge']) {
      self::addPackage($require, 'wikimedia/composer-merge-plugin');
    }

    if ($vars['behat']) {
      // Behat and Mink drivers are Drupal core dev dependencies.
      self::addPackage($require_dev, 'drupal/drupal-extension');
    }

    $composer_json['require'] = [
      'php' => $vars['php'],
      'ext-curl' => '*',
      'ext-gd' => '*',
      'ext-json' => '*',
    ];

    ksort($require);
    $composer_json['require'] += $require;
    ksort($require_dev);
    $composer_json['require-dev'] = $require_dev;

    // PHPUnit is core dev dependency.
    $composer_json['scripts']['phpunit'] = 'phpunit --colors=always --configuration ' . $vars['document_root_path'] . 'core ' . $vars['document_root_path'] . 'modules/custom';
    if ($vars['behat']) {
      $composer_json['scripts']['behat'] = 'behat --colors --config=tests/behat/local.behat.yml';
    }
    $composer_json['scripts']['phpcs'] = 'phpcs --standard=phpcs.xml';

    // @todo Remove this once Drupal 8.6.14 released.
    $composer_json['conflict']['symfony/http-foundation'] = '3.4.24';

    $composer_json['config'] = [
      'sort-packages' => TRUE,
      'bin-dir' => 'bin',
    ];

    if ($vars['composer_patches']) {
      $composer_json['extra']['composer-exit-on-patch-failure'] = TRUE;
    }

    $composer_json['extra']['installer-paths'] = [
      $vars['document_root_path'] . 'core' => ['type:drupal-core'],
      $vars['document_root_path'] . 'libraries/{$name}' => ['type:drupal-library'],
      $vars['document_root_path'] . 'modules/contrib/{$name}' => ['type:drupal-module'],
      $vars['document_root_path'] . 'themes/{$name}' => ['type:drupal-theme'],
      'drush/{$name}' => ['type:drupal-drush'],
    ];

    $composer_json['extra']['drupal-scaffold']['excludes'] = [
      '.csslintrc',
      '.editorconfig',
      '.eslintignore',
      '.eslintrc.json',
      '.gitattributes',
      '.ht.router.php',
      '.htaccess',
      'robots.txt',
      'update.php',
      'web.config',
    ];
    // Initial files are created but never updated.
    $composer_json['extra']['drupal-scaffold']['initial'] = [
      '.htaccess' => '.htaccess',
      'robots.txt' => 'robots.txt',
    ];
    if ($vars['document_root']) {
      $composer_json['extra']['drupal-scaffold']['initial']['.editorconfig'] = '../.editorconfig';
      $composer_json['extra']['drupal-scaffold']['initial']['.gitattributes'] = '../.gitattributes';
    }
    ksort($composer_json['extra']['drupal-scaffold']['initial']);

    if ($vars['composer_merge']) {
      $composer_json['extra']['merge-plugin'] = [
        'include' => [
          $vars['document_root_path'] . 'modules/custom/*/composer.json',
        ],
        'recurse' => TRUE,
      ];
    }

    return json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
  }

  /**
   * Requires a given package.
   *
   * @param array $section
   *   Section for the package (require|require-dev)
   *
   * @param $package
   *   A package to be added.
   *
   * @todo Find a way to track versions automatically.
   */
  protected static function addPackage(&$section, $package) {
    $versions = [
      'drupal/core' => '^8.6',
      'composer/installers' => '^1.4',
      'drupal-composer/drupal-scaffold' => '^2.5',
      'webflo/drupal-core-require-dev' => '^8.6',
      'drupal' => '^8.6',
      'drush/drush' => '^9.6',
      'drupal/console' => '^1.0',
      'cweagans/composer-patches' => '^1.6',
      'wikimedia/composer-merge-plugin' => '^1.4',
      'drupal/drupal-extension' => '^3.4',
    ];
    $section[$package] = $versions[$package];
  }

}

<?php

namespace DrupalCodeGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Class BaseGenerator
 * @package DrupalCodeGenerator\Command
 */
class BaseGenerator extends Command {


  protected static $name, $description;


  /**
   *
   */
  public function __construct() {
    parent::__construct();

    $twig_loader = new Twig_Loader_Filesystem(DCG_ROOT_DIR . '/../src/Resources/templates');
    $this->twig = new Twig_Environment($twig_loader);
    $this->fs = new Filesystem();
    $this->directoryBaseName = basename(getcwd());

  }

  /**
   *
   */
  protected function configure() {
    $this
      ->setName(static::$name)
      ->setDescription(static::$description)
      ->addOption(
        'dir',
        '-d',
        InputOption::VALUE_OPTIONAL,
        'Destination directory'
      );
  }

  /**
   * @param $template
   * @param array $vars
   * @return string
   */
  protected function render($template, array $vars) {
    return $this->twig->render($template, $vars);
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @param array $questions
   * @return array
   */
  protected function collectVars(InputInterface $input, OutputInterface $output, array $questions) {

    $vars = [];

    foreach ($questions as $name => $question) {
      list($question_text, $default_value) = $question;

      if (is_callable($default_value)) {
        $default_value = call_user_func($default_value, $vars);
      }

      $vars[$name] = $this->ask(
        $input,
        $output,
        $question_text,
        $default_value,
        empty($question[2])
      );
    }

    return $vars;
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @param $files
   */
  protected function submitFiles(InputInterface $input, OutputInterface $output, $files) {

    $style = new OutputFormatterStyle('black', 'cyan', []);
    $output->getFormatter()->setStyle('title', $style);

    $directory = $input->getOption('dir') ? $input->getOption('dir') . '/' : './';

    foreach($files as $name => $content) {
      try {
        $this->fs->dumpFile($directory . $name, $content);
      }
      catch (IOExceptionInterface $e) {
        $output->writeLn('<error>An error occurred while creating your directory at ' . $e->getPath() . '</error>');
        exit(1);
      }
    }

    $output->writeLn('<title>The following files have been created:</title>');
    foreach ($files as $name => $content) {
      $output->writeLn("[<info>*</info>] $name");
    }

  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @param $question_text
   * @param $default_value
   * @param bool $required
   * @return bool|mixed|null|string|void
   */
  protected function ask(InputInterface $input, OutputInterface $output, $question_text, $default_value, $required = FALSE) {
    /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
    $helper = $this->getHelper('question');

    $question_text = "<info>$question_text</info>";
    if ($default_value) {
      $question_text .= " [<comment>$default_value</comment>]";
    }
    $question_text .= ': ';

    return $helper->ask(
      $input,
      $output,
      new Question($question_text, $default_value)
    );

  }

  /**
   * @param $vars
   * @return string
   */
  protected function getDirectoryBaseName($vars) {
    return $this->directoryBaseName;
  }

  /**
   * @param $vars
   * @return mixed
   */
  protected function default_machine_name($vars) {
    return self::human2machine($this->directoryBaseName);
  }

  /**
   * @param $machine_name
   * @return string
   */
  protected static function machine2human($machine_name) {
    return ucfirst(str_replace('_', ' ', $machine_name));
  }

  /**
   * @param $human_name
   * @return mixed
   */
  protected static function human2machine($human_name) {
    return preg_replace(
      ['/^[0-9]/', '/[^a-z0-9_]+/'],
      '_',
      strtolower($human_name)
    );
  }

}

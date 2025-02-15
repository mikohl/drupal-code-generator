<?php declare(strict_types = 1);

namespace Drupal\foo\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Zoo' formatter.
 *
 * @FieldFormatter(
 *   id = "foo_zoo",
 *   label = @Translation("Zoo"),
 *   field_types = {"string"},
 * )
 */
final class ZooFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->value,
      ];
    }
    return $element;
  }

}

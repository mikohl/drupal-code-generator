<?php declare(strict_types = 1);

namespace PHPSTORM_META {

  registerArgumentsSet('date_formats',
    'fallback',
    'html_date',
    'html_datetime',
    'html_month',
    'html_time',
    'html_week',
    'html_year',
    'html_yearless_date',
    'long',
    'medium',
    'olivero_medium',
    'short',
    'custom',
  );
  expectedArguments(\Drupal\Core\Datetime\DateFormatter::format(), 1, argumentsSet('date_formats'));

  registerArgumentsSet('date_formats_custom',
    \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
    \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface::DATE_STORAGE_FORMAT,
    \DateTimeInterface::ATOM,
    \DateTimeInterface::COOKIE,
    \DateTimeInterface::ISO8601_EXPANDED,
    \DateTimeInterface::RFC822,
    \DateTimeInterface::RFC850,
    \DateTimeInterface::RFC1036,
    \DateTimeInterface::RFC1123,
    \DateTimeInterface::RFC7231,
    \DateTimeInterface::RFC2822,
    \DateTimeInterface::RFC3339,
    \DateTimeInterface::RFC3339_EXTENDED,
    \DateTimeInterface::RSS,
    \DateTimeInterface::W3C,
  );
  expectedArguments(\Drupal\Core\Datetime\DateFormatter::format(), 2, argumentsSet('date_formats_custom'));
  expectedArguments(\DateTimeInterface::format(), 0, argumentsSet('date_formats_custom'));
  expectedArguments(\DateTime::createFromFormat(), 0, argumentsSet('date_formats_custom'));
  expectedArguments(\DateTimeImmutable::createFromFormat(), 0, argumentsSet('date_formats_custom'));

}

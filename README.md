# FPDM-FQB — PHP PDF Form Filling Library

Actively maintained fork of [tmw/fpdm](https://github.com/codeshell/fpdm) (originally by Olivier Plathey), maintained by [Five Quantum Bits](https://fiveqb.com).

The original repository has been unmaintained since 2017. This fork adds critical fixes for modern PHP and real-world PDF templates.

## What's fixed / added

- **Adobe Reader checkbox compatibility** — original only wrote `/AS` (appearance state); Adobe Reader requires `/V` (field value). This fork injects `/V /state` alongside `/AS` so checkboxes render correctly in all readers including Adobe Acrobat/Reader.
- **Nested PDF field support** — PDFs with parent/child field hierarchies (`/Parent` chains, e.g. `clasa_caen.0.1`) are now fully supported. The parser walks the `/Parent` chain and rebuilds full dotted paths, giving access to all widget fields instead of just top-level ones.
- **PDF template compatibility** — improved handling of PDFs with incremental updates and object streams; better error tolerance.
- **PHP 8.x compatibility** — fixes for deprecated functions and type errors introduced since PHP 7.4.

## Installation

```bash
composer require fiveqb/fpdm
```

## Usage

```php
<?php
require 'vendor/autoload.php';

$fields = [
    'name'         => 'John Doe',
    'address'      => '123 Main St',
    'my_checkbox'  => true,   // checked
    'other_checkbox' => false, // unchecked
];

$pdf = new FPDM('template.pdf');
$pdf->useCheckboxParser = true;
$pdf->Load($fields, true); // true = UTF-8
$pdf->Merge();
$pdf->Output();
```

## Notes

- If your template is incompatible, run it through [ilovepdf.com/repair-pdf](https://ilovepdf.com/repair-pdf) to remove object streams.
- Checkbox values: any truthy value = checked, `false` / `0` / `''` = unchecked. You do not need to know the technical state name — it is read from the PDF template automatically.

## Credits

- **Olivier Plathey** — original FPDM script ([fpdf.org](http://www.fpdf.org/en/script/script93.php))
- **codeshell** — tmw/fpdm composer package
- **Five Quantum Bits** — this fork

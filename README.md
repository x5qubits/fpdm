# FPDM-FQB

PHP PDF form filling library — actively maintained fork of [tmw/fpdm](https://github.com/codeshell/fpdm) (original script by Olivier Plathey, 2017).

Maintained by [Five Quantum Bits](https://fiveqb.com).

---

## Why this fork?

The upstream repository has been unmaintained since 2019. Several critical issues were discovered while using it in production with real-world PDF templates (Romanian government forms):

- Checkboxes were invisible in **Adobe Acrobat / Adobe Reader** (worked in browsers and Foxit, not Adobe)
- PDFs with **nested form fields** (`/Parent` hierarchies) were only partially readable — 67 of 195 fields visible
- `create_function()` **crashes on PHP 8.0+** (removed from the language)
- Empty string input in `_bin2hex()` caused fatal errors in PHP 8

---

## Changes vs tmw/fpdm

### 1. Adobe Reader checkbox fix _(critical)_

**Problem:** The original `set_field_checkbox()` only wrote `/AS /StateName` (the appearance stream selector). Adobe Reader ignores `/AS` and reads `/V` (the field value) as authoritative. Result: checkboxes appeared checked in Chrome PDF viewer and Foxit but were always unchecked in Adobe.

**Fix:** After writing `/AS`, the library now also writes `/V /StateName` on the same widget object. If the template has no `/V` entry at all (common in ONRC templates), it is injected inline — without inserting new array elements which would corrupt xref offsets.

```
Before: /AS /Yes          ← Adobe ignores this
After:  /AS /Yes\n/V /Yes ← Adobe reads /V ✓
```

### 2. Nested field support via `/Parent` chain _(major)_

**Problem:** Complex PDF templates use AcroForm field hierarchies — a parent field contains children, children can have grandchildren, etc. Field names in such PDFs look like `clasa_caen.0.3` or `sectiune.row.col`. The original parser only saw top-level objects and missed all nested widgets.

**Fix:** After the main parse loop, the library now:
1. Captures `/Parent` object references during parsing
2. Stores nested widgets under temporary `__obj_X` keys
3. Post-processes to walk each `/Parent` chain and rebuild full dotted paths
4. Replaces temp keys with final paths like `clasa_caen.0.3`

Result: a Romanian ONRC Declaratie Anexa 4 template went from **67 accessible fields** to **195**.

### 3. PHP 8.0+ compatibility _(breaking in original)_

**`create_function()` removed in PHP 8.0**  
The `_set_field_value()` method used `create_function()` to build a callback dynamically. This was deprecated in PHP 7.2 and removed in PHP 8.0 — causing a fatal error on any PHP 8 server.  
Fixed by replacing it with a proper closure using `use ($self, $value)`.

**`_bin2hex()` crash on empty string**  
The function used a `do...while` loop which unconditionally accessed `$str[0]` before checking length. On an empty string this triggers a warning in PHP 7 and a fatal error in PHP 8.  
Fixed with an early return guard and a `for` loop.

---

## Installation

```bash
composer require fiveqb/fpdm
```

Or standalone:

```php
require_once '/path/to/src/fpdm.php';
```

---

## Usage

### Basic — fill text fields

```php
$fields = [
    'name'    => 'Ion Popescu',
    'address' => 'Str. Victoriei nr. 10',
    'date'    => '17.04.2026',
];

$pdf = new FPDM('template.pdf');
$pdf->Load($fields, true); // true = UTF-8
$pdf->Merge();
$pdf->Output(); // stream to browser
```

### Checkboxes (Adobe Reader compatible)

```php
$pdf = new FPDM('template.pdf');
$pdf->useCheckboxParser = true; // required

$pdf->Load([
    'agree_terms' => true,   // checked
    'newsletter'  => false,  // unchecked
    'type_srl'    => true,
    'type_pfa'    => false,
], true);

$pdf->Merge();
$pdf->Output('F', 'output.pdf'); // F = save to file
```

You do **not** need to know the internal checkbox state name (`Yes`, `On`, `31.5`, etc.) — it is read from the template automatically.

### Nested / hierarchical fields

```php
// Fields like "clasa_caen.0.3" work out of the box
$fields = [];
foreach (['6201', '6202', '6311'] as $i => $code) {
    $fields["clasa_caen.0.$i"]      = $code;
    $fields["clasa_caen_desc.0.$i"] = 'Software development';
}

$pdf = new FPDM('template_with_nested_fields.pdf');
$pdf->useCheckboxParser = true;
$pdf->Load($fields, true);
$pdf->Merge();
$pdf->Output('F', 'output.pdf');
```

### Template compatibility

If your PDF throws an error about object streams or incremental updates, run it through [ilovepdf.com/repair-pdf](https://ilovepdf.com/repair-pdf) first. The repaired file is typically smaller and fully compatible.

Pattern for trying multiple candidates:

```php
foreach (['template_repaired.pdf', 'template.pdf'] as $tpl) {
    try {
        $pdf = new FPDM($tpl);
        $pdf->useCheckboxParser = true;
        $pdf->Load($fields, true);
        $pdf->Merge();
        $pdf->Output('F', 'output.pdf');
        break;
    } catch (Exception $e) {
        $lastError = $e->getMessage();
    }
}
```

---

## Output modes

| Mode | Description |
|------|-------------|
| `$pdf->Output()` | Stream to browser (default) |
| `$pdf->Output('F', 'path.pdf')` | Save to file |
| `$pdf->Output('S')` | Return as string |
| `$pdf->Output('D', 'name.pdf')` | Force download |

---

## Requirements

- PHP 7.4 or higher (PHP 8.0, 8.1, 8.2, 8.3 supported)
- No other dependencies

---

## Credits

- **Olivier Plathey** — original FPDM script ([fpdf.org](http://www.fpdf.org/en/script/script93.php))
- **codeshell** — tmw/fpdm Composer package and PHP 7 compatibility
- **Five Quantum Bits** — this fork ([fiveqb.com](https://fiveqb.com))

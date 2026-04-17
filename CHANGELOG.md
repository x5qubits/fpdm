# Changelog

## [1.0.0] — 2026-04-17 (Five Quantum Bits fork)

### Fixed
- **Adobe Reader checkboxes** — `set_field_checkbox()` now writes `/V /StateName` alongside `/AS /StateName`. Adobe Acrobat/Reader reads `/V` as authoritative and was ignoring `/AS`, causing all checkboxes to appear unchecked. Injects `/V` inline on the same widget entry to avoid corrupting xref byte offsets.
- **Nested field support** — Parser now walks `/Parent` reference chains and rebuilds full dotted field paths (e.g. `clasa_caen.0.3`). Previously only top-level fields were accessible; templates with deep hierarchies lost most of their fields.
- **PHP 8.0+ crash** — `create_function()` (removed in PHP 8.0) replaced with a proper closure in `_set_field_value()`.
- **`_bin2hex()` empty string fatal** — `do...while` unconditionally accessed `$str[0]`; replaced with `for` loop and early return on empty/null input.

### Changed
- Minimum PHP version raised to 7.4 (was 5.3)
- Removed legacy PHP 4 `var $` property declarations in favour of explicit `public`
- Removed example files, cache files, and FDF export utilities from the package

---

## [2.9.2] — codeshell/tmw fork baseline

See https://github.com/codeshell/fpdm for previous history.

### Added (by codeshell)
- Checkbox parser (`$pdf->useCheckboxParser = true`)
- Composer / Packagist availability
- PHP 7.x compatibility (`__construct`, buffer fixes)
- UTF-8 field name support

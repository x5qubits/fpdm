<?php
/**
 * Example 2: Checkboxes — Adobe Reader compatible
 *
 * FPDM-FQB writes both /AS (appearance state) AND /V (field value).
 * The original tmw/fpdm only wrote /AS, which worked in browsers and
 * Foxit but NOT in Adobe Acrobat/Reader. This fork fixes that.
 *
 * You do NOT need to know the internal checkbox state name (e.g. "Yes",
 * "On", "31.5"). The parser reads it from the template automatically.
 *
 * Truthy values  → checked   (true, 1, "yes", any non-empty string)
 * Falsy values   → unchecked (false, 0, "", null)
 */

require_once __DIR__ . '/../src/fpdm.php';

$fields = [
    // text fields
    'nume_firma'        => 'SC Example SRL',
    'cui'               => 'RO12345678',

    // checkboxes — just use true/false
    'tip_srl'           => true,   // checked
    'tip_pfa'           => false,  // unchecked
    'acord_gdpr'        => true,
    'newsletter'        => false,

    // checkbox with non-standard state name — still works
    'optiune_specifica' => true,
];

$pdf = new FPDM(__DIR__ . '/template.pdf');
$pdf->useCheckboxParser = true; // required for checkbox support
$pdf->Load($fields, true);
$pdf->Merge();
$pdf->Output('F', __DIR__ . '/output/cu-bifari.pdf');

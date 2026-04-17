<?php
/**
 * Example 3: Nested / hierarchical PDF fields
 *
 * Some PDF templates — especially official government forms — use nested
 * AcroForm field hierarchies where fields have /Parent references.
 * The field names look like: "sectiune.0.1", "clasa_caen.0.3", etc.
 *
 * The original tmw/fpdm only parsed top-level fields; with nested
 * templates it would see far fewer fields than actually exist.
 *
 * FPDM-FQB walks the /Parent chain and reconstructs full dotted paths,
 * giving access to ALL widget fields in the PDF.
 *
 * Example: a Romanian ONRC Declaratie Anexa 4 template has 195 fields
 * across 3 nesting levels. The original library found only 67.
 */

require_once __DIR__ . '/../src/fpdm.php';

// Build dotted-path field map dynamically
$caenCodes = ['6201', '6202', '6209', '6311', '6312'];
$fields    = [];

foreach ($caenCodes as $i => $code) {
    $fields["clasa_caen.0.$i"]      = $code;           // CAEN code
    $fields["clasa_caen_desc.0.$i"] = 'Descriere cod'; // CAEN description
}

// Top-level fields still work normally
$fields['TRIBUNALUL']  = 'BUCUREȘTI';
$fields['InmFirma']    = 'SC Example SRL';
$fields['DataCerere']  = '17.04.2026';

$pdf = new FPDM(__DIR__ . '/template_nested.pdf');
$pdf->useCheckboxParser = true;
$pdf->Load($fields, true);
$pdf->Merge();
$pdf->Output('F', __DIR__ . '/output/nested-fields.pdf');

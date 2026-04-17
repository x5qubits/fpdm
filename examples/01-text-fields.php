<?php
/**
 * Example 1: Fill text fields from a PHP array (UTF-8)
 *
 * This is the most common use case — populate a PDF form with data
 * from your database or user input.
 */

require_once __DIR__ . '/../src/fpdm.php';

$fields = [
    'nume'       => 'Popescu',
    'prenume'    => 'Ion',
    'cnp'        => '1850101123456',
    'adresa'     => 'Str. Victoriei nr. 10, București',
    'telefon'    => '0722 123 456',
    'data'       => '17.04.2026',
];

$pdf = new FPDM(__DIR__ . '/template.pdf');
$pdf->Load($fields, true); // true = values are UTF-8
$pdf->Merge();
$pdf->Output('F', __DIR__ . '/output/filled.pdf'); // F = save to file

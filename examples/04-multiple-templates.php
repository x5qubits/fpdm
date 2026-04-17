<?php
/**
 * Example 4: Try multiple template candidates (repair fallback)
 *
 * Some PDF templates contain incremental updates or object streams that
 * FPDM cannot process. A common pattern is to keep a repaired version
 * alongside the original and try the repaired one first.
 *
 * Repair tool: https://ilovepdf.com/repair-pdf
 * (removes incremental updates and object streams)
 */

require_once __DIR__ . '/../src/fpdm.php';

$fields = [
    'Nume'  => 'Ionescu',
    'Firma' => 'SC Test SRL',
    'Data'  => '17.04.2026',
];

$templates = [
    __DIR__ . '/template_repaired.pdf', // try repaired version first
    __DIR__ . '/template.pdf',          // fall back to original
];

$lastError = null;
$success   = false;

foreach ($templates as $tpl) {
    if (!file_exists($tpl)) continue;
    try {
        $pdf = new FPDM($tpl);
        $pdf->useCheckboxParser = true;
        $pdf->Load($fields, true);
        $pdf->Merge();
        $pdf->Output('F', __DIR__ . '/output/result.pdf');
        $success = true;
        break;
    } catch (Exception $e) {
        $lastError = $e->getMessage();
    }
}

if (!$success) {
    throw new RuntimeException('All templates failed. Last error: ' . $lastError);
}

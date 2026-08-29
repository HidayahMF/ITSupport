<?php
/**
 * Standalone test runner for the IT Support application.
 *
 * Focuses on the pure business rules (inventory status / quantity logic)
 * that drive the most complex module. No external dependencies are
 * required — run with:  php tests/run.php
 *
 * Note: this is intentionally NOT the CodeIgniter PHPUnit integration.
 * The models/controllers require live database connections, which are
 * not available in this repository, so the verifiable core logic is
 * black-box tested here via the Inventory_status library.
 */

// CodeIgniter guards its application files with a BASEPATH check.
// Define a throwaway value so the library can be loaded standalone.
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/../system/');
}

require_once __DIR__ . '/../application/libraries/Inventory_status.php';

$pass = 0;
$fail = 0;

function check($label, $actual, $expected)
{
    global $pass, $fail;
    if ($actual === $expected) {
        $pass++;
        echo "PASS  $label\n";
    } else {
        $fail++;
        echo "FAIL  $label\n";
        echo "  expected: " . var_export($expected, true) . "\n";
        echo "  actual:   " . var_export($actual, true) . "\n";
    }
}

echo "=== Inventory_status::effective_status ===\n";

check(
    'Explicit "Sudah Diserahkan ke User" flag wins even when qty untouched',
    Inventory_status::effective_status('Sudah Diserahkan ke User', 0, 0, 10),
    'Sudah Diserahkan ke User'
);

check(
    'No receipt yet -> Menunggu Barang',
    Inventory_status::effective_status('Menunggu Barang', 0, 0, 10),
    'Menunggu Barang'
);

check(
    'Null DB status, nothing received -> Menunggu Barang',
    Inventory_status::effective_status(null, 0, 0, 10),
    'Menunggu Barang'
);

check(
    'Partial receipt -> Menunggu Barang',
    Inventory_status::effective_status('', 5, 0, 10),
    'Menunggu Barang'
);

check(
    'Fully received, nothing handed over -> Stock IT',
    Inventory_status::effective_status('Sudah Diterima IT', 10, 0, 10),
    'Stock IT'
);

check(
    'Fully received, partially handed over -> Stock IT',
    Inventory_status::effective_status('Sudah Diterima IT', 10, 5, 10),
    'Stock IT'
);

check(
    'Fully received and fully handed over -> Sudah Diserahkan ke User',
    Inventory_status::effective_status('Sudah Diterima IT', 10, 10, 10),
    'Sudah Diserahkan ke User'
);

check(
    'qty == 0 never -> Stock IT (guards division-like branch)',
    Inventory_status::effective_status('Sudah Diterima IT', 0, 0, 0),
    'Menunggu Barang'
);

echo "\n=== Inventory_status::progress ===\n";

check('Progress 5/10 -> 50', Inventory_status::progress(5, 10), 50);
check('Progress 10/10 -> 100', Inventory_status::progress(10, 10), 100);
check('Progress 0/10 -> 0', Inventory_status::progress(0, 10), 0);
check('Progress x/0 -> 0 (no division by zero)', Inventory_status::progress(3, 0), 0);

echo "\n=== Inventory_status::sisa ===\n";

check('Sisa 10-3 -> 7', Inventory_status::sisa(10, 3), 7);
check('Sisa 10-20 -> 0 (clamped)', Inventory_status::sisa(10, 20), 0);
check('Sisa 0-0 -> 0', Inventory_status::sisa(0, 0), 0);

echo "\n=== Inventory_status::pr_group_status ===\n";

check('Single status passes through', Inventory_status::pr_group_status(array('Stock IT')), 'Stock IT');
check('All waiting -> Menunggu Barang', Inventory_status::pr_group_status(array('Menunggu Barang', 'Menunggu Barang')), 'Menunggu Barang');
check('Mixed waiting + stock -> Stock IT', Inventory_status::pr_group_status(array('Menunggu Barang', 'Stock IT')), 'Stock IT');
check('Any handed over in a mixed group -> Sebagian Diserahkan', Inventory_status::pr_group_status(array('Stock IT', 'Sudah Diserahkan ke User')), 'Sebagian Diserahkan');
check('All handed over -> Sudah Diserahkan ke User', Inventory_status::pr_group_status(array('Sudah Diserahkan ke User', 'Sudah Diserahkan ke User')), 'Sudah Diserahkan ke User');

echo "\n=== Inventory_status::receive_error (quantity-overrun guard) ===\n";

check('Receive 5 of 10, none yet -> allowed', Inventory_status::receive_error(10, 5, 0), '');
check('Receive 5 of 10, already 5 -> allowed', Inventory_status::receive_error(10, 5, 5), '');
check('Receive 6 of 10, already 5 -> rejected', Inventory_status::receive_error(10, 6, 5), 'Qty diterima (6) melebihi sisa yang belum diterima (5). Qty total: 10, sudah diterima: 5.');
check('Receive 5 of 10, already 10 -> rejected (over-receipt)', Inventory_status::receive_error(10, 5, 10), 'Qty diterima (5) melebihi sisa yang belum diterima (0). Qty total: 10, sudah diterima: 10.');

echo "\n=== Inventory_status::handover_error (cannot hand over more than received) ===\n";

check('Handover with nothing received -> rejected', Inventory_status::handover_error(0, 1, 0), 'Barang belum diterima dari vendor. Tidak bisa melakukan serah terima.');
check('Handover 2 of 7 received -> allowed', Inventory_status::handover_error(7, 2, 0), '');
check('Handover 6 of 7 (2 already out) -> rejected', Inventory_status::handover_error(7, 6, 2), 'Qty diserahkan (6) melebihi sisa yang belum diserahkan (5). Total diterima: 7, sudah diserahkan: 2.');
check('Handover up to the whole received amount -> allowed', Inventory_status::handover_error(7, 7, 0), '');

echo "\n====================\n";
echo "Passed: $pass\n";
echo "Failed: $fail\n";
exit($fail > 0 ? 1 : 0);
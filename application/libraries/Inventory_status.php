<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Inventory_status
 *
 * Pure, dependency-free business rules for the Inventory Barang module.
 * Isolating this logic from the controller makes the derived status,
 * progress, and remaining-quantity calculations unit-testable.
 *
 * All methods are static on purpose (no database / framework state).
 */
class Inventory_status
{
    /**
     * Derive the "effective" status of an inventory line item.
     *
     * Rules (kept identical to the original controller logic):
     *  - A record explicitly flagged "Sudah Diserahkan ke User" stays so.
     *  - Otherwise, when everything was received and every received unit
     *    has been handed over -> 'Sudah Diserahkan ke User'.
     *  - When everything was received but some units remain with IT -> 'Stock IT'.
     *  - In every other case (nothing / partial receipt) -> 'Menunggu Barang'.
     *
     * @param string|null $db_status  inventory_barang.status column value
     * @param int|float   $diterima   total quantity received so far
     * @param int|float   $diserahkan total quantity handed over so far
     * @param int|float   $qty        quantity ordered on the PR
     * @return string
     */
    public static function effective_status($db_status, $diterima, $diserahkan, $qty)
    {
        if (!empty($db_status) && $db_status === 'Sudah Diserahkan ke User') {
            return 'Sudah Diserahkan ke User';
        }

        $qty       = (int) $qty;
        $diterima  = (int) $diterima;
        $diserahkan = (int) $diserahkan;

        if ($diterima >= $qty && $qty > 0) {
            return ($diserahkan >= $diterima) ? 'Sudah Diserahkan ke User' : 'Stock IT';
        }

        return 'Menunggu Barang';
    }

    /**
     * Receiving progress percentage (0-100). 0 when qty is unknown/zero.
     *
     * @param int|float $diterima
     * @param int|float $qty
     * @return int
     */
    public static function progress($diterima, $qty)
    {
        $qty = (int) $qty;
        if ($qty <= 0) {
            return 0;
        }
        return (int) round(((int) $diterima / $qty) * 100);
    }

    /**
     * Remaining quantity not yet received. Never negative.
     *
     * @param int|float $qty
     * @param int|float $diterima
     * @return int
     */
    public static function sisa($qty, $diterima)
    {
        return max(0, (int) $qty - (int) $diterima);
    }

    /**
     * Collapse a group of per-line statuses into a single PR-level status.
     *
     * @param string[] $statuses effective statuses of the PR's line items
     * @return string
     */
    public static function pr_group_status(array $statuses)
    {
        $unique = array_unique($statuses);

        if (count($unique) === 1) {
            return $unique[0];
        }

        if (in_array('Sudah Diserahkan ke User', $statuses, true)) {
            return 'Sebagian Diserahkan';
        }

        if (in_array('Stock IT', $statuses, true)) {
            return 'Stock IT';
        }

        return 'Menunggu Barang';
    }

    /**
     * Validate a receiving line against the PR quantity.
     * Returns an empty string when allowed, otherwise an error message.
     *
     * @param int $qty_total         PR quantity
     * @param int $qty_next          quantity to add/keep for this line
     * @param int $already_received  quantity already received (excluding this line when editing)
     * @return string
     */
    public static function receive_error($qty_total, $qty_next, $already_received)
    {
        $remaining = $qty_total - $already_received;
        if ($qty_next > $remaining) {
            return 'Qty diterima (' . $qty_next . ') melebihi sisa yang belum diterima (' . $remaining . '). Qty total: ' . $qty_total . ', sudah diterima: ' . $already_received . '.';
        }
        return '';
    }

    /**
     * Validate a handover line against the received quantity.
     * Returns an empty string when allowed, otherwise an error message.
     *
     * @param int $total_received  quantity received on the inventory
     * @param int $qty_next        quantity to hand over for this line
     * @param int $already_handed  quantity previously handed over (excluding this line when editing)
     * @return string
     */
    public static function handover_error($total_received, $qty_next, $already_handed)
    {
        if ($total_received <= 0) {
            return 'Barang belum diterima dari vendor. Tidak bisa melakukan serah terima.';
        }

        $remaining = $total_received - $already_handed;
        if ($qty_next > $remaining) {
            return 'Qty diserahkan (' . $qty_next . ') melebihi sisa yang belum diserahkan (' . $remaining . '). Total diterima: ' . $total_received . ', sudah diserahkan: ' . $already_handed . '.';
        }
        return '';
    }
}
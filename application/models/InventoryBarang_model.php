<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InventoryBarang_model extends CI_Model
{

    private $table = 'inventory_barang';

    public function __construct()
    {
        parent::__construct();
        // load koneksi khusus sqlServer (database BMC)
        $this->db = $this->load->database('sqlServer', TRUE);
    }

    /**
     * Ambil semua data dengan filter & search
     *
     * Source:
     *  - PURC_PURCHREQUEST_TEMP (a)
     *  - inventory_barang (b) sebagai tracking/status
     *  - PURC_MATCATALOG (c) untuk nama material
     *  - PURC_BIDPR (d) untuk BidNo (jika diperlukan)
     */
    public function get_all($filter = array(), $search = '')
    {
        // NOTE: Query ini bertujuan menjaga output key tetap kompatibel dengan view
        // (index.php/detail.php) yang memakai: id, no_pr, no_mrp, nama_user, nama_barang,
        // qty, toko, tanggal_pr, tanggal_terima, tanggal_diserahkan, status, keterangan.

        $this->db->from('PURC_PURCHREQUEST_TEMP a');

        $this->db->join(
            $this->table . ' b',
            "a.PRNo = b.no_pr AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT",
            'left',
            FALSE
        );

        $this->db->join('PURC_MATCATALOG c', 'a.MaterialId = c.Materialid', 'left');

        $this->db->join('PURC_BIDPR d', 'a.PRNo = d.PRNo AND a.MaterialId = d.MaterialId', 'left');

        // Select kolom yang dibutuhkan view.
        $this->db->select([
            // Tracking id dari inventory_barang (NULL jika belum ada entry)
            'b.id AS id',

            // PR & MRP/Barang
            'a.PRNo AS no_pr',
            'a.Qty',
            'a.CDate',
            'b.no_mrp AS no_mrp',

            // Nama user & barang, qty, toko
            'b.nama_user AS nama_user',
            // nama_barang: ambil nilai terakhir yang tidak kosong dari string ';' (SQL Server 2022)
            "(SELECT TOP 1 sub.item_name FROM (SELECT TRIM(value) AS item_name, ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS rn FROM OPENJSON('[\"' + REPLACE(COALESCE(TRIM(COALESCE(b.nama_barang, c.MaterialName)), ''), ';', '\",\"') + '\"]') WHERE TRIM(value) <> '') sub ORDER BY sub.rn DESC) AS nama_barang",

            'b.qty AS qty',
            'b.toko AS toko',

            // Tanggal
            'b.tanggal_pr AS tanggal_pr',
            'b.tanggal_terima AS tanggal_terima',
            'b.tanggal_diserahkan AS tanggal_diserahkan',

            // Status & keterangan
            'b.status AS status',
            'b.keterangan AS keterangan',

            // tambahan opsional
            'd.BidNo AS bid_no'
        ]);

        // Dept constant
        $this->db->where('a.Dept', '0800');

        // Hanya tahun 2026
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);

        // Filter status (map display status ke DB status)
        if (!empty($filter['status'])) {
            if ($filter['status'] === 'Stock IT') {
                $this->db->where('b.status', 'Sudah Diterima IT');
            } elseif ($filter['status'] === 'Menunggu Barang') {
                $this->db->group_start();
                $this->db->where('b.status IS NULL', NULL, FALSE);
                $this->db->or_where('b.status', 'Menunggu Barang');
                $this->db->group_end();
            } else {
                $this->db->where('b.status', $filter['status']);
            }
        }

        // Filter nama_user dari serah terima (LEFT JOIN inventory_serah_terima)
        if (!empty($filter['nama_user'])) {
            $this->db->join('inventory_serah_terima st_filter', 'st_filter.inventory_id = b.id', 'left');
            $this->db->where('st_filter.nama_user', $filter['nama_user']);
        }

        // Filter no_pr
        if (!empty($filter['no_pr'])) {
            // gunakan a.PRNo biar cocok dengan sumber data
            $this->db->where('a.PRNo', $filter['no_pr']);
        }

        // Filter nama barang (search by MaterialName)
        if (!empty($filter['nama_barang'])) {
            $this->db->like('c.MaterialName', $filter['nama_barang']);
        }

        // Filter tanggal terima range
        if (!empty($filter['tanggal_awal'])) {
            $this->db->where('b.tanggal_terima >=', $filter['tanggal_awal']);
        }
        if (!empty($filter['tanggal_akhir'])) {
            $this->db->where('b.tanggal_terima <=', $filter['tanggal_akhir']);
        }

        // Search
        if ($search !== '') {
            $this->db->group_start();

            $this->db->like('a.PRNo', $search);
            $this->db->or_like('b.no_mrp', $search);
            $this->db->or_like('b.nama_user', $search);
            $this->db->or_like('b.nama_barang', $search);
            $this->db->or_like('c.MaterialName', $search);
            $this->db->or_like('b.toko', $search);

            $this->db->group_end();
        }

        // Pastikan urutannya stabil dan sesuai UI sebelumnya.
        // Jika b.id NULL (belum ada entry), urutan tetap kebagian belakangan.
        $this->db->order_by('b.id', 'DESC');

        return $this->db->get()->result_array();
    }


    /**
     * Ambil satu data berdasarkan ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row_array();
    }

    /**
     * Tambah data baru
     */
    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        log_message('error', 'InventoryBarang_model::insert payload: ' . print_r($data, true));

        $this->db->insert($this->table, $data);

        // Log hasil setelah insert
        $lastQuery = $this->db->last_query();
        $dbError = $this->db->error();
        $affected = $this->db->affected_rows();
        log_message('error', 'InventoryBarang_model::insert last_query: ' . $lastQuery);
        log_message('error', 'InventoryBarang_model::insert affected_rows: ' . print_r($affected, true));
        log_message('error', 'InventoryBarang_model::insert db_error: ' . print_r($dbError, true));

        // CI3 PDO driver SQLSRV sometimes doesn't support insert_id/SCOPE_IDENTITY reliably,
        // controller only needs success/fail.
        if ($affected && (int)$affected > 0) {
            return TRUE;
        }

        // If insert failed, log SQLSTATE / error number / message if available.
        // CI's $this->db->error() typically returns: ['code' => ..., 'message' => ...]
        if (is_array($dbError) && !empty($dbError)) {
            log_message('error', 'InventoryBarang_model::insert SQLSTATE/ErrorNumber/ErrorMessage: ' . print_r($dbError, true));
        }

        return FALSE;
    }


    /**
     * Update data
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Hapus data
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Dashboard summary cards
     * Query dari PURC_PURCHREQUEST_TEMP supaya konsisten dengan get_all()
     */
    public function get_summary()
    {
        $summary = array();

        // Total Barang: semua PR Dept 0800
        $this->db->from('PURC_PURCHREQUEST_TEMP a');
        $this->db->join($this->table . ' b', "a.PRNo = b.no_pr AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);
        $this->db->where('a.Dept', '0800');
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);
        $summary['total'] = $this->db->count_all_results();

        // Menunggu Barang: status NULL (belum ada record inventory) atau 'Menunggu Barang'
        $this->db->from('PURC_PURCHREQUEST_TEMP a');
        $this->db->join($this->table . ' b', "a.PRNo = b.no_pr AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);
        $this->db->where('a.Dept', '0800');
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);
        $this->db->group_start();
        $this->db->where('b.status IS NULL', NULL, FALSE);
        $this->db->or_where('b.status', 'Menunggu Barang');
        $this->db->group_end();
        $summary['menunggu'] = $this->db->count_all_results();

        // Sudah Diterima IT (Stock IT)
        $this->db->from('PURC_PURCHREQUEST_TEMP a');
        $this->db->join($this->table . ' b', "a.PRNo = b.no_pr AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);
        $this->db->where('a.Dept', '0800');
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);
        $this->db->where('b.status', 'Sudah Diterima IT');
        $summary['diterima'] = $this->db->count_all_results();

        // Sudah Diserahkan ke User
        $this->db->from('PURC_PURCHREQUEST_TEMP a');
        $this->db->join($this->table . ' b', "a.PRNo = b.no_pr AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);
        $this->db->where('a.Dept', '0800');
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);
        $this->db->where('b.status', 'Sudah Diserahkan ke User');
        $summary['diserahkan'] = $this->db->count_all_results();

        return $summary;
    }

    /**
     * Hitung lead time (Tanggal Terima - Tanggal PR)
     */
    public function hitung_lead_time($tanggal_pr, $tanggal_terima)
    {
        if (empty($tanggal_pr) || empty($tanggal_terima)) {
            return '-';
        }

        $tgl_pr = new DateTime($tanggal_pr);
        $tgl_terima = new DateTime($tanggal_terima);
        $selisih = $tgl_pr->diff($tgl_terima)->days;

        return $selisih . ' Hari';
    }

    /**
     * Ambil daftar nama user untuk filter dropdown
     */
    public function get_nama_user_list()
    {
        // SQL Server: DISTINCT harus ditulis sebagai keyword, bukan kolom.
        $this->db->distinct();
        $this->db->select('nama_user');
        $this->db->from($this->table);
        $this->db->order_by('nama_user', 'ASC');
        return $this->db->get()->result_array();
    }


    /**
     * Ambil daftar no_pr untuk filter dropdown
     */
    public function get_no_pr_list()
    {
        // SQL Server: DISTINCT harus ditulis sebagai keyword, bukan kolom.
        $this->db->distinct();
        $this->db->select('no_pr');
        $this->db->from($this->table);
        $this->db->where("RIGHT(RTRIM(LTRIM(no_pr)), 2) = '26'", NULL, FALSE);
        $this->db->order_by('no_pr', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Cari inventory_id berdasarkan no_pr + material_id.
     * Jika belum ada, buat record baru otomatis.
     * Return inventory_id (integer).
     */
    public function find_or_create_inventory($no_pr, $material_id, $qty, $nama_barang)
    {
        $escaped_material_id = $this->db->escape($material_id);
        $existing = $this->db
            ->where('no_pr', $no_pr)
            ->where("RTRIM(material_id) COLLATE DATABASE_DEFAULT = RTRIM({$escaped_material_id}) COLLATE DATABASE_DEFAULT", NULL, FALSE)
            ->get($this->table)
            ->row_array();

        if (!empty($existing)) {
            return (int) $existing['id'];
        }

        $now = date('Y-m-d H:i:s');
        $data_insert = array(
            'no_pr'        => $no_pr,
            'material_id'  => $material_id,
            'nama_barang'  => $nama_barang,
            'qty'          => $qty,
            'status'       => 'Menunggu Barang',
            'tanggal_pr'   => $now,
            'created_at'   => $now,
            'updated_at'   => $now,
        );

        $this->db->insert($this->table, $data_insert);
        return (int) $this->db->insert_id();
    }

    /**
     * Cari satu inventory_id berdasarkan no_pr + nama_barang (tanpa create)
     */
    public function get_inventory_id_by_pr($no_pr, $material_id)
    {
        $row = $this->db
            ->where('no_pr', $no_pr)
            ->where('nama_barang', $material_id)
            ->get($this->table)
            ->row_array();
        return $row ? (int) $row['id'] : null;
    }

    // =====================================================================
    // PENERIMAAN BARANG
    // =====================================================================

    private $table_penerimaan = 'inventory_penerimaan';

    /**
     * Ambil semua PR dari PURC_PURCHREQUEST_TEMP untuk dropdown pilih PR/barang
     * - inventory_id: dari inventory_barang (bisa NULL jika belum ada)
     * - no_pr, qty, nama_barang: dari PURC_PURCHREQUEST_TEMP + PURC_MATCATALOG
     */
    public function get_inventory_list_for_dropdown()
    {
        $this->db->from('PURC_PURCHREQUEST_TEMP a');
        $this->db->join('PURC_MATCATALOG c', 'a.MaterialId = c.Materialid', 'left');
        $this->db->join($this->table . ' b', "a.PRNo = b.no_pr AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);

        $this->db->select([
            'b.id AS inventory_id',
            'a.PRNo AS no_pr',
            'a.Qty AS qty',
            "(SELECT TOP 1 sub.item_name FROM (SELECT TRIM(value) AS item_name, ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS rn FROM OPENJSON('[\"' + REPLACE(COALESCE(TRIM(c.MaterialName), ''), ';', '\",\"') + '\"]') WHERE TRIM(value) <> '') sub ORDER BY sub.rn DESC) AS nama_barang",
            'a.MaterialId AS material_id',
        ]);

        $this->db->where('a.Dept', '0800');
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);
        $this->db->order_by('a.PRNo', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Dropdown khusus Serah Terima: hanya item dengan status 'Sudah Diterima IT'
     * yang masih punya sisa qty belum diserahkan.
     */
    public function get_inventory_list_for_serah_terima()
    {
        $this->db->select([
            'b.id AS inventory_id',
            'b.no_pr AS no_pr',
            'b.nama_barang AS nama_barang',
            'b.material_id AS material_id',
            'b.qty AS qty',
            'ISNULL(SUM(p.qty_diterima), 0) AS total_diterima',
            'ISNULL(s_diserahkan.total_diserahkan, 0) AS total_diserahkan',
        ]);

        $this->db->from($this->table . ' b');
        $this->db->join($this->table_penerimaan . ' p', 'p.inventory_id = b.id', 'left');
        $this->db->join(
            '(SELECT inventory_id, ISNULL(SUM(qty_diserahkan), 0) AS total_diserahkan FROM ' . $this->table_serah_terima . ' GROUP BY inventory_id) s_diserahkan',
            's_diserahkan.inventory_id = b.id',
            'left'
        );

        $this->db->where("b.status IS NOT NULL", NULL, FALSE);
        $this->db->where("RIGHT(RTRIM(LTRIM(b.no_pr)), 2) = '26'", NULL, FALSE);

        $this->db->group_by('b.id, b.no_pr, b.nama_barang, b.material_id, b.qty, s_diserahkan.total_diserahkan');

        $this->db->having('ISNULL(SUM(p.qty_diterima), 0) - ISNULL(s_diserahkan.total_diserahkan, 0) > 0');

        $this->db->order_by('b.no_pr', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Ambil qty dari PURC_PURCHREQUEST_TEMP berdasarkan no_pr + material_id
     */
    public function get_qty_from_pr($no_pr, $material_id)
    {
        $this->db->select('Qty');
        $this->db->from('PURC_PURCHREQUEST_TEMP');
        $this->db->where('PRNo', $no_pr);
        $this->db->where('MaterialId', $material_id);
        $this->db->where('Dept', '0800');
        $this->db->where("RIGHT(RTRIM(LTRIM(PRNo)), 2) = '26'", NULL, FALSE);
        $row = $this->db->get()->row_array();
        return $row ? (int) $row['Qty'] : 0;
    }

    /**
     * Ambil semua data penerimaan
     * - no_pr dari PURC_PURCHREQUEST_TEMP via LEFT JOIN
     * - nama_barang dari PURC_MATCATALOG.MaterialName via LEFT JOIN
     * - qty_diterima, keterangan dari inventory_penerimaan
     */
    public function get_all_penerimaan($filter = array())
    {
        $this->db->select([
            'p.id AS id',
            'p.inventory_id AS inventory_id',
            'a.PRNo AS no_pr',
            "(SELECT TOP 1 sub.item_name FROM (SELECT TRIM(value) AS item_name, ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS rn FROM OPENJSON('[\"' + REPLACE(COALESCE(TRIM(COALESCE(b.nama_barang, c.MaterialName)), ''), ';', '\",\"') + '\"]') WHERE TRIM(value) <> '') sub ORDER BY sub.rn DESC) AS nama_barang",
            'a.Qty AS qty_total',
            'p.tanggal_diterima AS tanggal_diterima',
            'p.qty_diterima AS qty_diterima',
            'p.keterangan AS keterangan',
            'p.created_at AS created_at',
            'p.updated_at AS updated_at',
        ]);

        $this->db->from($this->table_penerimaan . ' p');
        $this->db->join($this->table . ' b', 'p.inventory_id = b.id', 'left');
        $this->db->join('PURC_PURCHREQUEST_TEMP a', "b.no_pr = a.PRNo AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);
        $this->db->join('PURC_MATCATALOG c', 'a.MaterialId = c.Materialid', 'left');

        // Hanya tahun 2026
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);

        // Filter no_pr
        if (!empty($filter['no_pr'])) {
            $this->db->where('a.PRNo', $filter['no_pr']);
        }

        // Filter nama barang
        if (!empty($filter['nama_barang'])) {
            $this->db->like('c.MaterialName', $filter['nama_barang']);
        }

        // Filter tanggal diterima range
        if (!empty($filter['tanggal_awal'])) {
            $this->db->where('p.tanggal_diterima >=', $filter['tanggal_awal']);
        }
        if (!empty($filter['tanggal_akhir'])) {
            $this->db->where('p.tanggal_diterima <=', $filter['tanggal_akhir']);
        }

        $this->db->order_by('p.id', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Ambil satu baris penerimaan berdasarkan ID
     */
    public function get_penerimaan_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table_penerimaan)->row_array();
    }

    /**
     * Hitung total qty sudah diterima untuk satu inventory_id
     */
    public function get_total_diterima($inventory_id)
    {
        $this->db->select('ISNULL(SUM(qty_diterima), 0) AS total_diterima');
        $this->db->from($this->table_penerimaan);
        $this->db->where('inventory_id', $inventory_id);
        $row = $this->db->get()->row_array();
        return (int) (isset($row['total_diterima']) ? $row['total_diterima'] : 0);
    }

    /**
     * Insert penerimaan baru
     */
    public function insert_penerimaan($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_penerimaan, $data);

        $affected = $this->db->affected_rows();
        log_message('error', 'InventoryBarang_model::insert_penerimaan affected: ' . $affected);
        log_message('error', 'InventoryBarang_model::insert_penerimaan query: ' . $this->db->last_query());

        if ($affected && (int)$affected > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Update baris penerimaan
     */
    public function update_penerimaan($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table_penerimaan, $data);
    }

    /**
     * Hapus baris penerimaan
     */
    public function delete_penerimaan($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_penerimaan);
    }

    /**
     * Update status di inventory_barang
     */
    public function update_status($inventory_id, $status)
    {
        $this->db->where('id', $inventory_id);
        return $this->db->update($this->table, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // =====================================================================
    // SERAH TERIMA KE USER
    // =====================================================================

    private $table_serah_terima = 'inventory_serah_terima';

    /**
     * Ambil semua data serah terima
     */
    public function get_all_serah_terima($filter = array())
    {
        $this->db->select([
            's.id AS id',
            's.inventory_id AS inventory_id',
            'a.PRNo AS no_pr',
            "(SELECT TOP 1 sub.item_name FROM (SELECT TRIM(value) AS item_name, ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS rn FROM OPENJSON('[\"' + REPLACE(COALESCE(TRIM(COALESCE(b.nama_barang, c.MaterialName)), ''), ';', '\",\"') + '\"]') WHERE TRIM(value) <> '') sub ORDER BY sub.rn DESC) AS nama_barang",
            'a.Qty AS qty_total',
            's.nama_user AS nama_user',
            's.qty_diserahkan AS qty_diserahkan',
            's.tanggal_serah AS tanggal_serah',
            's.keterangan AS keterangan',
            's.created_at AS created_at',
        ]);

        $this->db->from($this->table_serah_terima . ' s');
        $this->db->join($this->table . ' b', 's.inventory_id = b.id', 'left');
        $this->db->join('PURC_PURCHREQUEST_TEMP a', "b.no_pr = a.PRNo AND RTRIM(a.MaterialId) COLLATE DATABASE_DEFAULT = RTRIM(b.material_id) COLLATE DATABASE_DEFAULT", 'left', FALSE);
        $this->db->join('PURC_MATCATALOG c', 'a.MaterialId = c.Materialid', 'left');

        // Hanya tahun 2026
        $this->db->where("RIGHT(RTRIM(LTRIM(a.PRNo)), 2) = '26'", NULL, FALSE);

        if (!empty($filter['no_pr'])) {
            $this->db->where('a.PRNo', $filter['no_pr']);
        }
        if (!empty($filter['nama_user'])) {
            $this->db->like('s.nama_user', $filter['nama_user']);
        }

        $this->db->order_by('s.id', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Ambil satu baris serah terima berdasarkan ID
     */
    public function get_serah_terima_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table_serah_terima)->row_array();
    }

    /**
     * Hitung total qty sudah diserahkan untuk satu inventory_id
     */
    public function get_total_diserahkan($inventory_id)
    {
        $this->db->select('ISNULL(SUM(qty_diserahkan), 0) AS total_diserahkan');
        $this->db->from($this->table_serah_terima);
        $this->db->where('inventory_id', $inventory_id);
        $row = $this->db->get()->row_array();
        return (int) (isset($row['total_diserahkan']) ? $row['total_diserahkan'] : 0);
    }

    /**
     * Insert serah terima baru
     */
    public function insert_serah_terima($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_serah_terima, $data);
        $affected = $this->db->affected_rows();
        if ($affected && (int)$affected > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Update baris serah terima
     */
    public function update_serah_terima($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_serah_terima, $data);
    }

    /**
     * Hapus baris serah terima
     */
    public function delete_serah_terima($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_serah_terima);
    }

    /**
     * Ambil daftar nama employee aktif dari hris_Employee untuk dropdown serah terima
     */
    public function get_employee_list()
    {
        $this->db->select([
            'Id_Employee',
            'RTRIM(Name) AS Name',
            'NIP',
            'RTRIM(DepartID) AS DepartID',
        ]);
        $this->db->from('hris_Employee');
        $this->db->where('is_Active', '1');
        $this->db->order_by('Name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Ambil daftar nama_user dari serah terima untuk filter dropdown
     */
    public function get_nama_user_serah_terima_list()
    {
        $this->db->distinct();
        $this->db->select('nama_user');
        $this->db->from($this->table_serah_terima);
        $this->db->where('nama_user IS NOT NULL', NULL, FALSE);
        $this->db->where('nama_user !=', '');
        $this->db->order_by('nama_user', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Ambil ringkasan serah terima untuk satu inventory_id
     */
    public function get_serah_terima_summary($inventory_id)
    {
        $this->db->select('nama_user, qty_diserahkan, tanggal_serah');
        $this->db->from($this->table_serah_terima);
        $this->db->where('inventory_id', $inventory_id);
        $this->db->order_by('tanggal_serah', 'ASC');
        return $this->db->get()->result_array();
    }

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InventoryBarang extends CI_Controller
{

  public function __construct()
{
    parent::__construct();
    $this->load->model('InventoryBarang_model');
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->library('form_validation');   // ← tambahkan baris ini
}

    /**
     * Halaman utama daftar inventory barang
     */
    public function index()
    {
        $data['title'] = 'Inventory Barang | IT Support';

        // Ambil filter dari GET
        $filter = array(
            'status'       => $this->input->get('status'),
            'nama_user'    => $this->input->get('nama_user'),
            'no_pr'        => $this->input->get('no_pr'),
            'nama_barang'  => $this->input->get('nama_barang'),
            'tanggal_awal' => $this->input->get('tanggal_awal'),
            'tanggal_akhir'=> $this->input->get('tanggal_akhir'),
        );

        $search = $this->input->get('search');

        $data['items'] = $this->InventoryBarang_model->get_all($filter, $search);
        $data['summary'] = $this->InventoryBarang_model->get_summary();
        $data['user_list'] = $this->InventoryBarang_model->get_nama_user_list();
        $data['pr_list'] = $this->InventoryBarang_model->get_no_pr_list();
        $data['inventory_list'] = $this->InventoryBarang_model->get_inventory_list_for_dropdown();
        $data['serah_terima_user_list'] = $this->InventoryBarang_model->get_nama_user_serah_terima_list();
        $data['employee_list'] = $this->InventoryBarang_model->get_employee_list();
        $data['filter'] = $filter;

        // Hitung computed fields untuk setiap item
        foreach ($data['items'] as &$item) {
            $item['lead_time'] = $this->InventoryBarang_model->hitung_lead_time(
                $item['tanggal_pr'],
                $item['tanggal_terima']
            );

            if (!empty($item['id'])) {
                $item['total_diterima'] = $this->InventoryBarang_model->get_total_diterima($item['id']);
                $item['total_diserahkan'] = $this->InventoryBarang_model->get_total_diserahkan($item['id']);
                $item['serah_terima_detail'] = $this->InventoryBarang_model->get_serah_terima_summary($item['id']);
            } else {
                $item['total_diterima'] = 0;
                $item['total_diserahkan'] = 0;
                $item['serah_terima_detail'] = array();
            }

            $qty = (int) $item['Qty'];
            $diterima = $item['total_diterima'];
            $diserahkan = $item['total_diserahkan'];

            if (!empty($item['status']) && $item['status'] === 'Sudah Diserahkan ke User') {
                $item['effective_status'] = 'Sudah Diserahkan ke User';
            } elseif ($diterima >= $qty && $qty > 0) {
                if ($diserahkan >= $diterima) {
                    $item['effective_status'] = 'Sudah Diserahkan ke User';
                } else {
                    $item['effective_status'] = 'Stock IT';
                }
            } else {
                $item['effective_status'] = 'Menunggu Barang';
            }

            $item['progress'] = $qty > 0 ? round(($diterima / $qty) * 100) : 0;
            $item['sisa'] = max(0, $qty - $diterima);
        }
        unset($item);

        // Group items by no_pr
        $grouped = array();
        foreach ($data['items'] as $item) {
            $pr_no = $item['no_pr'];
            if (!isset($grouped[$pr_no])) {
                $grouped[$pr_no] = array(
                    'no_pr'       => $pr_no,
                    'tanggal_pr'  => $item['CDate'],
                    'items'       => array(),
                    'total_qty'   => 0,
                    'total_diterima' => 0,
                    'total_diserahkan' => 0,
                    'statuses'    => array(),
                );
            }
            $grouped[$pr_no]['items'][] = $item;
            $grouped[$pr_no]['total_qty'] += (int) $item['Qty'];
            $grouped[$pr_no]['total_diterima'] += $item['total_diterima'];
            $grouped[$pr_no]['total_diserahkan'] += $item['total_diserahkan'];
            $grouped[$pr_no]['statuses'][] = $item['effective_status'];
        }

        foreach ($grouped as &$pr) {
            $unique = array_unique($pr['statuses']);
            if (count($unique) === 1) {
                $pr['status'] = $unique[0];
            } elseif (in_array('Sudah Diserahkan ke User', $pr['statuses'])) {
                $pr['status'] = 'Sebagian Diserahkan';
            } elseif (in_array('Stock IT', $pr['statuses'])) {
                $pr['status'] = 'Stock IT';
            } else {
                $pr['status'] = 'Menunggu Barang';
            }
            $pr['progress_pct'] = $pr['total_qty'] > 0 ? round(($pr['total_diterima'] / $pr['total_qty']) * 100) : 0;
        }
        unset($pr);

        $data['grouped'] = $grouped;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('inventory_barang/index', $data);
        $this->load->view('templates/footer');
    }

    // =====================================================================
    // PENERIMAAN BARANG
    // =====================================================================

    /**
     * JSON list data penerimaan (untuk tabel penerimaan di index)
     */
    public function penerimaan_list()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $filter = array(
                'no_pr'        => $this->input->get('no_pr'),
                'nama_barang'  => $this->input->get('nama_barang'),
                'tanggal_awal' => $this->input->get('tanggal_awal'),
                'tanggal_akhir'=> $this->input->get('tanggal_akhir'),
            );

            $rows = $this->InventoryBarang_model->get_all_penerimaan($filter);

            // Hitung progress untuk setiap baris
            foreach ($rows as &$row) {
                $total_diterima = $this->InventoryBarang_model->get_total_diterima($row['inventory_id']);
                $qty_total = (int) $row['qty_total'];
                $row['total_diterima'] = $total_diterima;
                $row['progress'] = $qty_total > 0 ? $total_diterima . ' / ' . $qty_total : '0 / 0';
                $row['progress_pct'] = $qty_total > 0 ? round(($total_diterima / $qty_total) * 100) : 0;
            }
            unset($row);

            echo json_encode(array('status' => 'success', 'data' => $rows));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang penerimaan_list error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data penerimaan.'));
        }
    }

    /**
     * JSON data inventory untuk dropdown pilih PR/barang
     */
    public function inventory_dropdown()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $list = $this->InventoryBarang_model->get_inventory_list_for_dropdown();
            echo json_encode(array('status' => 'success', 'data' => $list));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang inventory_dropdown error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data inventory.'));
        }
    }

    /**
     * JSON data inventory untuk dropdown Serah Terima (hanya Stock IT, sisa > 0)
     */
    public function inventory_dropdown_serah_terima()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $list = $this->InventoryBarang_model->get_inventory_list_for_serah_terima();
            echo json_encode(array('status' => 'success', 'data' => $list));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang inventory_dropdown_serah_terima error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data inventory untuk serah terima.'));
        }
    }

    /**
     * JSON data employee untuk dropdown Serah Terima
     */
    public function employee_dropdown()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $search = $this->input->get('search');
            $list = $this->InventoryBarang_model->get_employee_list();

            if (!empty($search)) {
                $search = strtolower($search);
                $list = array_filter($list, function($row) use ($search) {
                    return strpos(strtolower($row['Name']), $search) !== false
                        || strpos(strtolower($row['NIP']), $search) !== false;
                });
                $list = array_values($list);
            }

            echo json_encode(array('status' => 'success', 'data' => $list));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang employee_dropdown error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data employee.'));
        }
    }

    /**
     * JSON detail satu baris penerimaan (untuk form edit)
     */
    public function penerimaan_detail($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $item = $this->InventoryBarang_model->get_penerimaan_by_id($id);

            if (empty($item)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data penerimaan tidak ditemukan.'));
                return;
            }

            // Sertakan info inventory (qty total, no_pr, nama_barang)
            $inv = $this->InventoryBarang_model->get_by_id($item['inventory_id']);
            if ($inv) {
                $item['no_pr'] = $inv['no_pr'];
                $item['nama_barang'] = $inv['nama_barang'];
                $item['qty_total'] = $inv['qty'];
                $item['total_diterima'] = $this->InventoryBarang_model->get_total_diterima($item['inventory_id']);
            }

            echo json_encode(array('status' => 'success', 'data' => $item));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang penerimaan_detail error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data penerimaan.'));
        }
    }

    /**
     * Simpan penerimaan baru (POST)
     */
    public function simpan_penerimaan()
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->form_validation->set_rules('no_pr', 'No PR', 'required|trim');
        $this->form_validation->set_rules('material_id', 'Material ID', 'required|trim');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('qty_total', 'Qty Total', 'required|integer');
        $this->form_validation->set_rules('tanggal_diterima', 'Tanggal Diterima', 'required');
        $this->form_validation->set_rules('qty_diterima', 'Qty Diterima', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $error = strip_tags(validation_errors());
            echo json_encode(array('status' => 'error', 'message' => trim($error)));
            return;
        }

        $no_pr       = $this->input->post('no_pr');
        $material_id = $this->input->post('material_id');
        $nama_barang = $this->input->post('nama_barang');
        $qty_total   = (int) $this->input->post('qty_total');
        $qty_diterima = (int) $this->input->post('qty_diterima');
        $tanggal_diterima = $this->input->post('tanggal_diterima');
        $keterangan   = $this->input->post('keterangan');

        try {
            $inventory_id = $this->InventoryBarang_model->find_or_create_inventory(
                $no_pr, $material_id, $qty_total, $nama_barang
            );

            if (!$inventory_id) {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal membuat data inventory.'));
                return;
            }

            $total_sudah_diterima = $this->InventoryBarang_model->get_total_diterima($inventory_id);
            $sisa = $qty_total - $total_sudah_diterima;

            if ($qty_diterima > $sisa) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Qty diterima (' . $qty_diterima . ') melebihi sisa yang belum diterima (' . $sisa . '). Qty total: ' . $qty_total . ', sudah diterima: ' . $total_sudah_diterima . '.'
                ));
                return;
            }

            $data_insert = array(
                'inventory_id'     => $inventory_id,
                'tanggal_diterima' => $tanggal_diterima,
                'qty_diterima'     => $qty_diterima,
                'keterangan'       => $keterangan,
            );

            $insert = $this->InventoryBarang_model->insert_penerimaan($data_insert);

            if ($insert) {
                $new_total = $total_sudah_diterima + $qty_diterima;
                if ($new_total >= $qty_total) {
                    $this->InventoryBarang_model->update_status($inventory_id, 'Sudah Diterima IT');
                }

                echo json_encode(array('status' => 'success', 'message' => 'Penerimaan berhasil ditambahkan.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal menambahkan penerimaan.'));
            }
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang simpan_penerimaan error: ' . $e->getMessage());
            $msg = 'Gagal menyimpan penerimaan.';
            if (ENVIRONMENT !== 'production') {
                $msg .= ' ' . strip_tags($e->getMessage());
            }
            echo json_encode(array('status' => 'error', 'message' => $msg));
        }
    }

    /**
     * Update penerimaan (POST)
     */
    public function update_penerimaan($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->form_validation->set_rules('tanggal_diterima', 'Tanggal Diterima', 'required');
        $this->form_validation->set_rules('qty_diterima', 'Qty Diterima', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $error = strip_tags(validation_errors());
            echo json_encode(array('status' => 'error', 'message' => trim($error)));
            return;
        }

        $qty_diterima_baru = (int) $this->input->post('qty_diterima');
        $tanggal_diterima = $this->input->post('tanggal_diterima');
        $keterangan = $this->input->post('keterangan');

        try {
            // Ambil data penerimaan yang mau di-update
            $existing = $this->InventoryBarang_model->get_penerimaan_by_id($id);
            if (empty($existing)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data penerimaan tidak ditemukan.'));
                return;
            }

            $inventory_id = $existing['inventory_id'];
            $qty_lama = (int) $existing['qty_diterima'];

            // Ambil qty total dari PURC_PURCHREQUEST_TEMP via inventory_barang join
            $inv = $this->InventoryBarang_model->get_by_id($inventory_id);
            $qty_total = (int) $this->InventoryBarang_model->get_qty_from_pr($inv['no_pr'], $inv['nama_barang']);

            // Hitung total diterima TANPA baris ini, lalu tambah qty baru
            $total_sudah = $this->InventoryBarang_model->get_total_diterima($inventory_id);
            $total_lain = $total_sudah - $qty_lama;
            $total_setelah_update = $total_lain + $qty_diterima_baru;

            // Validasi: total setelah update tidak boleh melebihi qty total
            if ($total_setelah_update > $qty_total) {
                $sisa_max = $qty_total - $total_lain;
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Qty diterima (' . $qty_diterima_baru . ') melebihi sisa maksimal (' . $sisa_max . '). Qty total: ' . $qty_total . ', sudah diterima selain baris ini: ' . $total_lain . '.'
                ));
                return;
            }

            $data_update = array(
                'tanggal_diterima' => $tanggal_diterima,
                'qty_diterima'     => $qty_diterima_baru,
                'keterangan'       => $keterangan,
            );

            $update = $this->InventoryBarang_model->update_penerimaan($id, $data_update);

            if ($update) {
                // Cek apakah total sudah mencapai qty total → update status
                if ($total_setelah_update >= $qty_total) {
                    $this->InventoryBarang_model->update_status($inventory_id, 'Sudah Diterima IT');
                } else {
                    // Jika sebelumnya sudah "Sudah Diterima IT" tapi sekarang kurang, kembali ke "Menunggu Barang"
                    if ($inv['status'] === 'Sudah Diterima IT' && $total_setelah_update < $qty_total) {
                        $this->InventoryBarang_model->update_status($inventory_id, 'Menunggu Barang');
                    }
                }

                echo json_encode(array('status' => 'success', 'message' => 'Penerimaan berhasil diupdate.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal mengupdate penerimaan.'));
            }
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang update_penerimaan error: ' . $e->getMessage());
            $msg = 'Gagal mengupdate penerimaan.';
            if (ENVIRONMENT !== 'production') {
                $msg .= ' ' . strip_tags($e->getMessage());
            }
            echo json_encode(array('status' => 'error', 'message' => $msg));
        }
    }

    /**
     * Hapus penerimaan (POST)
     */
    public function hapus_penerimaan($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // Ambil data penerimaan sebelum dihapus
            $existing = $this->InventoryBarang_model->get_penerimaan_by_id($id);
            if (empty($existing)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data penerimaan tidak ditemukan.'));
                return;
            }

            $inventory_id = $existing['inventory_id'];

            $delete = $this->InventoryBarang_model->delete_penerimaan($id);

            if ($delete) {
                // Cek ulang total diterima → jika kurang dari qty total, kembalikan status
                $inv = $this->InventoryBarang_model->get_by_id($inventory_id);
                if ($inv) {
$qty_total = (int) $this->InventoryBarang_model->get_qty_from_pr($inv['no_pr'], $inv['material_id']);
                    $total_diterima = $this->InventoryBarang_model->get_total_diterima($inventory_id);

                    if ($total_diterima < $qty_total && $inv['status'] === 'Sudah Diterima IT') {
                        $this->InventoryBarang_model->update_status($inventory_id, 'Menunggu Barang');
                    }
                }

                echo json_encode(array('status' => 'success', 'message' => 'Penerimaan berhasil dihapus.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal menghapus penerimaan.'));
            }
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang hapus_penerimaan error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal menghapus penerimaan.'));
        }
    }

    // =====================================================================
    // SERAH TERIMA KE USER
    // =====================================================================

    /**
     * JSON list data serah terima
     */
    public function serah_terima_list()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $filter = array(
                'no_pr'      => $this->input->get('no_pr'),
                'nama_user'  => $this->input->get('nama_user'),
            );

            $rows = $this->InventoryBarang_model->get_all_serah_terima($filter);

            echo json_encode(array('status' => 'success', 'data' => $rows));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang serah_terima_list error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data serah terima.'));
        }
    }

    /**
     * JSON detail satu baris serah terima (untuk form edit)
     */
    public function serah_terima_detail($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $item = $this->InventoryBarang_model->get_serah_terima_by_id($id);

            if (empty($item)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data serah terima tidak ditemukan.'));
                return;
            }

            $inv = $this->InventoryBarang_model->get_by_id($item['inventory_id']);
            if ($inv) {
                $item['no_pr'] = $inv['no_pr'];
                $item['nama_barang'] = $inv['nama_barang'];
                $item['qty_total'] = $inv['qty'];
                $item['total_diterima'] = $this->InventoryBarang_model->get_total_diterima($item['inventory_id']);
                $item['total_diserahkan'] = $this->InventoryBarang_model->get_total_diserahkan($item['inventory_id']);
            }

            echo json_encode(array('status' => 'success', 'data' => $item));
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang serah_terima_detail error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengambil data serah terima.'));
        }
    }

    /**
     * Simpan serah terima baru (POST)
     */
    public function simpan_serah_terima()
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->form_validation->set_rules('inventory_id', 'Inventory', 'required|integer');
        $this->form_validation->set_rules('nama_user', 'Nama User', 'required|trim');
        $this->form_validation->set_rules('qty_diserahkan', 'Qty Diserahkan', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('tanggal_serah', 'Tanggal Serah', 'required');

        if ($this->form_validation->run() === FALSE) {
            $error = strip_tags(validation_errors());
            echo json_encode(array('status' => 'error', 'message' => trim($error)));
            return;
        }

        $inventory_id     = (int) $this->input->post('inventory_id');
        $nama_user        = $this->input->post('nama_user');
        $qty_diserahkan   = (int) $this->input->post('qty_diserahkan');
        $tanggal_serah    = $this->input->post('tanggal_serah');
        $keterangan       = $this->input->post('keterangan');

        try {
            $inv = $this->InventoryBarang_model->get_by_id($inventory_id);
            if (empty($inv)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data inventory tidak ditemukan.'));
                return;
            }

            $qty_total = (int) $this->InventoryBarang_model->get_qty_from_pr($inv['no_pr'], $inv['material_id']);
            $total_diterima = $this->InventoryBarang_model->get_total_diterima($inventory_id);
            $total_sudah_diserahkan = $this->InventoryBarang_model->get_total_diserahkan($inventory_id);
            $sisa_belum_diserahkan = $total_diterima - $total_sudah_diserahkan;

            if ($total_diterima <= 0) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Barang belum diterima dari vendor. Tidak bisa melakukan serah terima.'
                ));
                return;
            }

            if ($qty_diserahkan > $sisa_belum_diserahkan) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Qty diserahkan (' . $qty_diserahkan . ') melebihi sisa yang belum diserahkan (' . $sisa_belum_diserahkan . '). Total diterima: ' . $total_diterima . ', sudah diserahkan: ' . $total_sudah_diserahkan . '.'
                ));
                return;
            }

            $data_insert = array(
                'inventory_id'   => $inventory_id,
                'nama_user'      => $nama_user,
                'qty_diserahkan' => $qty_diserahkan,
                'tanggal_serah'  => $tanggal_serah,
                'keterangan'     => $keterangan,
            );

            $insert = $this->InventoryBarang_model->insert_serah_terima($data_insert);

            if ($insert) {
                $new_total_diserahkan = $total_sudah_diserahkan + $qty_diserahkan;

                if ($new_total_diserahkan >= $total_diterima) {
                    $this->InventoryBarang_model->update_status($inventory_id, 'Sudah Diserahkan ke User');
                }

                echo json_encode(array('status' => 'success', 'message' => 'Serah terima berhasil ditambahkan.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal menambahkan serah terima.'));
            }
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang simpan_serah_terima error: ' . $e->getMessage());
            $msg = 'Gagal menyimpan serah terima.';
            if (ENVIRONMENT !== 'production') {
                $msg .= ' ' . strip_tags($e->getMessage());
            }
            echo json_encode(array('status' => 'error', 'message' => $msg));
        }
    }

    /**
     * Update serah terima (POST)
     */
    public function update_serah_terima($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->form_validation->set_rules('nama_user', 'Nama User', 'required|trim');
        $this->form_validation->set_rules('qty_diserahkan', 'Qty Diserahkan', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('tanggal_serah', 'Tanggal Serah', 'required');

        if ($this->form_validation->run() === FALSE) {
            $error = strip_tags(validation_errors());
            echo json_encode(array('status' => 'error', 'message' => trim($error)));
            return;
        }

        $qty_diserahkan_baru = (int) $this->input->post('qty_diserahkan');
        $nama_user    = $this->input->post('nama_user');
        $tanggal_serah = $this->input->post('tanggal_serah');
        $keterangan   = $this->input->post('keterangan');

        try {
            $existing = $this->InventoryBarang_model->get_serah_terima_by_id($id);
            if (empty($existing)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data serah terima tidak ditemukan.'));
                return;
            }

            $inventory_id = $existing['inventory_id'];
            $qty_lama = (int) $existing['qty_diserahkan'];

            $inv = $this->InventoryBarang_model->get_by_id($inventory_id);
            $total_diterima = $this->InventoryBarang_model->get_total_diterima($inventory_id);
            $total_sudah = $this->InventoryBarang_model->get_total_diserahkan($inventory_id);
            $total_lain = $total_sudah - $qty_lama;
            $total_setelah_update = $total_lain + $qty_diserahkan_baru;

            if ($total_setelah_update > $total_diterima) {
                $sisa_max = $total_diterima - $total_lain;
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Qty diserahkan (' . $qty_diserahkan_baru . ') melebihi sisa maksimal (' . $sisa_max . '). Total diterima: ' . $total_diterima . ', sudah diserahkan selain baris ini: ' . $total_lain . '.'
                ));
                return;
            }

            $data_update = array(
                'nama_user'      => $nama_user,
                'qty_diserahkan' => $qty_diserahkan_baru,
                'tanggal_serah'  => $tanggal_serah,
                'keterangan'     => $keterangan,
            );

            $update = $this->InventoryBarang_model->update_serah_terima($id, $data_update);

            if ($update) {
                if ($total_setelah_update >= $total_diterima) {
                    $this->InventoryBarang_model->update_status($inventory_id, 'Sudah Diserahkan ke User');
                } else {
                    if ($inv['status'] === 'Sudah Diserahkan ke User' && $total_setelah_update < $total_diterima) {
                        $this->InventoryBarang_model->update_status($inventory_id, 'Sudah Diterima IT');
                    }
                }

                echo json_encode(array('status' => 'success', 'message' => 'Serah terima berhasil diupdate.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal mengupdate serah terima.'));
            }
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang update_serah_terima error: ' . $e->getMessage());
            $msg = 'Gagal mengupdate serah terima.';
            if (ENVIRONMENT !== 'production') {
                $msg .= ' ' . strip_tags($e->getMessage());
            }
            echo json_encode(array('status' => 'error', 'message' => $msg));
        }
    }

    /**
     * Hapus serah terima (POST)
     */
    public function hapus_serah_terima($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $existing = $this->InventoryBarang_model->get_serah_terima_by_id($id);
            if (empty($existing)) {
                echo json_encode(array('status' => 'error', 'message' => 'Data serah terima tidak ditemukan.'));
                return;
            }

            $inventory_id = $existing['inventory_id'];

            $delete = $this->InventoryBarang_model->delete_serah_terima($id);

            if ($delete) {
                $inv = $this->InventoryBarang_model->get_by_id($inventory_id);
                if ($inv) {
                    $total_diterima = $this->InventoryBarang_model->get_total_diterima($inventory_id);
                    $total_diserahkan = $this->InventoryBarang_model->get_total_diserahkan($inventory_id);

                    if ($total_diserahkan < $total_diterima && $inv['status'] === 'Sudah Diserahkan ke User') {
                        $this->InventoryBarang_model->update_status($inventory_id, 'Sudah Diterima IT');
                    }
                }

                echo json_encode(array('status' => 'success', 'message' => 'Serah terima berhasil dihapus.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal menghapus serah terima.'));
            }
        } catch (Exception $e) {
            log_message('error', 'InventoryBarang hapus_serah_terima error: ' . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Gagal menghapus serah terima.'));
        }
    }

}

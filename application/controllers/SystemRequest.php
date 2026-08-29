<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SystemRequest extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sqlServer = $this->load->database('sqlServer', true);
        $this->load->database();
        $this->load->helper(array('url', 'form'));

        // Load optional application settings (gemini key, etc.).
        // The real file application/config/itsupport.php is gitignored.
        if (file_exists(APPPATH . 'config/itsupport.php')) {
            $this->config->load('itsupport');
        }
    }

    /**
     * Resolve the Google Gemini API key.
     * Priority: GEMINI_API_KEY env var -> application/config/itsupport.php.
     * Returns '' when not configured (callers must degrade gracefully).
     */
    private function _gemini_api_key()
    {
        $key = getenv('GEMINI_API_KEY');
        if (empty($key)) {
            $key = $this->config->item('gemini_api_key');
        }
        return is_string($key) ? trim($key) : '';
    }

    /**
     * Resolve the Google Gemini model name.
     */
    private function _gemini_model()
    {
        $model = getenv('GEMINI_MODEL');
        if (empty($model)) {
            $model = $this->config->item('gemini_model');
        }
        return is_string($model) && $model !== '' ? $model : 'gemini-flash-lite-latest';
    }

    public function index()
    {
        $this->load->view('SystemRequest/form_request_view');
    }

    // Endpoint AJAX untuk Select2
    public function get_employees()
    {
        $search = $this->input->get('searchTerm');

        // Implementasi Query
        $this->sqlServer->select('a.NIP as id, a.Name as text, b.NamaDepartemen as dept');
        $this->sqlServer->from('hris_Employee a');
        $this->sqlServer->join('MASCOSTCENTER b', 'a.DepartID = b.DepartID', 'left');

        // Filter pencarian Select2
        if (!empty($search)) {
            $this->sqlServer->group_start();
            $this->sqlServer->like('a.Name', $search);
            $this->sqlServer->or_like('a.NIP', $search);
            $this->sqlServer->group_end();
        }

        // Batasi hasil agar tidak berat
        $this->sqlServer->limit(10);
        $query = $this->sqlServer->get();

        echo json_encode($query->result_array());
    }

    public function submit()
    {
        $data = array(
            'nama_peminta'          => $this->input->post('nama_peminta'),
            'nip_peminta'           => $this->input->post('nip'),
            'departemen_peminta'    => $this->input->post('departemen_peminta'),
            'kontak_peminta'        => $this->input->post('kontak_peminta'),
            'masalah'               => $this->input->post('masalah'),
            'solusi'                => $this->input->post('solusi'),
            'kondisi_before'        => $this->input->post('kondisi_before'),
            'kondisi_after'         => $this->input->post('kondisi_after'),
            'preferensi_ui'         => $this->input->post('preferensi_ui'),
            'kebutuhan_data_output' => $this->input->post('kebutuhan_data_output')
        );

        $insert = $this->sqlServer->insert('IT_SYSTEM_REQUEST', $data);

        if ($insert) {
            $this->session->set_flashdata('status_swal', 'success');
            $this->session->set_flashdata('pesan_swal', 'Permintaan berhasil disubmit.');
        } else {
            $this->session->set_flashdata('status_swal', 'error');
            $this->session->set_flashdata('pesan_swal', 'Gagal menyimpan data ke SQL Server.');
        }

        $this->load->library('user_agent');
        if ($this->agent->is_referral()) {
            redirect($this->agent->referrer());
        } else {
            redirect('systemrequest');
        }
    }

    public function cek_model()
    {
        $api_key = $this->_gemini_api_key();

        if ($api_key === '') {
            echo '<pre>GEMINI_API_KEY belum dikonfigurasi. Lihat application/config/itsupport.example.php.</pre>';
            return;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($api_key);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo "<pre>";
        print_r(json_decode($response, true));
        echo "</pre>";
    }
    // Endpoint AJAX untuk Tanya AI
    // Endpoint AJAX untuk Tanya AI (Real API Connection)
    public function ask_ai()
    {
        $masalah = $this->input->post('masalah');

        if (empty(trim($masalah))) {
            echo json_encode(['status' => 'error', 'message' => 'Harap tulis masalahnya terlebih dahulu.']);
            return;
        }

        $api_key = $this->_gemini_api_key();

        if ($api_key === '') {
            echo json_encode(['status' => 'error', 'message' => 'Fitur AI belum dikonfigurasi.']);
            return;
        }

        $model = $this->_gemini_model();
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . urlencode($model) . ':generateContent?key=' . urlencode($api_key);

        $prompt = "Sebagai System Analyst, berikan saran solusi sistem IT dan data output untuk masalah berikut: " . $masalah . ". Jawab dengan sangat singkat, to the point, dan tidak bertele-tele. DILARANG keras menggunakan format markdown (seperti simbol bintang ** atau pagar ###). Gunakan teks biasa saja dengan penomoran angka standar.";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpcode == 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $ai_text = $result['candidates'][0]['content']['parts'][0]['text'];
            echo json_encode(['status' => 'success', 'data' => $ai_text]);
        } else {
            $error_msg = isset($result['error']['message']) ? $result['error']['message'] : 'Terjadi kesalahan pada API.';
            echo json_encode(['status' => 'error', 'message' => "HTTP $httpcode: $error_msg"]);
        }
    }

    // Menampilkan halaman Report
    public function report()
    {
        $data['total']   = $this->sqlServer->count_all('IT_SYSTEM_REQUEST');
        $data['pending'] = $this->sqlServer->where('status', 'Pending')->count_all_results('IT_SYSTEM_REQUEST');
        $data['proses']  = $this->sqlServer->where('status', 'Proses')->count_all_results('IT_SYSTEM_REQUEST');
        $data['selesai'] = $this->sqlServer->where('status', 'Selesai')->count_all_results('IT_SYSTEM_REQUEST');

        $this->sqlServer->select('departemen_peminta, COUNT(id) as jumlah');
        $this->sqlServer->group_by('departemen_peminta');
        $data['dept_stats'] = $this->sqlServer->get('IT_SYSTEM_REQUEST')->result();

        $this->sqlServer->order_by('tanggal_permintaan', 'DESC');
        $data['requests'] = $this->sqlServer->get('IT_SYSTEM_REQUEST')->result();

        // Programmer Performance

        $it_developers = array('5614', '0362', '0202', '0377','3490');

        $this->sqlServer->select('t.ditangani_oleh, e.Name as nama_programmer, 
            COUNT(t.id) as total_assigned,
            SUM(CASE WHEN t.status = \'Selesai\' THEN 1 ELSE 0 END) as total_selesai,
            SUM(CASE WHEN t.status = \'Proses\' THEN 1 ELSE 0 END) as total_proses,
            SUM(CASE WHEN t.status = \'Pending\' THEN 1 ELSE 0 END) as total_pending');
        $this->sqlServer->from('IT_SYSTEM_REQUEST t');
        $this->sqlServer->join('hris_Employee e', 'e.NIP = t.ditangani_oleh', 'left');
        $this->sqlServer->where_in('t.ditangani_oleh', $it_developers);
        $this->sqlServer->group_by('t.ditangani_oleh, e.Name');
        $data['programmer_stats'] = $this->sqlServer->get()->result();

        // Monthly trend (last 6 months)
        $this->sqlServer->select('FORMAT(tanggal_permintaan, \'yyyy-MM\') as bulan, COUNT(id) as jumlah');
        $this->sqlServer->where('tanggal_permintaan >= DATEADD(MONTH, -6, GETDATE())');
        $this->sqlServer->group_by('FORMAT(tanggal_permintaan, \'yyyy-MM\')');
        $this->sqlServer->order_by('bulan', 'ASC');
        $data['monthly_trend'] = $this->sqlServer->get('IT_SYSTEM_REQUEST')->result();

        $this->load->view('SystemRequest/report_request_view', $data);
    }


    public function update_status($id, $status)
    {
        $data = array(
            'status' => $status
        );

        if ($status == 'Proses' && $this->input->get('it_id')) {
            $data['ditangani_oleh'] = $this->input->get('it_id');
            $data['tanggal_diproses'] = date('Y-m-d H:i:s');
        }
        if ($status == 'Selesai') {
            $data['tanggal_selesai'] = date('Y-m-d H:i:s');
        }

        $this->sqlServer->where('id', $id);
        $update = $this->sqlServer->update('IT_SYSTEM_REQUEST', $data);

        if ($update) {
            $this->session->set_flashdata('status_swal', 'success');
            $this->session->set_flashdata('pesan_swal', 'Status tiket berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('status_swal', 'error');
            $this->session->set_flashdata('pesan_swal', 'Gagal memperbarui status tiket.');
        }

        $this->load->library('user_agent');
        if ($this->agent->is_referral()) {
            redirect($this->agent->referrer());
        } else {
            redirect('systemrequest/report');
        }
    }

    public function cetak_laporan($id)
    {
        $this->sqlServer->select('IT_SYSTEM_REQUEST.*, hris_Employee.Name');
        $this->sqlServer->from('IT_SYSTEM_REQUEST');

        $this->sqlServer->join('hris_Employee', 'hris_Employee.NIP = IT_SYSTEM_REQUEST.ditangani_oleh', 'left');

        $this->sqlServer->where('IT_SYSTEM_REQUEST.id', $id);
        $data['request'] = $this->sqlServer->get()->row();

        if (!$data['request']) {
            show_404();
        }

        $this->load->view('SystemRequest/v_cetak_laporan', $data);
    }

    public function get_employeesItSupport()
    {
        $searchTerm = $this->input->get('searchTerm');

        $it_developers = array('5614', '0362', '0202', '0377');

        $this->sqlServer->select('NIP as id, Name as text, DepartID as dept');
        $this->sqlServer->from('hris_Employee');

        $this->sqlServer->where('DepartID', '0800');
        $this->sqlServer->where('is_Active', 1);

        $this->sqlServer->where_in('NIP', $it_developers);

        if (!empty($searchTerm)) {
            $this->sqlServer->like('nama_karyawan', $searchTerm);
        }

        $query = $this->sqlServer->get();
        $results = $query->result_array();

        echo json_encode($results);
    }

    public function get_ProgrammerKerja()
    {
        $it_id = $this->input->get('ditangani_oleh');

        // Jalankan query Anda: SELECT * FROM IT_SYSTEM_REQUEST WHERE ditangani_oleh = $it_id AND status = 'Proses'
        $is_busy = $this->sqlServer->get_where('IT_SYSTEM_REQUEST', ['ditangani_oleh' => $it_id, 'status' => 'Proses'])->num_rows();
        // echo $this->sqlServer->last_query();

        echo json_encode([
            'sedang_kerja' => ($is_busy > 0) ? true : false
        ]);
    }
}

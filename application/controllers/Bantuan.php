<?php
class Bantuan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_dashboard");
    }

    public function index()
    {
        $data['title'] = "Form IT Support";
        $data['karyawan'] = $this->M_dashboard->getKaryawan();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('bantuan/form', $data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $nonik = $this->input->post("NONIK");
        $foto = $this->input->post('foto');
        $fotoName = null;

        // ====== FILE ======
        $lampiranName = null;

        if (!empty($_FILES['lampiran']['name'])) {

            $path = FCPATH . "uploads/it_support/file/";
            if (!is_dir($path)) mkdir($path, 0777, true);

            // biar aman terhadap spasi & karakter aneh
            $safeName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $_FILES['lampiran']['name']);

            $lampiranName = "file_" . time() . "_" . $safeName;

            move_uploaded_file($_FILES['lampiran']['tmp_name'], $path . $lampiranName);
        }


        // ====== FOTO ======
        if ($foto) {
            $path = FCPATH . "uploads/it_support/img/";
            if (!is_dir($path)) mkdir($path, 0777, true);

            $fotoName = "foto_" . time() . ".jpg";
            $data = explode(",", $foto);
            file_put_contents($path . $fotoName, base64_decode($data[1]));
        }

        // Ambil dept
        $emp = $this->db->get_where("KARYAWAN", ["NONIK" => $nonik])->row();
        $dept = $emp ? $emp->KODEF : '-';

        $nowDate = date('Y-m-d');
        $nowTime = date('H:i:s');

        $sql = "INSERT INTO IT.rpt_it (NONIK, KODEF, report, lokasi, gambar, tgl, time, gambar3)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            $nonik,
            $dept,
            $this->input->post('report'),
            $this->input->post('lokasi'),
            $fotoName,
            $nowDate,
            $nowTime,
            $lampiranName
        ]);

        $nama   = $emp ? $emp->NM_KAR : 'Karyawan';
        $report = $this->input->post('report');
        $lokasi = $this->input->post('lokasi');


        // ===== Ambil ID terakhir =====
        $last = $this->db->query("SELECT id FROM IT.rpt_it ORDER BY id DESC LIMIT 1")->row();
        $id = $last ? $last->id : 0;

        // ===== Generate LINK PENYELESAIAN =====
        $link = base_url("bantuan/selesai?id=" . $id);

        // ===== Format Pesan WA =====
        $pesanWA = "*Dear IT Team*\n\n" .
            "Mohon bantuannya terdapat kendala sebagai berikut:\n" .
            "$report\n\n" .
            "*Klik link berikut jika sudah menyelesaikan pekerjaan:*\n" .
            "$link\n\n" .
            "Nama Pelapor: *$nama*";

        // ===== List Nomor Tujuan =====
        $nohp = [
            '085782075367',
            '082125630770',
            '082220776782',
            '08111789823',
            '081977785738'
        ];

        // ===== API WA =====
        $url = "http://10.19.25.70:3000/api/send";

        // ===== Simpan hasil =====
        $results = [];

        // ===== Loop Kirim WA =====
        foreach ($nohp as $hp) {

            // Bersihkan nomor
            $phone = preg_replace('/[^0-9]/', '', $hp);

            // Ubah 08 jadi 628
            if (substr($phone, 0, 1) == '0') {
                $phone = '62' . substr($phone, 1);
            }

            // ===== Payload =====
            $payload = [
                'no'   => $phone,
                'text' => $pesanWA
            ];

            // ===== CURL =====
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);

            // ===== Error CURL =====
            if (curl_errno($ch)) {

                $results[] = [
                    'phone' => $phone,
                    'status' => 'ERROR',
                    'message' => curl_error($ch)
                ];

                curl_close($ch);

                continue;
            }

            // ===== HTTP CODE =====
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            // ===== Success / Failed =====
            if ($httpcode == 200) {

                $results[] = [
                    'phone' => $phone,
                    'status' => 'OK',
                    'response' => json_decode($response, true)
                ];
            } else {

                $results[] = [
                    'phone' => $phone,
                    'status' => 'FAILED',
                    'http_code' => $httpcode,
                    'response' => $response
                ];
            }
        }

        // ===== Final Output =====
        echo json_encode([
            "status" => "DONE",
            "results" => $results
        ]);
    }

    public function selesai()
    {
        $data['title'] = "Ticket Penyelesaian Pekerjaan";
        $data['pic'] = $this->db
            ->where('KODEF', 11)
            ->where('KELUAR', 0)
            ->get('KARYAWAN')
            ->result_array();

        $id = $this->input->get('id');

        if (!$id) show_404();

        $data['tiket'] = $this->db->get_where('IT.rpt_it', ['id' => $id])->row();

        if (!$data['tiket']) {
            echo "Data tidak ditemukan";
            return;
        }

        // $this->load->view('templates/header', $data);
        $this->load->view('bantuan/selesai', $data);
    }


    public function proses_selesai()
    {
        $id = $this->input->post('id');
        $nonik  = $this->input->post('NONIK');
        $tipe   = $this->input->post('Tipe');
        $report = $this->input->post('report');

        $this->db->where('id', $id)->update('IT.rpt_it', [
            'gambar2'  => $nonik,
            'device'   => $tipe,
            'report2'  => $report,
            'tgl2'     => date('Y-m-d'),
            'time2'    => date('H:i:s'),
            'dt'       => 1
        ]);



        echo json_encode(["status" => "OK"]);
    }
    
}
